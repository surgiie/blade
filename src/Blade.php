<?php

namespace Surgiie\Blade;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as FoundationApplication;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\ViewFinderInterface;
use SplFileInfo;
use Surgiie\Blade\Concerns\ParsesFilePath;
use Surgiie\Blade\Exceptions\FileNotFoundException;
use Surgiie\Blade\Exceptions\UndefinedVariableException;

class Blade
{
    use ParsesFilePath;

    /**
     * Get the engine name for resolver registration.
     */
    public const ENGINE_NAME = 'blade';

    /**Path to compiled files.*/
    protected string $compiledPath;

    /**
     * The filesystem instance.
     */
    protected Filesystem $filesystem;

    /**
     * File info about the file being rendered.
     */
    protected ?SplFileInfo $fileInfo = null;

    /**
     * The file finder instance.
     */
    protected ?FileFinder $fileFinder = null;

    /**
     * The file factory instance.
     */
    protected ?FileFactory $fileFactory = null;

    /**Whether cached files should get used.*/
    protected static bool $useCachedFiles = true;

    /**
     * The engine resolver instance.
     */
    protected ?EngineResolver $resolver = null;

    /**
     * The file compiler instance.
     */
    protected ?FileCompiler $fileCompiler = null;

    /**
     * The file compiler engine instance.
     */
    protected ?FileCompilerEngine $compilerEngine = null;

    /**
     * The container instance.
     */
    protected Container|FoundationApplication $container;

    /**
     * Construct a new Blade instance and configure engine.
     */
    public function __construct(Container|FoundationApplication $container, Filesystem $filesystem, string $compiledPath = null)
    {
        $this->container = $container;
        $this->filesystem = $filesystem;

        $this->compiledPath = $compiledPath ?: __DIR__.'/../.compiled';

        $this->container->bind(ViewFactoryContract::class, function () {
            return $this->getFileFactory();
        });

        $this->container->bind('view', function () {
            return $this->getFileFactory();
        });

        $this->container->bind(ViewFinderInterface::class, function () {
            return $this->getFileFinder();
        });

        Container::setInstance($this->container);

        $this->makeCompiledDirectory();

        $this->resolver = $this->getEngineResolver();

        $this->resolver->register(self::ENGINE_NAME, function () {
            return $this->getCompilerEngine();
        });
    }

    /**Set whether cached files should be used or not. */
    public static function useCachedCompiledFiles(bool $useCacheFiles)
    {
        static::$useCachedFiles = $useCacheFiles;
    }

    /**Get whether cached compiled files should be used or not. */
    public static function shouldUseCachedCompiledFiles()
    {
        return static::$useCachedFiles;
    }

    /**Normalize a path for the appropriate OS/directory separator.*/
    protected static function normalizePathForOS(string $path)
    {
        if (DIRECTORY_SEPARATOR == '\\') {
            return str_replace('/', '\\', $path);
        }

        return $path;
    }

    /**
     * Return the set file finder.
     */
    public function getFileFinder(): FileFinder
    {
        if (! is_null($this->fileFinder)) {
            return $this->fileFinder;
        }

        return $this->fileFinder = new FileFinder($this->filesystem, []);
    }

    /**Set the compiled path.*/
    public function setCompiledPath(string $path)
    {
        $this->compiledPath = $path;

        return $this;
    }

    /**
     * Return the set engine resolver.
     */
    protected function getEngineResolver(): EngineResolver
    {
        if (! is_null($this->resolver)) {
            return $this->resolver;
        }

        return $this->resolver = new EngineResolver();
    }

    /**
     * Return set file factory instance.
     */
    protected function getFileFactory(): FileFactory
    {
        if (! is_null($this->fileFactory)) {
            return $this->fileFactory;
        }

        return $this->fileFactory = new FileFactory(
            $this->getEngineResolver(),
            $this->getFileFinder(),
            new Dispatcher($this->container)
        );
    }

    /**
     * Return set compiler engine instance.
     */
    protected function getCompilerEngine(): FileCompilerEngine
    {
        if (! is_null($this->compilerEngine)) {
            return $this->compilerEngine;
        }

        return $this->compilerEngine = new FileCompilerEngine($this->getFileCompiler());
    }

    /**
     * Return the set file compiler instance.
     */
    protected function getFileCompiler(): FileCompiler
    {
        if (! is_null($this->fileCompiler)) {
            return $this->fileCompiler;
        }

        return $this->fileCompiler = new FileCompiler($this->filesystem, $this->getCompiledPath());
    }

    /**
     * Get the compiled path to where compiled files go.
     */
    public function getCompiledPath(): string
    {
        return $this->compiledPath;
    }

    /**
     * Make the directory where compiled files go.
     */
    public function makeCompiledDirectory(): bool
    {
        return @mkdir($this->getCompiledPath());
    }

    /**
     * Return a custom error handler for when we render a file.
     */
    protected function getRenderErrorHandler(): Closure
    {
        return function ($severity, $message, $file, $line) {
            if ($severity != E_WARNING) {
                return;
            }
            preg_match('/Undefined variable \$(.*)/', $message, $match);

            if ($match) {
                throw new UndefinedVariableException(
                    "Undefined variable \$$match[1] on line $line."
                );
            }
        };
    }

    /**Compile a file and return the contents.*/
    public function compile(string $path, array $data): string
    {
        $path = static::normalizePathForOS($path);

        $path = static::parseFilePath($path);

        $real_path = realpath($path);

        if ($real_path === false || ! is_file($path)) {
            throw new FileNotFoundException("The $path file does not exist.");
        }
        $finder = $this->getFileFinder();

        $finder->replaceNamespace('__components', $this->getCompiledPath());

        $info = new SplFileInfo($real_path);

        $factory = $this->getFileFactory();
        // flush found files, so we're not returning files that match in path when using relative paths.
        $finder->flush();

        $finder->setPaths([dirname($info->getRealPath())]);

        $factory->addExtension($info->getExtension(), self::ENGINE_NAME);

        $file = $factory->make($info->getFilename(), $data);

        set_error_handler($this->getRenderErrorHandler());

        $contents = $file->render();

        restore_error_handler();

        return $contents;
    }
}
