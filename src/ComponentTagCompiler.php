<?php

namespace Surgiie\Blade;

use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;
use InvalidArgumentException;
use Surgiie\Blade\Concerns\ParsesFilePath;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    use ParsesFilePath;

    /**The file being compiled that contains components.*/
    protected string $path;

    /**
     * The component to file stack.
     *
     * @var array
     */
    protected static $componentToFileStack = [];

    /**Register a component to file entry.*/
    public static function newComponentToFile(string $component, string $file, string $class)
    {
        static::$componentToFileStack[$component] = [$file, $class];
    }

    /**Get a component to file entry.*/
    public static function getComponentFilePath(string $component, string $compilingPath)
    {
        $path = static::$componentToFileStack[$component];
        if ($path) {
            return $path;
        }
        // if no saved path, compute if relative to the file being compiled.
        return [dirname($compilingPath).DIRECTORY_SEPARATOR.$component, AnonymousComponent::class];
    }

    /**
     * Create a new component tag compiler.
     */
    public function __construct(string $path, array $aliases = [], array $namespaces = [], ?FileCompiler $compiler = null)
    {
        $this->path = $path;
        parent::__construct($aliases, $namespaces, $compiler);
    }

    protected function componentString(string $component, array $attributes)
    {
        $string = parent::componentString($component, $attributes);

        return str_replace(BladeAnonymousComponent::class, AnonymousComponent::class, $string);
    }

    /**
     * Get the component class for a given component alias.
     *
     * @param  string  $component
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function componentClass(string $component)
    {
        // if component starts with -, meaning they used a <x-- for tag, we're using absolute path.
        if (str_starts_with($component, '-')) {
            $directory = DIRECTORY_SEPARATOR;
        } else {
            $directory = dirname($this->path).DIRECTORY_SEPARATOR;
        }

        $path = $this->parseFilePath($component);

        $path = $directory.ltrim($path, DIRECTORY_SEPARATOR);
        // if a file with the what we assumed is a file extension doesnt exist.
        // then replace it with a directory separator as its likely a file without extension.
        if (! file_exists($path)) {
            $path = str_replace('.', DIRECTORY_SEPARATOR, $path);
        }
        if (array_key_exists($path, static::$componentToFileStack)) {
            return static::$componentToFileStack[$path];
        }

        if (file_exists($componentPhpFilePath = $path.'.php')) {
            $class = require_once "$componentPhpFilePath";

            if (is_numeric($class) || ! class_exists($class)) {
                throw new InvalidArgumentException(
                    "File [{$componentPhpFilePath}] must return ::class constant."
                );
            }

            static::newComponentToFile($component, $componentPhpFilePath, $class);

            return $class;
        }
        // base class will use anonymous component when a class doesnt exist.
        return $component;
    }
}
