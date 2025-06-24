<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Utils;

/**
 * @internal
 */
final class Type
{
    public static function isStringCastable(mixed $value): bool
    {
        return is_scalar($value)
            || $value === null
            || (is_object($value) && method_exists($value, '__toString'));
    }
}
