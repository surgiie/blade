<?php

namespace Surgiie\Blade;

use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\PhpEngine;
use Throwable;

class FileCompilerEngine extends CompilerEngine
{
    /**
     * Overwritten to not ltrim but to rtrim outbutput buffer.
     * This assists with preserving spacing/indentation from
     * the compiled file which is important to preserve so
     * we dont end up with shifted content in a file.
     *
     * @param string $path
     * @param array $data
     * @return void
     */
    protected function evaluatePath($path, $data)
    {
        $obLevel = ob_get_level();

        ob_start();

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            $this->files->getRequire($path, $data);
        } catch (Throwable $e) {
            $this->handleViewException($e, $obLevel);
        }

        return rtrim(ob_get_clean());
    }

    /**
     * Handle a view render exception.
     *
     * @param Throwable $e
     * @param int $obLevel
     * @return void
     */
    protected function handleViewException(Throwable $e, $obLevel)
    {
        $class = get_class($e);
        PhpEngine::handleViewException(new $class($this->getMessage($e)), $obLevel);
    }

    /**
     * Get a formatted exception message for a compile error.
     *
     * @param Throwable $e
     * @return string
     */
    protected function getMessage(Throwable $e): string
    {
        $msg = $e->getMessage();

        return $msg.' (File: '.realpath(end($this->lastCompiled)).')';
    }
}
