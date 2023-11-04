<?php

namespace Surgiie\Blade;

use Illuminate\View\View;
use Surgiie\Blade\Concerns\ModifiesRenderedContent;

class File extends View
{
    // use ModifiesRenderedContent;

    // /**Rendering options. */
    // protected array $renderOptions = [];

    // /**
    //  * Set the rendering options.
    //  *
    //  * @return void
    //  */
    // public function setRenderOptions(array $options = [])
    // {
    //     $this->renderOptions = $options;
    // }

    public function render(callable $callback = null)
    {
        return parent::render($callback);
        // return $this->modifyRenderedContent(parent::render($callback), $this->renderOptions);
    }
}
