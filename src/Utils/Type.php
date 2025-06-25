<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Utils;

/**
 * @internal
 */
final class Type
{
    public static function isStringable(mixed $value): bool
    {
        return is_scalar($value)
            || $value === null
            || (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * Filter an array to only include values that can be converted to strings
     *
     * @param  array<mixed>  $values
     * @return array<string>
     */
    public static function filterStringable(array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            if ($value === null) {
                $result[] = '';
            } elseif (is_scalar($value)) {
                $result[] = (string) $value;
            } elseif (is_object($value) && method_exists($value, '__toString')) {
                $result[] = $value->__toString();
            }
        }

        return $result;
    }

    /**
     * Normalize a value to an array and filter to only stringable values
     *
     * @param  mixed  $values  Single value or array of values
     * @return array<string>
     */
    public static function normalizeToStringableArray(mixed $values): array
    {
        if (! is_array($values)) {
            $values = [$values];
        }

        return self::filterStringable($values);
    }
}
