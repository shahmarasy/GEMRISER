<?php

declare(strict_types=1);

namespace Gemriser\Exceptions;

use Gemriser\Http\Response;
use Psr\Http\Message\ResponseInterface;

class ValidationException extends \RuntimeException
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed')
    {
        parent::__construct($message, 422);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function render(): ResponseInterface
    {
        return Response::json(['message' => $this->getMessage(), 'errors' => $this->errors], 422);
    }
}
