<?php

namespace Surgiie\Blade;

use Illuminate\View\Factory;
use InvalidArgumentException;

class FileFactory extends Factory
{
    /**
     * Disable dot notation normalization.
     */
    protected function normalizeName($name)
    {
        return $name;
    }

    /**Require the component class if needed.*/
    public function requireComponentClass(string $class, string $path)
    {
        if (! class_exists($class)) {
            $class = require_once $path;

            if (is_numeric($class) || ! class_exists($class)) {
                throw new InvalidArgumentException(
                    "File [{$path}] must return ::class constant."
                );
            }
        }
    }

    /**
     * Get the first view that actually exists from the given list.
     */
    public function first(array $views, $data = [], $mergeData = [], $options = [])
    {
        $file = parent::first($views, $data, $mergeData);

        $file->setRenderOptions($options);

        return $file;
    }

    /**
     * Get the rendered content of the view based on a given condition.
     */
    public function renderWhen($condition, $view, $data = [], $mergeData = [], $options = [])
    {
        if (! $condition) {
            return '';
        }

        $file = $this->make($view, $this->parseData($data), $mergeData);

        $file->setRenderOptions($options);

        return $file->render();
    }

    /**
     * Get the rendered content of the view based on the negation of a given condition.
     */
    public function renderUnless($condition, $view, $data = [], $mergeData = [], $options = [])
    {
        return $this->renderWhen(! $condition, $view, $data, $mergeData, $options);
    }

    /**
     * Get the evaluated view contents for the given view.
     */
    public function make($view, $data = [], $mergeData = [], $options = [])
    {
        $file = parent::make($view, $data, $mergeData);

        $file->setRenderOptions($options);

        return $file;
    }

    /**
     * Get the extension used by the view file.
     */
    protected function getExtension($path)
    {
        return pathinfo($path)['extension'] ?? '';
    }

    /**
     * Get the appropriate view engine for the given path.
     */
    public function getEngineFromPath($path)
    {
        return $this->engines->resolve(Blade::ENGINE_NAME);
    }

    /**
     * Create a new view instance from the given arguments.
     */
    protected function viewInstance($view, $path, $data)
    {
        return new File($this, $this->getEngineFromPath($path), $view, $path, $data);
    }
}
