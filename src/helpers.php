<?php

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Surgiie\Blade\Blade;

if (! function_exists('blade')) {
    /**Return the blade instance.*/
    function blade()
    {
        $blade = Blade::getInstance();

        $container = Container::getInstance();

        if (is_null($container)) {
            $container = new Container;
        }

        if (is_null($blade)) {
            $blade = new Blade($container, new Filesystem);
        }

        return $blade;
    }
}
