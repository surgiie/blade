<?php

namespace Surgiie\Blade;

use Illuminate\View\View;
use Surgiie\Blade\Concerns\Modifiers\ModifiesSpacing;
use Surgiie\Blade\Concerns\ModifiesRenderedContent;

class File extends View
{
    use ModifiesSpacing;

    public function render(callable $callback = null, array $modifiers = [])
    {
        return $this->modifySpacing(parent::render($callback), $modifiers);
    }
}
