<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Exceptions;

use InvalidArgumentException;

/**
 * @internal
 */
final class InvalidHttpMethodException extends InvalidArgumentException
{
    public static function create(string $method): self
    {
        return new self(sprintf('Invalid HTTP method: %s', $method));
    }
}
