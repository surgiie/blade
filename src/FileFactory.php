<?php

namespace Surgiie\Blade;

use Illuminate\View\Factory;
use Surgiie\Blade\Concerns\AppliesModifiers;
use Surgiie\Blade\Concerns\Modifiers\ModifiesSpacing;

class FileFactory extends Factory
{
    use AppliesModifiers, ModifiesSpacing;

    /**
     * Normalizes the given file name.
     *
     * @param  string  $name
     * @return string
     */
    protected function normalizeName($name)
    {
        // Disable dot notation from file name.
        return $name;
    }

    /**
     * Render the given file when the condition is true.
     *
     * @param  bool  $condition
     * @param  string  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @param  array  $modifiers
     * @return string
     */
    public function renderWhen($condition, $view, $data = [], $mergeData = [], $modifiers = [])
    {
        if (! $condition) {
            return '';
        }

        $file = $this->make($view, $this->parseData($data), $mergeData);

        return $file->render(modifiers: $modifiers);
    }

    /**
     * Render the given file unless the condition is true.
     *
     * @param  bool  $condition
     * @param  string  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @param  array  $modifiers
     * @return string
     */
    public function renderUnless($condition, $view, $data = [], $mergeData = [], $modifiers = [])
    {
        return $this->renderWhen(! $condition, $view, $data, $mergeData, $modifiers);
    }

    /**
     * Render a component with the given modifiers.
     *
     * @return string
     */
    public function renderComponent(?array $modifiers = [])
    {
        return $this->applyModifiers(parent::renderComponent(), $modifiers);
    }

    /**
     * Make a new \Surgiie\Blade\File instance.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return \Surgiie\Blade\File
     */
    public function make($view, $data = [], $mergeData = [])
    {
        return parent::make($view, $data, $mergeData);
    }

    /**
     * Get the extension used by the file at the given path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getExtension($path)
    {
        return pathinfo($path)['extension'] ?? '';
    }

    /**
     * Resolve the engine to use from the given path.
     *
     * @param  string  $path
     * @return \Illuminate\Contracts\View\Engine
     */
    public function getEngineFromPath($path)
    {
        // We always use the Blade engine.
        return $this->engines->resolve(Blade::ENGINE_NAME);
    }

    /**
     * Return the class instance to use for rendering files.
     *
     * @param  string  $view
     * @param  string  $path
     * @param  array  $data
     * @return \Surgiie\Blade\File
     */
    protected function viewInstance($view, $path, $data)
    {
        return new File($this, $this->getEngineFromPath($path), $view, $path, $data);
    }
}
