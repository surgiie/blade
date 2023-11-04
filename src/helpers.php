<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;

if (! function_exists('blade')) {
    /**
     * Return a fresh blade instance.
     */
    function blade(): Blade
    {
        $container = Container::getInstance();

        if (is_null($container)) {
            $container = new Container;
        }

        return new Blade($container, new Filesystem);
    }
}
