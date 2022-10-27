<?php

namespace Surgiie\Blade;

use Illuminate\View\FileViewFinder;
use InvalidArgumentException;

class FileFinder extends FileViewFinder
{
    /**
     * The file extensions.
     */
    protected $extensions = [];

    /**
     * Set the active file paths.
     */
    public function setPaths($paths)
    {
        $this->paths = array_map([$this, 'resolvePath'], $paths);

        return $this;
    }

    /**
     * Overwritten to disable dot notation.
     */
    protected function getPossibleViewFiles($name)
    {
        // allows includes/components to be rendered on the fly if no extension is available for the file.
        $ext = pathinfo($name)['extension'] ?? '';
        if ($ext) {
            $this->addExtension($ext);
        }

        return array_map(function ($extension) use ($name, $ext) {
            if (empty($extension) || $ext) {
                return $name;
            }

            return $name.'.'.$extension;
        }, $this->extensions);
    }

    /**
     * Find the given view in the list of paths.
     */
    protected function findInPaths($name, $paths)
    {
        try {
            return parent::findInPaths($name, $paths);
        } catch (InvalidArgumentException) {
            if (file_exists($name)) {
                return $name;
            }
            throw new InvalidArgumentException("File [{$name}] not found.");
        }
    }
}
