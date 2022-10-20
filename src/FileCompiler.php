<?php

namespace Surgiie\Blade;

use Illuminate\View\Compilers\BladeCompiler;

class FileCompiler extends BladeCompiler
{
    /**
     * The directives that we should format to start of lines.
     *
     * @var array
     */
    protected $formatDirectives = [
        'foreach',
        'endforeach',
        'empty',
        'if',
        'elseif',
        'else',
        'endif',
        'forelse',
        'endforelse',
        'for',
        'endfor',
        'while',
        'endwhile',
        'switch',
        'default',
        'case',
        'endswitch',
    ];

    /**
     * Compile and rendered php code can leave behind unwanted spacing which
     * can be problematic for files where spacing has semantical meaning.
     * This function compiles known blade directives so that they are shifted
     * to the start of the line should they have leading whitespace.
     *
     * @see https://www.php.net/manual/en/language.basic-syntax.phptags.php
     */
    protected function compileStatements($value)
    {
        $keywords = implode('|', $this->formatDirectives);

        $value = preg_replace("/\\s+\@($keywords)/", "\n@$1", $value);

        return parent::compileStatements($value);
    }

    /**
     * Determine if the given view is expired.
     *
     * We'll always return true here to ensure
     * the compiler always compiles the file.
     *
     * @param  string  $path
     * @return bool
     */
    public function isExpired($path)
    {
        // ensures that compiler compiles the file always
        return true;
    }
}
