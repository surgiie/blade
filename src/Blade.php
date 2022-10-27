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
use Surgiie\Blade\Exceptions\FileNotFoundException;
use Surgiie\Blade\Exceptions\UndefinedVariableException;

class Blade
{
    /**A static instance of the engine. */
    protected static ?Blade $instance = null;

    /**
     * The render file instance.
     */
    protected ?File $file = null;

    /**
     * Get the engine name for resolver registration.
     */
    public const ENGINE_NAME = 'blade';

    /**
     * The filesystem instance.
     */
    protected Filesystem $filesystem;

    /**
     * File info about the file being rendered.
     */
    protected ?SplFileInfo $fileInfo = null;

    /**
     * The file factory instance.
     */
    protected ?FileFactory $fileFactory = null;

    /**
     * The file finder instance.
     */
    protected ?FileFinder $fileFinder = null;

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
    public function __construct(Container|FoundationApplication $container, Filesystem $filesystem)
    {
        $this->container = $container;
        $this->filesystem = $filesystem;

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

        $instance = static::getInstance();
        if (is_null($instance)) {
            static::setInstance($this);
        }
    }

    /**Set static Blade instance.*/
    public function setInstance(Blade $blade)
    {
        static::$instance = $blade;
    }

    /**Get static Blade instance.*/
    public static function getInstance(): Blade|null
    {
        return static::$instance;
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
        return __DIR__.'/../.compiled';
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
        $real_path = realpath(static::normalizePathForOS($path));

        if ($real_path === false || ! is_file($path)) {
            throw new FileNotFoundException("The $path file does not exist.");
        }

        $finder = $this->getFileFinder();

        $info = new SplFileInfo($real_path);

        $factory = $this->getFileFactory();

        $finder->setPaths([dirname($info->getRealPath())]);

        $factory->addExtension($info->getExtension(), self::ENGINE_NAME);

        $file = $factory->make($info->getFilename(), $data);

        set_error_handler($this->getRenderErrorHandler());

        $contents = $file->render();

        restore_error_handler();

        // flush found files so that we ensure we dont load wrong file contents
        // from the $this->view cache propety. May happen when compiling basename file
        // paths.
        $finder->flush();

        return $contents;
    }
}
