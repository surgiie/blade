<?php

namespace Surgiie\Blade\Concerns;

trait ParsesFilePath
{
    /**Parse a path name to filesystem path. */
    protected function parseFilePath($path)
    {
        if (str_starts_with($path, '-')) {
            $path = '/'.ltrim($path, '-');
        }

        $ext = pathinfo($path)['extension'] ?? '';

        $path = str_replace('.', DIRECTORY_SEPARATOR, str_replace(".$ext", '', $path));

        $path = $ext ? "$path.$ext" : $path;

        return $path;
    }
}
