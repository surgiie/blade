<?php

namespace Surgiie\Blade;

use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Surgiie\Blade\Concerns\CompilesComponents;
use Surgiie\Blade\Concerns\CompilesIncludes;

class FileCompiler extends BladeCompiler
{
    use CompilesIncludes, CompilesComponents;

    /**The options stack for each statement.*/
    protected array $optionsStack = [];

    /**
     * Compile Blade statements that start with "@".
     *
     * @param  string  $value
     * @return string
     */
    protected function compileStatements($value)
    {
        $compiled = preg_replace_callback(
            '/\h*(?:\#\#BEGIN-\COMPONENT\-CLASS\#\#)?\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', function ($match) {
                $spacing = strlen($match[0]) - strlen(ltrim($match[0]));
                $spacing = strlen(str_repeat(' ', $spacing));

                $match[0] = ltrim($match[0]);

                $this->optionsStack[] = ['spacing' => $spacing];

                return $this->compileStatement($match);
            }, $value
        );

        $result = [];
        $lines = explode(PHP_EOL, $compiled);

        foreach ($lines as $index => $line) {
            $cleanLine = trim($line);
            $nextLine = trim($lines[$index + 1] ?? '');
            // Handle adding a space next to close tags so that we prevent the next traling line from
            // being encompassed, which is a side effect of close tag compilation. Howver.
            // on some cases we want to avoid doing this such as for loops, for some reason, a new line is
            // embedded when echoing items from a @foreach/@while.
            // @see https://www.php.net/manual/en/language.basic-syntax.instruction-separation.php
            if (
                $cleanLine &&
                ! Str::startsWith($cleanLine, ['<?php $__currentLoopData', '<?php while', 'case (']) &&
                Str::endsWith($cleanLine, ['?>']) &&
                ! Str::startsWith($nextLine, ['<?php'])
            ) {
                $line = $line.' ';
            }
            $result[] = $line;
        }

        $result = implode(PHP_EOL, $result);

        return rtrim($result);
    }

    /**Determine if the file is expired.*/
    public function isExpired($path)
    {
        return true;
        // return Blade::shouldUseCachedCompiledFiles() == false ? true : parent::isExpired($path);
    }

    /**
     * Compile the component tags.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileComponentTags($value)
    {
        if (! $this->compilesComponentTags) {
            return $value;
        }

        return (new ComponentTagCompiler(
            $this->path, $this->classComponentAliases, $this->classComponentNamespaces, $this
        ))->compile($value);
    }
}
