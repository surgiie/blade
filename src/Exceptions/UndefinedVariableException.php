<?php

namespace Surgiie\Blade\Exceptions;

use ErrorException;

class UndefinedVariableException extends ErrorException
{
    protected string $variableName;

    public function __construct(string $message, string $variableName)
    {
        parent::__construct($message);

        $this->variableName = $variableName;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }
}
