<?php

namespace Surgiie\Blade\Concerns;

trait ParsesComponentFilePath
{
    /**
     * Parse a path name to filesystem path for compile.
     * This may be a component path as well.
     */
    protected static function parseComponentPath(string $path)
    {
        // component paths via absolute path <x--*>
        if (str_starts_with($path, '-')) {
            $path = '/'.ltrim($path, '-');
        }

        $ext = pathinfo($path)['extension'] ?? '';

        $path = str_replace(".$ext", '', $path);

        $path = str_replace('.', DIRECTORY_SEPARATOR, $path);

        $path = $ext ? "$path.$ext" : $path;

        return $path;
    }
}
