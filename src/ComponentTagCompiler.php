<?php

namespace Surgiie\Blade;

use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;
use InvalidArgumentException;
use Surgiie\Blade\Concerns\ParsesComponentFilePath;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    use ParsesComponentFilePath;

    /**The file being compiled that contains components.*/
    protected string $path;

    /**
     * The component to file stack.
     *
     * @var array
     */
    protected static $componentToFileStack = [];

    /**
     * Create a new \Surgiie\Blade\ComponentTagCompiler instance.
     */
    public function __construct(string $path, array $aliases = [], array $namespaces = [], ?FileCompiler $compiler = null)
    {
        $this->path = $path;
        parent::__construct($aliases, $namespaces, $compiler);
    }

    /**
     * Register a component to file entry
     *
     * @param string $component
     * @param string $file
     * @param string $class
     * @return void
     */
    public static function newComponentToFile(string $component, string $file, string $class)
    {
        static::$componentToFileStack[$component] = [$file, $class];
    }

    /**
     * Get a component to file entry.
     *
     * @param string $component
     * @param string $compilingPath
     * @return void
     */
    public static function getComponentFilePath(string $component, string $compilingPath)
    {
        $path = static::$componentToFileStack[$component];

        if ($path) {
            return $path;
        }
        
        $parsed = static::parseComponentPath($component);
        if (str_starts_with($parsed, '/')) {
            return $parsed;
        }
        // if no saved path, compute if relative to the file being compiled.
        return [dirname($compilingPath).DIRECTORY_SEPARATOR.$component, AnonymousComponent::class];
    }

    /**
     * Generate a component string.
     *
     * @param string $component
     * @param array $attributes
     * @return void
     */
    protected function componentString(string $component, array $attributes)
    {
        $string = parent::componentString($component, $attributes);

        return str_replace(BladeAnonymousComponent::class, AnonymousComponent::class, $string);
    }

    /**
     * Get the component class for a given component alias.
     *
     * @param string $component
     * @return void
     */
    public function componentClass(string $component)
    {
        // if component starts with -, meaning they used a <x-- for tag, we're using absolute path.
        if (str_starts_with($component, '-')) {
            $directory = DIRECTORY_SEPARATOR;
        } else {
            $directory = dirname($this->path).DIRECTORY_SEPARATOR;
        }

        $path = static::parseComponentPath($component);

        $path = $directory.ltrim($path, DIRECTORY_SEPARATOR);
        // if a file with the what we assumed is a file extension doesnt exist.
        // then replace it with a directory separator as its likely a file without extension.
        if (! file_exists($path)) {
            $path = str_replace('.', DIRECTORY_SEPARATOR, $path);
        }
        if (array_key_exists($path, static::$componentToFileStack)) {
            return static::$componentToFileStack[$path];
        }

        if (is_file($componentPhpFilePath = $path.'.php') || is_file($componentPhpFilePath = $path) && str_ends_with($path, '.php')) {
            $class = require_once "$componentPhpFilePath";

            if (is_numeric($class) || ! class_exists($class)) {
                throw new InvalidArgumentException(
                    "File [{$componentPhpFilePath}] must return ::class constant."
                );
            }

            static::newComponentToFile($component, $componentPhpFilePath, $class);

            return $class;
        }

        return $component;
    }
}
