<?php

use Surgiie\Blade\Blade;

if (! function_exists('blade')) {

    function blade(): Blade
    {
        return new Blade(...func_get_args());
    }
}
