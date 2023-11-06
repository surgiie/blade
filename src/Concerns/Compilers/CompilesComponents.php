<?php

namespace Surgiie\Blade\Concerns\Compilers;

use Illuminate\Support\Str;
use Illuminate\View\AnonymousComponent as ViewAnonymousComponent;
use Surgiie\Blade\AnonymousComponent;
use Surgiie\Blade\ComponentTagCompiler;
use Surgiie\Blade\FileCompiler;

trait CompilesComponents
{
    // use ParsesComponentFilePath;

    /**The options for components passed down from start component compile. */
    protected array $componentModifiersStack = [];

    /**
     * Compile the end-component statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndComponent()
    {
        // $modifiers = array_pop($this->modifiersStack);
        $modifiers = array_pop($this->componentModifiersStack);

        $modifiers = var_export($modifiers, true);

        return "<?php echo \$__env->renderComponent(modifiers: $modifiers); ?> ";
    }

    protected function compileComponent($expression)
    {
        $this->componentModifiersStack[] = array_pop($this->modifiersStack);

        return str_replace(ViewAnonymousComponent::class, AnonymousComponent::class, parent::compileComponent($expression));
    }


    public static function compileComponentClassOpening(string $component, string $alias, string $data, string $hash, FileCompiler $compiler)
    {

        /*[$path, $class] = ComponentTagCompiler::getComponentFilePath($cleanAlias = str_replace("'", '', $alias), $compiler->getPath());

        if (! $path && $alias) {
            $path = static::parseComponentPath($cleanAlias);

            $data = str_replace("'view' => $alias", "'view'=> '$path'", $data);
        } elseif ($path && $class == AnonymousComponent::class) {
            $data = str_replace("'view' => $alias", "'view'=> '$path'", $data);
        }

        $parts = explode(PHP_EOL, $opening = parent::compileClassComponentOpening($component, $alias, $data, $hash));

        // no alias/class means its an anonymous component.
        if (empty($class)) {
            return $opening;
        }

        array_splice($parts, 1, 0, "<?php \$__env->requireComponentClass('$class', '$path') ?>");
        return implode(PHP_EOL, $parts);*/
    }
}
