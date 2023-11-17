<?php

namespace Surgiie\Blade;

use Illuminate\Container\Container;
use Illuminate\View\Component as BladeComponent;
use Surgiie\Blade\Exceptions\FileException;

abstract class Component extends BladeComponent
{
    /**
     * A cache array of component classes that have already been resolved.
     *
     * @var array
     */
    protected static $resolvedComponents = [];

    /**
     * Resolve the component class instance.
     *
     * @param  array  $data
     * @return mixed
     *
     * @throws \Surgiie\Blade\Exceptions\FileException
     */
    public static function resolve($data)
    {
        $class = null;

        if (! isset($data['view'])) {
            return parent::resolve($data);
        }

        if (array_key_exists($data['view'], static::$resolvedComponents)) {
            $class = static::$resolvedComponents[$data['view']];
        } elseif (! is_file($data['view'])) {
            throw new FileException("Could not resolve component class or file for: {$data['view']}");
        } elseif (is_file($data['view']) && str_ends_with($data['view'], '.php')) {
            $class = require_once $data['view'];
            static::$resolvedComponents[$data['view']] = $class;
        } elseif (! is_null($class) && ! is_string($class)) {
            throw new FileException("Could not resolve or require component class for: {$data['view']}, must return a class.");
        }

        return $class ? Container::getInstance()->make($class, $data['data']) : parent::resolve($data);
    }

    /**
     * Create a blade component path string path from a string.
     *
     * @param  \Surgiie\Blade\FileFactory  $factory
     * @param  string  $contents
     * @return string
     */
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
