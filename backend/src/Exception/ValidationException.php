<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationException extends HttpException
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed', \Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct(422, $message, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
