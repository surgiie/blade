<?php

namespace Surgiie\Blade;

use Illuminate\Support\Str;
use Surgiie\Blade\AnonymousComponent;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    protected string $path;

    public function __construct(string $path, array $aliases = [], array $namespaces = [], FileCompiler $compiler = null)
    {
        $this->path = $path;
        parent::__construct($aliases, $namespaces, $compiler);
    }

    protected function componentString(string $component, array $attributes): string
    {
        $bladeComponents = Blade::getComponents();

        if (array_key_exists($component, $bladeComponents)) {
            return str_replace(BladeAnonymousComponent::class, $bladeComponents[$component], parent::componentString($component, $attributes));
        }

        if (! str_starts_with($component, '-')) {
            $component = dirname($this->path).DIRECTORY_SEPARATOR.$component;
        } else {
            $component = Str::start(ltrim($component, '-'), DIRECTORY_SEPARATOR);
        }

        $component = str_replace('.', DIRECTORY_SEPARATOR, $component);

        $component = is_file($component) ? $component : Str::replaceLast(DIRECTORY_SEPARATOR, '.', $component);

        return str_replace(BladeAnonymousComponent::class, AnonymousComponent::class, parent::componentString($component, $attributes));
    }

    public function componentClass(string $component)
    {
        return Blade::getComponents()[$component] ?? $component;
    }
}
