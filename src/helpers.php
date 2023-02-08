<?php

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Surgiie\Blade\Blade;

if (! function_exists('blade')) {
    /**
     * Return a fresh blade instance.
     *
     * @return \Surgiie\Blade\Blade
     */
    function blade()
    {
        $container = Container::getInstance();

        if (is_null($container)) {
            $container = new Container;
        }

        return new Blade($container, new Filesystem);
    }
}
