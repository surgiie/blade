<?php

namespace Surgiie\Blade;

use Illuminate\Support\Str;
use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Illuminate\View\Compilers\ComponentTagCompiler as BladeComponentTagCompiler;

class ComponentTagCompiler extends BladeComponentTagCompiler
{
    /**
     * The path to the file being rendered.
     */
    protected string $path;

    /**
     * Construct a new ComponentTagCompiler instance.
     */
    public function __construct(string $path, array $aliases = [], array $namespaces = [], FileCompiler $compiler = null)
    {
        $this->path = $path;
        parent::__construct($aliases, $namespaces, $compiler);
    }

    /**
     * Compile a component down to valid PHP.
     */
    protected function componentString(string $component, array $attributes): string
    {
        $path = $component;
        $bladeComponents = Blade::getComponents();

        if (array_key_exists($path, $bladeComponents)) {
            return str_replace(BladeAnonymousComponent::class, $bladeComponents[$path], parent::componentString($path, $attributes));
        }

        if (! str_starts_with($path, '-')) {
            $path = dirname($this->path).DIRECTORY_SEPARATOR.$path;
        } else {
            $path = Str::start(ltrim($path, '-'), DIRECTORY_SEPARATOR);
        }

        $path = str_replace('.', DIRECTORY_SEPARATOR, $path);

        $path = is_file($path) ? $path : Str::replaceLast(DIRECTORY_SEPARATOR, '.', $path);

        return str_replace(BladeAnonymousComponent::class, AnonymousComponent::class, parent::componentString($path, $attributes));
    }

    /**
     * Determine the php class for a given component.
     *
     * @return void
     */
    public function componentClass(string $component)
    {
        // use registered class or component name that can be resolved later by AnonymousComponent class.
        return Blade::getComponents()[$component] ?? $component;
    }
}
