<?php

namespace Surgiie\Blade\Concerns;

trait CompilesIncludes
{
    /**
     * Compile the include statements into valid PHP.
     */
    protected function compileInclude($expression)
    {
        $expression = $this->stripParentheses($expression);

        $options = array_pop($this->optionsStack);

        $options['type'] = 'include';

        $options = var_export($options, true);

        return "<?php echo \$__env->make({$expression}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']), [], $options)->render(); ?> ";
    }

    /**
     * Compile the include-if statements into valid PHP.
     */
    protected function compileIncludeIf($expression)
    {
        $expression = $this->stripParentheses($expression);

        $options = array_pop($this->optionsStack);

        $options['type'] = 'include';

        $options = var_export($options, true);

        return "<?php if (\$__env->exists({$expression})) echo \$__env->make({$expression}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']),  [], $options)->render(); ?> ";
    }

    /**
     * Compile the include-when statements into valid PHP.
     */
    protected function compileIncludeWhen($expression)
    {
        $expression = $this->stripParentheses($expression);

        $options = array_pop($this->optionsStack);

        $options['type'] = 'include';

        $options = var_export($options, true);

        return "<?php echo \$__env->renderWhen($expression, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']), [], $options); ?> ";
    }

    /**
     * Compile the include-unless statements into valid PHP.
     */
    protected function compileIncludeUnless($expression)
    {
        $expression = $this->stripParentheses($expression);

        $options = array_pop($this->optionsStack);

        $options['type'] = 'include';

        $options = var_export($options, true);

        return "<?php echo \$__env->renderUnless($expression, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']), [], $options); ?> ";
    }

    /**
     * Compile the include-first statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeFirst($expression)
    {
        $expression = $this->stripParentheses($expression);

        $options = array_pop($this->optionsStack);

        $options['type'] = 'include';

        $options = var_export($options, true);

        return "<?php echo \$__env->first({$expression}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']), [], $options)->render(); ?> ";
    }
}
