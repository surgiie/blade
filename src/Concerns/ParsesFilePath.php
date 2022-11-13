<?php

namespace Surgiie\Blade\Concerns;

trait ParsesFilePath
{
    /**Parse a path name to filesystem path. */
    protected static function parseFilePath(string $path)
    {
        $computedPath = $path;
        if (str_starts_with($path, '-')) {
            $computedPath = '/'.ltrim($path, '-');
        }

        $ext = pathinfo($computedPath)['extension'] ?? '';

        $computedPath = str_replace('.', DIRECTORY_SEPARATOR, str_replace(".$ext", '', $computedPath));

        $computedPath = $ext ? "$computedPath.$ext" : $computedPath;

        return is_file($computedPath) ? $computedPath : $path;
    }
}
