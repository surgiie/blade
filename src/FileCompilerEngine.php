<?php

namespace Surgiie\Blade;

use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\PhpEngine;
use Surgiie\Blade\Exceptions\FileException;
use Throwable;

class FileCompilerEngine extends CompilerEngine
{
    /**
     * Overwritten to not ltrim but to rtrim output buffer.
     * This helps with preserving spacing/indentation from
     * the compiled file which we want to preserve, especially
     * in files where nesting is important, such as yaml.
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

        return rtrim(ob_get_clean());
    }

    protected function handleViewException(Throwable $e, $obLevel)
    {
        $e = new FileException($this->getMessage($e), 0, 1, $e->getFile(), $e->getLine(), $e);

        PhpEngine::handleViewException($e, $obLevel);
    }

    protected function getMessage(Throwable $e): string
    {
        $msg = $e->getMessage();

        return $msg.' (File: '.realpath(end($this->lastCompiled)).')';
    }
}
