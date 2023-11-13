<?php

namespace Surgiie\Blade;

use Illuminate\Container\Container;
use Surgiie\Blade\Exceptions\FileException;
use Illuminate\View\Component as BladeComponent;

abstract class Component extends BladeComponent
{
    public static function resolve($data)
    {
        $class = null;

        if (! isset($data['view'])){
            return parent::resolve($data);
        }

        if(! is_file($data['view']) && !  class_exists($data['view'])) {
            throw new FileException("Could not resolve component class or file for: {$data['view']}");
        }

        if(is_file($data['view']) && str_ends_with($data['view'], '.php') && ! class_exists($data['view'])){
            $class = require_once $data['view'];
        }

        if(is_int($class)){
            throw new FileException("Could not resolve or require component class for: {$data['view']}, must return a class.");
        }

        return $class ? Container::getInstance()->make($class, $data["data"]) : parent::resolve($data);
    }

    protected function createBladeViewFromString($factory, $contents)
    {
        $directory = Blade::getCachePath();

        if (! is_file($viewFile = $directory.'/'.hash('xxh128', $contents).'.php')) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($viewFile, $contents);
        }

        return '__components::'.basename($viewFile, '.php').'.php';
    }
}
