<?php

namespace Surgiie\Blade\Concerns;

trait ParsesFilePath
{
    /**
     * Parse a path name to filesystem path for compile.
     * This may be a component path as well.
     */
    protected static function parseFilePath(string $path)
    {
        $computedPath = $path;

        // component paths via absolute path <x--*>
        if (str_starts_with($path, '-')) {
            $computedPath = '/'.ltrim($path, '-');
        }

        $ext = pathinfo($computedPath)['extension'] ?? '';

        $computedPath = str_replace('.', DIRECTORY_SEPARATOR, str_replace(".$ext", '', $computedPath));

        $computedPath = $ext ? "$computedPath.$ext" : $computedPath;

        return is_file($computedPath) ? $computedPath : $path;
    }
}
