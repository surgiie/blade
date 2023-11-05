<?php

namespace Surgiie\Blade;

use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;
use InvalidArgumentException;
use Surgiie\Blade\Concerns\ParsesComponentFilePath;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    // use ParsesComponentFilePath;

    // /**The file being compiled that contains components.*/
    // protected string $path;

    // /**
    //  * A array to keep track of mapping component name/tagname to file path.
    //  */
    // protected static array $componentToFileStack = [];

    public function __construct(string $path, array $aliases = [], array $namespaces = [], ?FileCompiler $compiler = null)
    {
        $this->path = $path;
        parent::__construct($aliases, $namespaces, $compiler);
    }

    // /**
    //  * Register a component to file entry
    //  */
    // public static function newComponentToFile(string $component, string $file, string $class): void
    // {
    //     static::$componentToFileStack[$component] = [$file, $class];
    // }

    // /**
    //  * Get a component to file entry.
    //  */
    // public static function getComponentFilePath(string $component, string $compilingPath): array|string
    // {
    //     $path = static::$componentToFileStack[$component];

    //     if ($path) {
    //         return $path;
    //     }

    //     $parsed = static::parseComponentPath($component);
    //     if (str_starts_with($parsed, '/')) {
    //         return $parsed;
    //     }
    //     // if no saved path, compute if relative to the file being compiled.
    //     return [dirname($compilingPath).DIRECTORY_SEPARATOR.$component, AnonymousComponent::class];
    // }

    // /**
    //  * Generate a component string for a compiled file.
    //  */
    // protected function componentString(string $component, array $attributes): string
    // {
    //     $string = parent::componentString($component, $attributes);

    //     return str_replace(BladeAnonymousComponent::class, AnonymousComponent::class, $string);
    // }

    // /**
    //  * Get the component class for a given component alias.
    //  */
    // public function componentClass(string $component)
    // {
    //     // if component starts with -, meaning they used a <x-- for tag, we're using absolute path.
    //     if (str_starts_with($component, '-')) {
    //         $directory = DIRECTORY_SEPARATOR;
    //     } else {
    //         $directory = dirname($this->path).DIRECTORY_SEPARATOR;
    //     }

    //     $path = static::parseComponentPath($component);
    //     $path = $directory.ltrim($path, DIRECTORY_SEPARATOR);

    //     if (array_key_exists($path, static::$componentToFileStack)) {
    //         return static::$componentToFileStack[$path];
    //     }

    //     if (is_file($componentPhpFilePath = $path.'.php') || (is_file($componentPhpFilePath = $path) && str_ends_with($path, '.php'))) {
    //         $class = require_once $componentPhpFilePath;

    //         if (! is_string($class) || ! class_exists($class)) {
    //             throw new InvalidArgumentException(
    //                 "File [{$componentPhpFilePath}] must return a valid class name."
    //             );
    //         }

    //         static::newComponentToFile($component, $componentPhpFilePath, $class);

    //         return $class;
    //     }

    //     return $component;
    // }
}
