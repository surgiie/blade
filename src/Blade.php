<?php

namespace Surgiie\Blade;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\EngineResolver;
use SplFileInfo;
use Surgiie\Blade\Exceptions\FileException;

class Blade
{
    /**
     * The name of the engine for the engine resolver.
     */
    public const ENGINE_NAME = 'blade';

    /**
     * The set container instance.
     */
    protected ContainerContract $container;

    /**
     * The file factory that makes the \Surgiie\Blade\File instances.
     */
    protected ?FileFactory $factory = null;

    /**
     * The compiler instance used to compile blade directives.
     */
    protected ?FileCompiler $compiler = null;

    /**
     * The file finder that resolves file paths.
     */
    protected ?FileFinder $fileFinder = null;

    /**
     * The path used to put cached compiled files in.
     */
    protected static ?string $cachePath = null;

    /**
     * The array of blade component classes to use when compiling components.
     */
    protected static array $components = [];

    /**
     * The engine used for the file compiler.
     */
    protected ?FileCompilerEngine $engine = null;

    /**
     * The resolver used to resolve the engine.
     */
    protected ?EngineResolver $engineResolver = null;

    /**
     * Construct a new Blade instance.
     */
    public function __construct(ContainerContract $container = null)
    {
        $this->container = (Container::getInstance() ?: $container) ?: new Container();

        $this->container->singleton(ViewFactoryContract::class, fn () => $this->factory());

        $this->container->singleton('view', fn () => $this->factory());
        $this->container->singleton(ViewFinderInterface::class, fn () => $this->finder());

        $this->engineResolver()->register(self::ENGINE_NAME, fn () => $this->engine());

        static::ensureCachePathIsSet();
    }

    /**
     * Ensure the cache path is set and not null.
     */
    protected static function ensureCachePathIsSet(): void
    {
        if (is_null(static::$cachePath)) {
            static::setCachePath(__DIR__.'/../.cache');
        }
    }

    /**
     * Merge an array of blade component classes to use for component resolvement.
     */
    public static function components(array $components): void
    {
        static::$components = array_merge(static::$components, $components);
    }

    /**
     * Get the registered component classes to use for component resolvement.
     */
    public static function getComponents(): array
    {
        return static::$components;
    }

    /**
     * Forget the current registered component classes.
     */
    public static function forgetComponents(): void
    {
        static::$components = [];
    }

    /**
     * Set the cache compiled directory.
     *
     * @return void
     */
    public static function setCachePath(string $path)
    {
        static::$cachePath = $path;
    }

    /**
     * Delegate undefined methods to the compiler/file factory.
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $compiler = $this->compiler();

        if (method_exists($compiler, $name)) {
            return $compiler->{$name}(...$arguments);
        }

        return $this->factory->{$name}(...$arguments);
    }

    /**
     * Return the set file finder or create a new one.
     */
    public function finder(): FileFinder
    {
        return $this->fileFinder ??= new FileFinder(new Filesystem, []);
    }

    /**
     * Return the set engine resolver or create a new one.
     */
    protected function engineResolver(): EngineResolver
    {
        if (! is_null($this->engineResolver)) {
            return $this->engineResolver;
        }

        return $this->engineResolver = new EngineResolver();
    }

    /**
     * Return the set file factory or create a new one.
     */
    protected function factory(): FileFactory
    {
        return $this->factory ??= new FileFactory(
            $this->engineResolver(),
            $this->finder(),
            new Dispatcher($this->container)
        );
    }

    /**
     * Return the current set cache path.
     */
    public static function getCachePath(): ?string
    {
        static::ensureCachePathIsSet();

        return static::$cachePath;
    }

    /**
     * Return the set file compiler engine or create a new one.
     */
    protected function engine(): FileCompilerEngine
    {
        return $this->engine ??= new FileCompilerEngine($this->compiler());
    }

    /**
     * Return the set file compiler or create a new one.
     */
    protected function compiler(): FileCompiler
    {
        return $this->compiler ??= new FileCompiler(new Filesystem, static::getCachePath());
    }

    /**
     * Render a file with the given variables.
     *
     *
     * @throws \Surgiie\Blade\Exceptions\FileException
     */
    public function render(string $path, array $vars = []): string
    {
        if (! is_file($path)) {
            throw new FileException("The $path file does not exist.");
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
                throw new FileException(
                    "Undefined variable \$$match[1] on line $line.",
                );
            }
        });

        // render and return the contents.
        try {
            $contents = $factory->make($info->getFilename(), $vars)->render();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            restore_error_handler();
        }

        return $contents;
    }

    /**
     * Delete the set cache directory.
     */
    public static function deleteCacheDirectory(): bool
    {
        static::ensureCachePathIsSet();

        return (new Filesystem)->deleteDirectory(static::getCachePath());
    }
}
