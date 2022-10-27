<?php

namespace Surgiie\Blade;

use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
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
        'component',
        'endcomponent',
        'empty',
        'slot',
        'endslot',
        'php',
        'endphp',
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
     * to the start of the line should they have leading whitespace. This
     * keeps the compiled content from shifting spaces and ending up in
     * places where it shouldnt be.
     *
     * @see https://www.php.net/manual/en/language.basic-syntax.phptags.php
     */
    protected function compileStatements($value)
    {
        $keywords = implode('|', $this->formatDirectives);
        // move all @ directives that are spaced/tabbed in to the start of the
        // file this helps preserve the location of the content after compile
        $value = preg_replace("/\\s+\@($keywords)/", PHP_EOL.'@$1', $value);
        // add new line @endcomponent to ensure the next line isnt merged to the last line of component file.
        $value = preg_replace('/@(endcomponent)/', '@$1'.PHP_EOL, $value);
        // and a new line after @endComponentClass so the next line doesnt get merged to end of component file either.
        $value = preg_replace('/@(endComponentClass)(.*)/', '@$1'.PHP_EOL, $value);

        $value = preg_replace('/@include(.*)/', '@include$1'.PHP_EOL, $value);

        return parent::compileStatements($value);
    }

    /**
     * Compile a class component opening.
     *
     * @param  string  $component
     * @param  string  $alias
     * @param  string  $data
     * @param  string  $hash
     * @return string
     */
    public static function compileClassComponentOpening(string $component, string $alias, string $data, string $hash)
    {
        if ($component == BladeAnonymousComponent::class) {
            $component = AnonymousComponent::class;
        }

        return parent::compileClassComponentOpening($component, $alias, $data, $hash);
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
        return true;
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
