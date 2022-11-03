<?php

namespace Surgiie\Blade\Concerns;

trait ModifiesRenderedContent
{
    /**Modify the given content using the given options. */
    protected function modifyRenderedContent(string $content, array $options = [])
    {
        $result = [];
        $lines = explode(PHP_EOL, $content);

        $spacing = $options['spacing'] ?? false;

        foreach ($lines as $line) {
            if ($spacing) {
                $line = $spacing.$line;
            }

            $result[] = rtrim($line);
        }

        return implode(PHP_EOL, $result);
    }
}
