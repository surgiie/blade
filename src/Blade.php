<?php

namespace Surgiie\Blade;

use SplFileInfo;
use Surgiie\Blade\FileFactory;
use Surgiie\Blade\FileCompiler;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\EngineResolver;
use Surgiie\Blade\Exceptions\FileNotFoundException;
use Surgiie\Blade\Exceptions\UndefinedVariableException;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Contracts\Container\Container as ContainerContract;

class Blade
{
    public const ENGINE_NAME = 'blade';

    protected ?FileFactory $factory = null;
    protected ?FileCompiler $compiler = null;
    protected ?FileFinder $fileFinder = null;
    protected static ?string $cachePath = null;
    protected static array $components = [];
    protected ?FileCompilerEngine $engine = null;
    protected ?EngineResolver $engineResolver = null;

    public function __construct(?ContainerContract $container = null)
    {
        $this->container = (Container::getInstance() ?:$container) ?: new Container();

        $this->container->singleton(ViewFactoryContract::class, fn () => $this->factory());

        $this->container->singleton('view', fn () => $this->factory());
        $this->container->singleton(ViewFinderInterface::class, fn () => $this->finder());

        $this->engineResolver()->register(self::ENGINE_NAME, fn () => $this->engine());

        if(is_null(static::$cachePath)){
            static::setCachePath(__DIR__.'/../.cache');
        }
    }

    public static function components(array $components): void
    {
        static::$components = array_merge(static::$components, $components);
    }

    public static function getComponents(): array
    {
        return static::$components;
    }

    public static function forgetComponents(): void
    {
        static::$components = [];
    }

    public static function setCachePath(string $path)
    {
        static::$cachePath = $path;
    }

    public function __call(string $name, array $arguments)
    {
        $compiler = $this->compiler();

        if (method_exists($compiler, $name)) {
            return $compiler->{$name}(...$arguments);
        }

        return $this->factory->{$name}(...$arguments);
    }

    public function finder(): FileFinder
    {
        return $this->fileFinder ??= new FileFinder(new Filesystem, []);
    }

    protected function engineResolver(): EngineResolver
    {
        if (! is_null($this->engineResolver)) {
            return $this->engineResolver;
        }

        return $this->engineResolver = new EngineResolver();
    }

    protected function factory(): FileFactory
    {
        return $this->factory ??= new FileFactory(
            $this->engineResolver(),
            $this->finder(),
            new Dispatcher($this->container)
        );
    }

    public static function getCachePath(): ?string
    {
        return static::$cachePath;
    }

    protected function engine(): FileCompilerEngine
    {
        return $this->engine ??= new FileCompilerEngine($this->compiler());
    }

    protected function compiler(): FileCompiler
    {
        return  $this->compiler ??= new FileCompiler(new Filesystem, static::getCachePath());
    }

    public function render(string $path, array $vars = []): string
    {
        if (! is_file($path)) {
            throw new FileNotFoundException("The $path file does not exist.");
        }

        $info = new SplFileInfo($path);

        $finder = $this->finder();
        // // replace the namespace for components to the compiled path so the file finder can find them.
        $finder->replaceNamespace('__components', static::getCachePath());

        // ensure we're in a clean state before rendering so we can render files on the fly without conflicts.
        $finder->flush();

        $factory = $this->factory();

        // dont use realpath on phar file paths as it will always be false, since phar files are virtual.
        $directory = str_starts_with($path, 'phar://') ? dirname($path) : dirname($info->getRealPath());


        // tell the finder about the directory this file is in and it's file extension.
        $finder->setPaths([$directory]);
        $factory->addExtension($info->getExtension(), self::ENGINE_NAME);

        // Set an error handler that throws an exception on undefined variables instead of a warning.
        set_error_handler(function ($severity, $message, $file, $line) {
            if ($severity != E_WARNING) {
                return;
            }
            preg_match('/Undefined variable \$(.*)/', $message, $match);

            if ($match) {
                throw new UndefinedVariableException(
                    "Undefined variable \$$match[1] on line $line.",
                    $match[1]
                );
            }
        });

        // render and return the contents.
        try {
            $contents = $factory->make($info->getFilename(), $vars)->render();
        } catch(\Exception $e){
            throw $e;
        } finally {
            restore_error_handler();
        }

        // if (! $cache) {
            //     $engine = $file->getEngine();

            //     $engine->forgetCompiledOrNotExpired();

        //     unlink($engine->getCompiler()->getCompiledPath($path));

        //     if ($this->filesystem->isEmptyDirectory($compilePath = $this->getCompiledPath())) {
        //         $this->filesystem->deleteDirectory($compilePath);
        //     }
        // }
        return $contents;
    }

    public static function deleteCacheDirectory(): bool
    {
        return (new Filesystem)->deleteDirectory(static::getCachePath());
    }
}
