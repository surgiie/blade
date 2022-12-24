<?php

namespace Surgiie\Blade\Concerns;

trait ParsesFilePath
{
    /**
     * Parse a path name to filesystem path for compile.
     * This may be a component path as well.
     */
    protected static function parseFilePath(string $path, bool $isComponentPath = false)
    {
        // component paths via absolute path <x--*>
        if (str_starts_with($path, '-') && $isComponentPath) {
            $path = '/'.ltrim($path, '-');
        }
        
        $ext = pathinfo($path)['extension'] ?? '';
        
        $path = str_replace(".$ext", '', $path);

        if($isComponentPath){
            $path = str_replace('.', DIRECTORY_SEPARATOR, $path);
        }
        
        $path = $ext ? "$path.$ext" : $path;

        return $path;
    }
}
