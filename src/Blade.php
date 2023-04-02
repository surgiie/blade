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
    /**
     * Get the engine name for resolver registration.
     */
    public const ENGINE_NAME = 'blade';

    /**Path to compiled files.*/
    protected string $compiledPath;

    /**
     * Whether the container/engine is booted.
     */
    protected bool $booted = false;

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

    /**
     * The engine resolver instance.
     */
    protected ?EngineResolver $resolver = null;

    /**
     * Whether compiled views should be cached into directory.
     */
    protected static bool $cacheCompiled = true;

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

    public function __construct(Container|FoundationApplication $container, Filesystem $filesystem, string $compiledPath = null)
    {
        $this->container = $container;
        $this->filesystem = $filesystem;
        $this->compiledPath = $compiledPath ?: __DIR__.'/../.compiled';
    }

    /**
     * Normalize a path for the appropriate OS/directory separator.
     */
    protected static function normalizePathForOS(string $path): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path = str_replace('/', '\\', $path);
        } else {
            $path = str_replace('\\', '/', $path);
        }

        return $path;
    }

    /**
     * Return the file finder that searches for possible files to render.
     */
    public function getFileFinder(): FileFinder
    {
        return $this->fileFinder ??= new FileFinder($this->filesystem, []);
    }

    /**
     * Set the compiled cached path.
     */
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
     * Return the file factory that renders the files.
     */
    protected function getFileFactory(): FileFactory
    {
        return $this->fileFactory ??= new FileFactory(
            $this->getEngineResolver(),
            $this->getFileFinder(),
            new Dispatcher($this->container)
        );
    }

    /**
     * Return the file compiler engine.
     */
    protected function getCompilerEngine(): FileCompilerEngine
    {
        return $this->compilerEngine ??= new FileCompilerEngine($this->getFileCompiler());
    }

    /**
     * Return the file compiler.
     */
    protected function getFileCompiler(): FileCompiler
    {
        return  $this->fileCompiler ??= new FileCompiler($this->filesystem, $this->getCompiledPath(), shouldCache: static::$cacheCompiled);
    }

    /**
     * Enable compile caching into a directory.
     */
    public static function cacheCompiled(bool $flag): void
    {
        static::$cacheCompiled = $flag;
    }

    /**
     * Enable compile caching into a directory.
     */
    public static function isCachingCompiled(): bool
    {
        return static::$cacheCompiled;
    }

    /**
     * Get the compiled path to where compiled files go.
     */
    public function getCompiledPath(): ?string
    {
        return $this->compiledPath;
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

    /**
     * Boot and register container dependencies.
     */
    protected function boot(): bool
    {
        if ($this->booted) {
            return false;
        }
        $this->container->bind(ViewFactoryContract::class, fn () => $this->getFileFactory());
        $this->container->bind('view', fn () => $this->getFileFactory());
        $this->container->bind(ViewFinderInterface::class, fn () => $this->getFileFinder());

        Container::setInstance($this->container);

        $this->resolver = $this->getEngineResolver();
        $this->resolver->register(self::ENGINE_NAME, fn () => $this->getCompilerEngine());

        return $this->booted = true;
    }

    /**
     * Compile a file and return the contents.
     */
    public function compile(string $path, array $data, bool $cache = true): string
    {
        Blade::cacheCompiled($cache);

        $this->boot();

        $path = static::normalizePathForOS($path);

        if (! is_file($path)) {
            throw new FileNotFoundException("The $path file does not exist.");
        }

        $finder = $this->getFileFinder();

        $finder->replaceNamespace('__components', $this->getCompiledPath());

        $info = new SplFileInfo($path);

        $factory = $this->getFileFactory();
        // flush found files, so we're not returning files that match in path when using relative paths.
        $finder->flush();

        $realPath = dirname($info->getRealPath());

        // dont use realpath on phar file paths as it will always be false.
        if (str_starts_with($path, 'phar://')) {
            $realPath = dirname($path);
        }
        $finder->setPaths([$realPath]);

        $factory->addExtension($info->getExtension(), self::ENGINE_NAME);

        $file = $factory->make($info->getFilename(), $data);

        set_error_handler($this->getRenderErrorHandler());

        $contents = $file->render();

        restore_error_handler();

        if (! $cache) {
            $this->cleanupAfterNotCachedRender($path, $file);
        }

        Blade::cacheCompiled($cache);

        return $contents;
    }

    /**
     * Cleanup steps after a compile call for non cached file.
     */
    protected function cleanupAfterNotCachedRender(string $path, File $file)
    {
        $engine = $file->getEngine();

        $engine->forgetCompiledOrNotExpired();

        unlink($engine->getCompiler()->getCompiledPath($path));

        $fs = new Filesystem;

        if ($fs->isEmptyDirectory($compilePath = $this->getCompiledPath())) {
            $fs->deleteDirectory($compilePath);
        }
    }
}
