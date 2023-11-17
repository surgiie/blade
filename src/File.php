<?php

namespace Surgiie\Blade;

use Illuminate\View\View;
use Surgiie\Blade\Concerns\AppliesModifiers;
use Surgiie\Blade\Concerns\Modifiers\ModifiesSpacing;

class File extends View
{
    use AppliesModifiers, ModifiesSpacing;

    /**
     * Render the file with the given variables and apply any modifiers.
     *
     * @return void
     */
    public function render(callable $callback = null, array $modifiers = [])
    {
        return $this->applyModifiers(parent::render($callback), $modifiers);
    }
}
