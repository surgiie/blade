<?php

namespace Surgiie\Blade;

use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\PhpEngine;
use Surgiie\Blade\Exceptions\FileException;
use Throwable;

class FileCompilerEngine extends CompilerEngine
{
    /**
     * Require the file path and return the evaluated contents.
     *
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    protected function evaluatePath($path, $data)
    {
        $obLevel = ob_get_level();

        ob_start();

        try {
            $this->files->getRequire($path, $data);
        } catch (Throwable $e) {
            $this->handleViewException($e, $obLevel);
        }

        // Parent class uses ltrim but rtrim output buffer instead.
        // This helps with preserving spacing/indentation from
        // the compiled file which we want to preserve, especially
        // in files where nesting is important, such as yaml.
        return rtrim(ob_get_clean());
    }

    /**
     * Handle a file exception.
     *
     * @param  int  $obLevel
     * @return void
     */
    protected function handleViewException(Throwable $e, $obLevel)
    {
        $e = new FileException($this->getExceptionMessage($e), 0, 1, $e->getFile(), $e->getLine(), $e);

        PhpEngine::handleViewException($e, $obLevel);
    }

    /**
     * Get the exception message for an exception.
     */
    protected function getExceptionMessage(Throwable $e): string
    {
        $msg = $e->getMessage();

        return $msg.' (File: '.realpath(end($this->lastCompiled)).')';
    }
}
