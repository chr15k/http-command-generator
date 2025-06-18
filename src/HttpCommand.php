<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand;

use Chr15k\HttpCommand\Builder\CommandBuilder;
use Chr15k\HttpCommand\Enums\HttpMethod;
use Chr15k\HttpCommand\Exceptions\InvalidHttpMethodException;
use TypeError;

/**
 * @internal
 */
final class HttpCommand
{
    /**
     * @param  array<mixed>  $arguments
     */
    public static function __callStatic(string $name, array $arguments): CommandBuilder
    {
        $method = strtoupper($name);

        if (! HttpMethod::tryFrom($method) instanceof HttpMethod) {
            throw InvalidHttpMethodException::create($method);
        }

        $url = $arguments[0] ?? '';

        if (is_string($url) === false) {
            throw new TypeError(sprintf(
                'Argument 1 passed to %s must be of type string, %s given', $name, gettype($url)
            ));
        }

        return (new CommandBuilder)
            ->url($url)
            ->method($method);
    }
}
