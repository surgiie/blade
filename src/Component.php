<?php

namespace Surgiie\Blade;

use Illuminate\View\Component as BladeComponent;

abstract class Component extends BladeComponent
{
    /**
     * Create a Blade view with the raw component string content.
     *
     * @param  \Illuminate\Contracts\View\Factory  $factory
     * @param  string  $contents
     * @return string
     */
    protected function createBladeViewFromString($factory, $contents)
    {
        $factory->addNamespace(
            '__components',
            $directory = blade()->getCompiledPath()
        );

        if (! is_file($viewFile = $directory.'/'.sha1($contents).'.php')) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            file_put_contents($viewFile, $contents);
        }

        return '__components::'.basename($viewFile, '.php').'.php';
    }
}
