<?php

namespace Surgiie\Blade;

use Illuminate\View\Compilers\BladeCompiler;
use Surgiie\Blade\Concerns\Compilers\CompilesComponents;
use Surgiie\Blade\Concerns\Compilers\CompilesIncludes;

class FileCompiler extends BladeCompiler
{
    use CompilesComponents, CompilesIncludes;

    protected array $modifiersStack = [];

    protected function compileStatements($value)
    {
        return preg_replace_callback(
            '/\h*(?:\#\#BEGIN-\COMPONENT\-CLASS\#\#)?\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', function ($match) {
                // capture the spacing next to the directive so we can modify the content later so it's indented properly
                $spacingTotal = strlen($match[0]) - strlen(ltrim($match[0]));

                $spacing = str_repeat(' ', $spacingTotal);

                $match[0] = ltrim($match[0]);

                $this->modifiersStack[] = ['spacing' => $spacing];

                return $this->compileStatement($match);

            }, $value
        );
    }

    protected function compileComponentTags($value): string
    {
        if (! $this->compilesComponentTags) {
            return $value;
        }

        return (new ComponentTagCompiler(
            $this->path, $this->classComponentAliases, $this->classComponentNamespaces, $this
        ))->compile($value);
    }
}
