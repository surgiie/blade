<?php

namespace Surgiie\Blade\Concerns\Compilers;

trait CompilesIncludes
{
    /**
     * Compile the @include directive into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace('render()', "render(modifiers: $modifiers)", parent::compileInclude($expression)).PHP_EOL;
    }

    /**
     * Compile the @includeIf directive into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeIf($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace('render()', "render(modifiers: $modifiers)", parent::compileIncludeIf($expression)).PHP_EOL;
    }

    /**
     * Compile the @includeWhen directive into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeWhen($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace(');', ", modifiers: $modifiers);", parent::compileIncludeWhen($expression)).PHP_EOL;
    }

    /**
     * Compile the @includeUnless directive into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeUnless($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace(');', ", modifiers: $modifiers);", parent::compileIncludeUnless($expression)).PHP_EOL;
    }

    /**
     * Compile the @includeFirst directive into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeFirst($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace('render()', "render(modifiers: $modifiers)", parent::compileIncludeFirst($expression)).PHP_EOL;
    }
}
