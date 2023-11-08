<?php

namespace Surgiie\Blade;

use Illuminate\View\Component as BladeComponent;

abstract class Component extends BladeComponent
{
    protected function createBladeViewFromString($factory, $contents)
    {
        $directory = Blade::getCachePath();

        if (! is_file($viewFile = $directory.'/'.hash('xxh128', $contents).'.blade.php')) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($viewFile, $contents);
        }

        return '__components::'.basename($viewFile, '.php').'.php';
    }
}
