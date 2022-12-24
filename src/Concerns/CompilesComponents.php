<?php

namespace Surgiie\Blade\Concerns;

use Illuminate\Support\Str;
use Surgiie\Blade\FileCompiler;
use Surgiie\Blade\AnonymousComponent;
use Surgiie\Blade\ComponentTagCompiler;
use Surgiie\Blade\Concerns\ParsesComponentFilePath;

trait CompilesComponents
{
    use ParsesComponentFilePath;
    /**The options for components passed down from start component compile. */
    protected array $componentOptionsStack = [];

    /**
     * Compile the end-component statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndComponent()
    {
        $options = array_pop($this->componentOptionsStack);

        $options['type'] = 'component';

        $options = var_export($options, true);

        return "<?php echo \$__env->renderComponent($options); ?> ";
    }

    /**
     * Compile the component statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileComponent($expression)
    {
        [$component, $alias, $data] = str_contains($expression, ',')
        ? array_map('trim', explode(',', trim($expression, '()'), 3)) + ['', '', '']
        : [trim($expression, '()'), '', ''];
        $hash = static::newComponentHash($component);

        if (Str::contains($component, ['::class', '\\'])) {
            $compiled = static::compileComponentClassOpening($component, $alias, $data, $hash, $this);
        } else {
            $compiled = "<?php \$__env->startComponent{$expression}; ?>";
        }
        // pass the options from stack down since render is done in `compileEndComponent`
        $this->componentOptionsStack[] = array_pop($this->optionsStack);

        return $compiled;
    }

    /**
     * Compile a class component opening.
     *
     * @return string
     */
    public static function compileComponentClassOpening(string $component, string $alias, string $data, string $hash, FileCompiler $compiler)
    {

        $separator = DIRECTORY_SEPARATOR;
        [$path, $class] = ComponentTagCompiler::getComponentFilePath($cleanAlias = str_replace("'", '', $alias), $compiler->getPath());
        
        if (!$path && $alias) {
            $path = static::parseComponentPath($cleanAlias);

            $data = str_replace("'view' => $alias", "'view'=> '$path'", $data);
        }
        else if ($path && $class == AnonymousComponent::class) {
            $data = str_replace("'view' => $alias", "'view'=> '$path'", $data);
        }
        
        $parts = explode(PHP_EOL, $opening = parent::compileClassComponentOpening($component, $alias, $data, $hash));
        
        // no alias/class means its an anonymous component.
        if (empty($class)) {
            return $opening;
        }
        
        array_splice($parts, 1, 0, "<?php \$__env->requireComponentClass('$class', '$path') ?>");

        return implode(PHP_EOL, $parts);
    }
}
