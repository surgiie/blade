<?php

namespace Surgiie\Blade;

use Illuminate\View\Compilers\BladeCompiler;
use Surgiie\Blade\Concerns\CompilesComponents;
use Surgiie\Blade\Concerns\CompilesIncludes;

class FileCompiler extends BladeCompiler
{
    // use CompilesIncludes, CompilesComponents;

    /**The options stack for each statement.*/
    protected array $optionsStack = [];

    /**
     * Compile Blade statements that start with "@".
     */
    /*protected function compileStatements($value)
    {
        return preg_replace_callback(
          '/\h*(?:\#\#BEGIN-\COMPONENT\-CLASS\#\#)?\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', function ($match) {
                $spacingTotal = strlen($match[0]) - strlen(ltrim($match[0]));

                $spacing = str_repeat(' ', $spacingTotal);

                $match[0] = ltrim($match[0]);

                $this->optionsStack[] = ['spacing' => $spacing];


                return $this->compileStatement($match);
            }, $value
        );
    }*/

    // /**
    //  * Compile the component tags.
    //  *
    //  * @param  string  $value
    //  */
    // protected function compileComponentTags($value): string
    // {
    //     if (! $this->compilesComponentTags) {
    //         return $value;
    //     }

    //     return (new ComponentTagCompiler(
    //         $this->path, $this->classComponentAliases, $this->classComponentNamespaces, $this
    //     ))->compile($value);
    // }
}
