<?php

namespace Surgiie\Blade;

use SplFileInfo;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\EngineResolver;
use Surgiie\Blade\Exceptions\FileNotFoundException;
use Surgiie\Blade\Exceptions\UndefinedVariableException;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Contracts\Container\Container as ContainerContract;

class BladeEngine
{
    public const ENGINE_NAME = 'blade';
    protected string $cachePath;
    protected ?FileFinder $fileFinder = null;
    protected ?FileFactory $fileFactory = null;
    protected ?EngineResolver $resolver = null;
    protected ?FileCompiler $fileCompiler = null;
    protected ?FileCompilerEngine $compilerEngine = null;

    public function __construct(ContainerContract $container, string $cachePath)
    {
        $this->container = $container;
        $this->cachePath = $cachePath;

        $this->container->bind(ViewFactoryContract::class, fn () => $this->getFileFactory());
        $this->container->bind('view', fn () => $this->getFileFactory());
        $this->container->bind(ViewFinderInterface::class, fn () => $this->getFileFinder());

        Container::setInstance($this->container);

        $this->getEngineResolver()->register(self::ENGINE_NAME, fn () => $this->getCompilerEngine());
    }

    public function getFileFinder(): FileFinder
    {
        return $this->fileFinder ??= new FileFinder(new Filesystem, []);
    }

    public function setCachePath(string $path)
    {
        $this->cachePath = $path;

        return $this;
    }

    protected function getEngineResolver(): EngineResolver
    {
        if (! is_null($this->resolver)) {
            return $this->resolver;
        }

        return $this->resolver = new EngineResolver();
    }

    protected function getFileFactory(): FileFactory
    {
        return $this->fileFactory ??= new FileFactory(
            $this->getEngineResolver(),
            $this->getFileFinder(),
            new Dispatcher($this->container)
        );
    }

    public function getCachePath(): ?string
    {
        return $this->cachePath;
    }

    protected function getCompilerEngine(): FileCompilerEngine
    {
        return $this->compilerEngine ??= new FileCompilerEngine($this->getFileCompiler());
    }

    protected function getFileCompiler(): FileCompiler
    {
        return  $this->fileCompiler ??= new FileCompiler(new Filesystem, $this->getCachePath());
    }

    public function render(string $path, array $vars = []): string
    {
        if (! is_file($path)) {
            throw new FileNotFoundException("The $path file does not exist.");
        }

        $info = new SplFileInfo($path);

        $finder = $this->getFileFinder();
        // // replace the namespace for components to the compiled path so the file finder can find them.
        // $finder->replaceNamespace('__components', $this->getCompiledPath());

        // ensure we're in a clean state before rendering so we can render files on the fly without conflicts.
        $finder->flush();

        $factory = $this->getFileFactory();

        // dont use realpath on phar file paths as it will always be false, since phar files are virtual.
        $directory = str_starts_with($path, 'phar://') ? dirname($path) : dirname($info->getRealPath());

        // tell the finder about the directory this file is in and it's file extension.
        $finder->setPaths([$directory]);
        $factory->addExtension($info->getExtension(), self::ENGINE_NAME);

        $file = $factory->make($info->getFilename(), $vars);

        // Set an error handler that throws an exception on undefined variables instead of a warning.
        set_error_handler(function ($severity, $message, $file, $line) {
            if ($severity != E_WARNING) {
                return;
            }
            preg_match('/Undefined variable \$(.*)/', $message, $match);

            if ($match) {
                throw new UndefinedVariableException(
                    "Undefined variable \$$match[1] on line $line."
                );
            }
        });

        // render and return the contents.
        try {
            $contents = $file->render();
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

    public function deleteCachePath()
    {
        (new Filesystem)->deleteDirectory($this->getCachePath());
    }
}
