<?php

namespace Surgiie\Blade;

use Illuminate\View\Factory;
use InvalidArgumentException;
use Surgiie\Blade\Concerns\ModifiesRenderedContent;

class FileFactory extends Factory
{
    // use ModifiesRenderedContent;

    // /**
    //  * Disable dot notation normalization.
    //  *
    //  * @param  string  $name
    //  * @return void
    //  */
    // protected function normalizeName($name)
    // {
    //     return $name;
    // }

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
    //  * Get the first view that actually exists from the given list.
    //  *
    //  * @param  array  $data
    //  * @param  array  $mergeData
    //  * @param  array  $options
    //  * @return string
    //  */
    // public function first(array $views, $data = [], $mergeData = [], $options = [])
    // {
    //     $file = parent::first($views, $data, $mergeData);

    //     $file->setRenderOptions($options);

    //     return $file;
    // }

    // /**
    //  * Get the rendered content of the view based on a given condition.
    //  *
    //  * @param  bool  $condition
    //  * @param  string  $view
    //  * @param  array  $data
    //  * @param  array  $mergeData
    //  * @param  array  $options
    //  * @return string
    //  */
    // public function renderWhen($condition, $view, $data = [], $mergeData = [], $options = [])
    // {
    //     if (! $condition) {
    //         return '';
    //     }

    //     $file = $this->make($view, $this->parseData($data), $mergeData);

    //     $file->setRenderOptions($options);

    //     return $file->render();
    // }

    // /**
    //  * Get the rendered content of the view based on the negation of a given condition.
    //  *
    //  * @param  bool  $condition
    //  * @param  string  $view
    //  * @param  array  $data
    //  * @param  array  $mergeData
    //  * @param  array  $options
    //  * @return string
    //  */
    // public function renderUnless($condition, $view, $data = [], $mergeData = [], $options = [])
    // {
    //     return $this->renderWhen(! $condition, $view, $data, $mergeData, $options);
    // }

    // /**
    //  * Render the current component.
    //  *
    //  * @return string
    //  */
    // public function renderComponent(?array $options = [])
    // {
    //     $contents = parent::renderComponent();

    //     return $this->modifyRenderedContent($contents, $options);
    // }

    // /**
    //  * Get the evaluated view contents for the given view.
    //  *
    //  * @param  string  $view
    //  * @param  array  $data
    //  * @param  array  $mergeData
    //  * @param  array  $options
    //  * @return \Surgiie\Blade\File
    //  */
    // public function make($view, $data = [], $mergeData = [], $options = [])
    // {
    //     $file = parent::make($view, $data, $mergeData);

    //     $file->setRenderOptions($options);

    //     return $file;
    // }

    // /**
    //  * Get the extension used by the view file.
    //  *
    //  * @param  string  $path
    //  * @return string
    //  */
    // protected function getExtension($path)
    // {
    //     return pathinfo($path)['extension'] ?? '';
    // }

    // /**
    //  * Get the appropriate view engine for the given path.
    //  *
    //  * @param  string  $path
    //  * @return \Illuminate\Contracts\View\Engine
    //  */
    // public function getEngineFromPath($path)
    // {
    //     return $this->engines->resolve(Blade::ENGINE_NAME);
    // }

    // /**
    //  * Create a new view instance from the given arguments.
    //  *
    //  * @param  string  $view
    //  * @param  string  $path
    //  * @param  array  $data
    //  * @return \Surgiie\Blade\File
    //  */
    // protected function viewInstance($view, $path, $data)
    // {
    //     return new File($this, $this->getEngineFromPath($path), $view, $path, $data);
    // }
}
