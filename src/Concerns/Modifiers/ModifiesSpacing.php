<?php

namespace Surgiie\Blade\Concerns\Modifiers;

trait ModifiesSpacing
{
    /**Modify the given content using the given options. */
    protected function modifySpacing(string $content, array $options = [])
    {
        $result = [];
        $spacing = $options['spacing'] ?? false;

        foreach (explode(PHP_EOL, $content) as $line) {

            if ($spacing) {
                $line = $spacing.$line;
            }
            $result[] = rtrim($line);
        }

        return implode(PHP_EOL, $result);
    }
}
