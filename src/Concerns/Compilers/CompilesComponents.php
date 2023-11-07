<?php

namespace Surgiie\Blade\Concerns\Compilers;

use Illuminate\View\AnonymousComponent as ViewAnonymousComponent;
use Surgiie\Blade\AnonymousComponent;

trait CompilesComponents
{
    protected array $componentModifiersStack = [];

    protected function compileEndComponent()
    {
        $modifiers = array_pop($this->componentModifiersStack);

        $modifiers = var_export($modifiers, true);

        return "<?php echo \$__env->renderComponent(modifiers: $modifiers); ?> ";
    }

    protected function compileComponent($expression)
    {
        $this->componentModifiersStack[] = array_pop($this->modifiersStack);

        return str_replace(ViewAnonymousComponent::class, AnonymousComponent::class, parent::compileComponent($expression));
    }
}
