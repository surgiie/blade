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
        $directory = Blade::getCachePath();

        if (! is_file($viewFile = $directory.'/'.sha1($contents).'.php')) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            file_put_contents($viewFile, $contents);
        }

        // $factory->addNamespace(
        //     '__components',
        //     $directory = Container::getInstance()['config']->get('view.compiled')
        // );

        // if (! is_file($viewFile = $directory.'/'.hash('xxh128', $contents).'.blade.php')) {
        //     if (! is_dir($directory)) {
        //         mkdir($directory, 0755, true);
        //     }

        //     file_put_contents($viewFile, $contents);
        // }

        // return '__components::'.basename($viewFile, '.blade.php');

        return '__components::'.basename($viewFile, '.php').'.php';
    }
}
