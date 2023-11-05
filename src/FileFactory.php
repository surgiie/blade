<?php

namespace Surgiie\Blade;

use Illuminate\View\Factory;
use InvalidArgumentException;
use Surgiie\Blade\Concerns\Modifiers\ModifiesSpacing;

class FileFactory extends Factory
{
    use ModifiesSpacing;

    protected function normalizeName($name)
    {
        // Disable dot notation from file name.
        return $name;
    }

    // /**
    //  * Require the component class if needed.
    //  *
    //  * @return void
    //  */
    // public function requireComponentClass(string $class, string $path)
    // {
    //     if (! class_exists($class)) {
    //         $class = require_once $path;

    //         if (is_numeric($class) || ! class_exists($class)) {
    //             throw new InvalidArgumentException(
    //                 "File [{$path}] must return ::class constant."
    //             );
    //         }
    //     }
    // }



    // /**
    //  * Get the rendered content of the view based on a given condition.
    //  *
    //  * @param  bool  $condition
    //  * @param  string  $view
    //  * @param  array  $data
    //  * @param  array  $mergeData
    //  * @param  array  $modifiers
    //  * @return string
    //  */
    public function renderWhen($condition, $view, $data = [], $mergeData = [], $modifiers = [])
    {
        if (! $condition) {
            return '';
        }

        $file = $this->make($view, $this->parseData($data), $mergeData);

        return $file->render(modifiers: $modifiers);
    }

    public function renderUnless($condition, $view, $data = [], $mergeData = [], $modifiers = [])
    {
        return $this->renderWhen(! $condition, $view, $data, $mergeData, $modifiers);
    }

    /**
     * Render the current component.
     *
     * @return string
     */
    public function renderComponent(?array $modifiers = [])
    {
        return $this->modifySpacing( parent::renderComponent(), $modifiers);
    }

    // /**
    //  * Get the evaluated view contents for the given view.
    //  *
    //  * @param  string  $view
    //  * @param  array  $data
    //  * @param  array  $mergeData
    //  * @param  array  $modifiers
    //  * @return \Surgiie\Blade\File
    //  */
    public function make($view, $data = [], $mergeData = [])
    {
        $file = parent::make($view, $data, $mergeData);

        // $file->setOptions($modifiers);

        return $file;
    }

    protected function getExtension($path)
    {
        return pathinfo($path)['extension'] ?? '';
    }

    public function getEngineFromPath($path)
    {
        return $this->engines->resolve(Blade::ENGINE_NAME);
    }

    protected function viewInstance($view, $path, $data)
    {
        return new File($this, $this->getEngineFromPath($path), $view, $path, $data);
    }
}
