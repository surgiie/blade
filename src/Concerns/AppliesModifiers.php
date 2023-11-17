<?php

namespace Surgiie\Blade\Concerns;

trait AppliesModifiers
{
    /**
     * Apply the given modifiers to the content.
     *
     * @return void
     */
    public function applyModifiers(string $content, array $modifiers = [])
    {
        // if no modifiers are passed, return the content with trailing white space trimmed.
        if (empty($modifiers)) {
            return implode(PHP_EOL, array_map('rtrim', explode(PHP_EOL, $content)));
        }

        foreach (array_keys($modifiers) as $modifier) {
            if (method_exists($this, $method = 'modify'.ucfirst($modifier))) {
                return $this->$method($content, $modifiers);
            }
        }

        return $content;
    }
}
