<?php

namespace Surgiie\Blade;

use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Illuminate\View\Compilers\BladeCompiler;

class FileCompiler extends BladeCompiler
{
    /**
     * Compile the contents of the file.
     */
    protected function compileStatements($value)
    {
        $result = [];

        $lines = explode(PHP_EOL, $value);
        foreach ($lines as $line) {
            // certain directives will get a space added to prevent the next line from being
            // merged to the end of the line, which is a side effect from php closing tag compilation.
            $line = preg_replace('/(?<!@)@(include|endComponent)(.+)(?!\s+\n)$/', '@$1$2 __@BLADE_SPACE_ADDED@__', $line);
            $line = preg_replace('/(?<!@)@endcomponent(?!\s+\n)$/', '@endcomponent __@BLADE_SPACE_ADDED@__', $line);
            // lines that have a @ directive indented, should be moved to start of line
            // this prevents the compiled tag from pushing content further in then where it
            // actually is in the file being compiled.
            $line = preg_replace("/^\s+(?<!@)@([^@]+)/", '@$1', $line);

            $result[] = $line;
        }

        return parent::compileStatements(implode(PHP_EOL, $result));
    }

    /**Determine if the file is expired.*/
    public function isExpired($path)
    {
        return true;

        return Blade::shouldUseCachedCompiledFiles() == false ? true : parent::isExpired($path);
    }

    /**
     * Compile a class component opening.
     *
     * @return string
     */
    public static function compileClassComponentOpening(string $component, string $alias, string $data, string $hash)
    {
        if (str_replace("'", '', $component) == BladeAnonymousComponent::class) {
            $component = "'".AnonymousComponent::class."'";
        }

        $parts = explode(PHP_EOL, $opening = parent::compileClassComponentOpening($component, $alias, $data, $hash));
        [$path, $class] = ComponentTagCompiler::getComponentFilePath(str_replace("'", '', $alias));

        // no alias/class means its an anonymous component.
        if (empty($class)) {
            return $opening;
        }

        array_splice($parts, 1, 0, "<?php \$__env->requireComponentClass('$class', '$path') ?>");

        return implode(PHP_EOL, $parts);
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
