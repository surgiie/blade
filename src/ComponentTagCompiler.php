<?php

namespace Surgiie\Blade;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\View\AnonymousComponent;
use Surgiie\Blade\Concerns\ParsesComponentFilePath;
use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;
use Illuminate\View\DynamicComponent;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    use ParsesComponentFilePath;

    protected string $path;

    public function __construct(string $path, array $aliases = [], array $namespaces = [], ?FileCompiler $compiler = null)
    {
        $this->path = $path;
        parent::__construct($aliases, $namespaces, $compiler);
    }

    /**
     * Generate a component string for a compiled file.
     */
    protected function componentString(string $component, array $attributes): string
    {
        if(! str_starts_with($this->path, "-")){
            $component = dirname($this->path).DIRECTORY_SEPARATOR.$component;
        }else{
            // TODO
        }

        $string = parent::componentString($component, $attributes);

        return str_replace(BladeAnonymousComponent::class, AnonymousComponent::class, $string);
    }

    /**
     * Get the component class for a given component alias.
     */
    public function componentClass(string $component)
    {
        try{
            return parent::componentClass($component);
        }catch(BindingResolutionException){
            return $component;
        }
    }
}
