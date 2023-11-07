<?php

namespace Surgiie\Blade;

use Illuminate\Support\Str;
use Illuminate\View\DynamicComponent;
use Illuminate\View\AnonymousComponent;
use Surgiie\Blade\Concerns\ParsesComponentFilePath;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;

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
        $bladeComponents = Blade::getComponents();
        dd($component, $bladeComponents);

        if(! str_starts_with($component, "-")){
            $component = dirname($this->path).DIRECTORY_SEPARATOR.$component;
        }else{
            $component = Str::start(ltrim($component, "-"), DIRECTORY_SEPARATOR);
        }

        $component = str_replace(".", DIRECTORY_SEPARATOR, $component);

        $component = is_file($component) ? $component : Str::replaceLast(DIRECTORY_SEPARATOR, ".", $component);

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
            dd($component);
            return $component;
        }
    }
}
