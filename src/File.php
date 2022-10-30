<?php

namespace Surgiie\Blade;

use Illuminate\View\View;

class File extends View
{
    /**
     * Get the evaluated contents of the compiled file.
     *
     * @return string
     */
    protected function getContents()
    {
        // remove the trailing space placeholder that we added to work around
        // php's closing tag encompassing trailing/next line.
        return str_replace(' __@BLADE_SPACE_ADDED@__', '', parent::getContents());
    }
}
