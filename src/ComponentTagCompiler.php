<?php

namespace Surgiie\Blade;

use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;
use InvalidArgumentException;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    /**Loaded classes.*/
    protected array $classes = [];

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
        $component = str_replace('.', DIRECTORY_SEPARATOR, $component);

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
