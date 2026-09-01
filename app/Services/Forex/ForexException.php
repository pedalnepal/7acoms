<?php

namespace App\Services\Forex;

use RuntimeException;

class ForexException extends RuntimeException
{
    /** @var array<string, mixed> */
    public array $context = [];

    /**
     * @param  array<string, mixed>  $context
     */
    public static function withContext(string $message, array $context = []): self
    {
        $e          = new self($message);
        $e->context = $context;

        return $e;
    }
}
