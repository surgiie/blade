<?php

namespace Surgiie\Blade\Concerns\Compilers;

trait CompilesIncludes
{
    /**
     * Compile the include statements into valid PHP.
     * @TODO - test
     */
    protected function compileInclude($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace("render()", "render(modifiers: $modifiers)", parent::compileInclude($expression));
    }

    /**
     * Compile the include-if statements into valid PHP.
     * @TODO - test
     *
     */
    protected function compileIncludeIf($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace("render()", "render(modifiers: $modifiers)", parent::compileIncludeIf($expression));
    }

    /**
     * Compile the include-when statements into valid PHP.
     * @TODO - test
     */
    protected function compileIncludeWhen($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace(");", ", modifiers: $modifiers);", parent::compileIncludeWhen($expression));
    }

    /**
     * Compile the include-unless statements into valid PHP.
     * @TODO - test
     *
     */
    protected function compileIncludeUnless($expression)
    {
        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace(");", ", modifiers: $modifiers);", parent::compileIncludeUnless($expression));
    }

    /**
     * Compile the include-first statements into valid PHP.
     * @TODO - test
     */
    protected function compileIncludeFirst($expression)
    {

        $modifiers = var_export(array_pop($this->modifiersStack), true);

        return str_replace("render()", "render(modifiers: $modifiers)", parent::compileIncludeFirst($expression));
    }
}
