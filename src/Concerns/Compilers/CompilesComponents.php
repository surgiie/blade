<?php

namespace Surgiie\Blade\Concerns\Compilers;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Surgiie\Blade\Exceptions\FileException;

trait CompilesComponents
{
    protected ?string $lastComponent = null;

    protected array $componentModifiersStack = [];

    protected function compileEndComponent()
    {
        $modifiers = array_pop($this->componentModifiersStack);

        $modifiers = var_export($modifiers, true);

        return "<?php echo \$__env->renderComponent(modifiers: $modifiers); ?> ";
    }

    protected function compileComponent($expression)
    {
        // propagate the modifiers stack to the component compilation functions
        $this->componentModifiersStack[] = array_pop($this->modifiersStack);

        [$component, $alias, $data] = str_contains($expression, ',')
                    ? array_map('trim', explode(',', trim($expression, '()'), 3)) + ['', '', '']
                    : [trim($expression, '()'), '', ''];

        $component = trim($component, '\'"');

        $hash = static::newComponentHash($component);

        if (class_exists($component) || Str::contains($component, ['::class', '\\'])) {
            return static::compileClassComponentOpening($component, $alias, $data, $hash);
        }

        return "<?php \$__env->startComponent{$expression}; ?>";
    }
}
