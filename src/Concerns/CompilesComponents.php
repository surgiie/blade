<?php

namespace Surgiie\Blade\Concerns;

use Illuminate\View\AnonymousComponent as BladeAnonymousComponent;
use Surgiie\Blade\ComponentTagCompiler;

trait CompilesComponents
{
    /**
     * Compile the end-component statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndComponent()
    {
        return '<?php echo $__env->renderComponent(); ?> ';
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
}
