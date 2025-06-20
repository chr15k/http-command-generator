<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand;

use Chr15k\HttpCommand\Builder\CommandBuilder;
use Chr15k\HttpCommand\Enums\HttpMethod;

/**
 * @internal
 */
final class HttpCommand
{
    public static function url(string $url = ''): CommandBuilder
    {
        return (new CommandBuilder)->url($url);
    }

    public static function get(string $url = ''): CommandBuilder
    {
        return self::url($url)->method(HttpMethod::GET);
    }

    public static function post(string $url = ''): CommandBuilder
    {
        return self::url($url)->method(HttpMethod::POST);
    }

    public static function put(string $url = ''): CommandBuilder
    {
        return self::url($url)->method(HttpMethod::PUT);
    }

    public static function patch(string $url = ''): CommandBuilder
    {
        return self::url($url)->method(HttpMethod::PATCH);
    }

    public static function delete(string $url = ''): CommandBuilder
    {
        return self::url($url)->method(HttpMethod::DELETE);
    }

    public static function head(string $url = ''): CommandBuilder
    {
        return self::url($url)->method(HttpMethod::HEAD);
    }

    public static function options(string $url = ''): CommandBuilder
    {
        return self::url($url)->method(HttpMethod::OPTIONS);
    }
}
