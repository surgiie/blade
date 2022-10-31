<?php

namespace Surgiie\Blade;

use Illuminate\View\View;

class File extends View
{
    /**Rendering options. */
    protected array $renderOptions = [];

    /**Set the rendering options.*/
    public function setRenderOptions(array $options = [])
    {
        $this->renderOptions = $options;
    }

    /**
     * Get the string contents of the view.
     *
     * @throws \Throwable
     */
    public function render(callable $callback = null)
    {
        $result = [];
        $lines = explode(PHP_EOL, parent::render($callback));

        $spacing = $this->renderOptions['spacing'] ?? false;

        foreach ($lines as $line) {
            if ($spacing) {
                $line = $spacing.$line;
            }

            $result[] = $line;
        }

        return implode(PHP_EOL, $result);
    }
}
