<?php

namespace Surgiie\Blade;

use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;
use InvalidArgumentException;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    /**Loaded classes.*/
    protected array $classes = [];

    /**The file being compiled that contains components.*/
    protected string $path;

    /**
     * Create a new component tag compiler.
     */
    public function __construct(string $path, array $aliases = [], array $namespaces = [], ?FileCompiler $compiler = null)
    {
        $this->path = $path;
        parent::__construct($aliases, $namespaces, $compiler);
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
        if (str_starts_with($component, '-')) {
            $component = ltrim($component, '-');
            $directory = DIRECTORY_SEPARATOR;
        } else {
            $directory = dirname($this->path).DIRECTORY_SEPARATOR;
        }

        $ext = pathinfo($component)['extension'] ?? '';

        $component = str_replace('.', DIRECTORY_SEPARATOR, rtrim($component, ".$ext"));

        $component = $ext ? "$component.$ext" : $component;

        $component = $directory.$component;

        // if a file with the what we assumed is a file extension doesnt exist.
        // then replace it with a directory separator as its likely a file without extension.
        if (! file_exists($component)) {
            $component = str_replace('.', DIRECTORY_SEPARATOR, $component);
        }
        if (array_key_exists($component, $this->classes)) {
            return $this->classes[$component];
        }

        if (file_exists($componentPhpFile = $component.'.php')) {
            $class = require_once "$componentPhpFile";

            if (is_numeric($class) || ! class_exists($class)) {
                throw new InvalidArgumentException(
                    "File [{$component}.php] must return ::class constant."
                );
            }

            return $this->classes[$component] = $class;
        }
        // base class will use anonymous component when a class doesnt exist.
        return $component;
    }
}
