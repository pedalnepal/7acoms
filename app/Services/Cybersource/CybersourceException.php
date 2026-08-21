<?php

namespace App\Services\Cybersource;

use RuntimeException;

class CybersourceException extends RuntimeException
{
    /** @var array<string, mixed> */
    public array $context = [];

    /**
     * @param  array<string, mixed>  $context
     */
    public static function withContext(string $message, array $context): self
    {
        $exception = new self($message);
        $exception->context = $context;

        return $exception;
    }
}
