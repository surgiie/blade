<?php

namespace Surgiie\Blade;

use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;
use InvalidArgumentException;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    protected $classes = [];
    // /**
    //  * Guess the class name for the given component.
    //  *
    //  * @param  string  $component
    //  * @return string
    //  */
    // public function guessClassName(string $component)
    // {
    //     $namespace = 'App\\'; // customize?

    //     $class = $this->formatClassName($component);

    //     return $namespace.'View\\Components\\'.$class;
    // }

    /**
     * Find the class for the given component using the registered namespaces.
     *
     * @param  string  $component
     * @return string|null
     */
    public function findClassByComponent(string $component)
    {
        if (array_key_exists($component, $this->classes)) {
            return $this->classes[$component];
        }
        if (! file_exists($component.'.php')) {
            throw new InvalidArgumentException(
                "Unable to locate a class or file for component [{$component}.php]."
            );
        }

        require_once "$component.php";

        $classes = get_declared_classes();
        // need a way to extract user defined class from this
        dd($classes);
        $class = $classes[count($classes)];

        return $this->classes[$component] = $class;
    }
}
