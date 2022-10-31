<?php

namespace Surgiie\Blade;

use Illuminate\View\View;
use Surgiie\Blade\Concerns\ModifiesRenderedContent;

class File extends View
{
    use ModifiesRenderedContent;

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
        return $this->modifyRenderedContent(parent::render($callback), $this->renderOptions);
    }
}
