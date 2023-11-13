<?php

namespace Surgiie\Blade;

use Illuminate\View\Factory;
use Surgiie\Blade\Concerns\Modifiers\ModifiesSpacing;

class FileFactory extends Factory
{
    use ModifiesSpacing;

    protected function normalizeName($name)
    {
        // Disable dot notation from file name.
        return $name;
    }

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

    public function renderComponent(?array $modifiers = [])
    {
        return $this->modifySpacing(parent::renderComponent(), $modifiers);
    }

    public function make($view, $data = [], $mergeData = [])
    {
        $file = parent::make($view, $data, $mergeData);

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
