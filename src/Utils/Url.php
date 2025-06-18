<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Utils;

use Chr15k\HttpCommand\Collections\HttpParameterCollection;

/**
 * @internal
 */
final readonly class Url
{
    /**
     * @return false|array{
     *     scheme?: string,
     *     host?: string,
     *     port?: int<0, 65535>,
     *     user?: string,
     *     pass?: string,
     *     path?: string,
     *     query?: string,
     *     fragment?: string
     * }
     */
    public static function parse(string $url): false|array
    {
        $parsed = parse_url(url: $url);

        if (is_array(value: $parsed)) {
            return $parsed;
        }

        return false;
    }

    /**
     * @param  array<string, array<int, string>|string>  $params
     */
    public static function mergeQuery(
        string $url,
        array $params,
        bool $encode = true
    ): string {
        $new = new HttpParameterCollection;
        $new = $new->merge(params: $params);

        if ($new->isEmpty()) {
            return $url;
        }

        $parts = self::parse(url: $url);

        $existing = new HttpParameterCollection;
        if (isset($parts['query'])) {
            $existing = $existing->mergeFromQueryString(query: $parts['query']);
        }

        $updated = $existing->merge(params: $new->all());
        $updatedQueryString = $updated->toQueryString(encode: $encode);

        // We still want to output the URL even if parse_url() fails, so we handle that case.
        if ($parts === false) {
            return $url.'?'.$updatedQueryString;
        }

        return vsprintf(
            format: '%s%s%s%s?%s%s',
            values: [
                isset($parts['scheme']) ? $parts['scheme'].'://' : '',
                $parts['host'] ?? '',
                isset($parts['port']) ? ':'.$parts['port'] : '',
                $parts['path'] ?? '',
                $updatedQueryString,
                isset($parts['fragment']) ? '#'.$parts['fragment'] : '',
            ]
        );
    }

    /**
     * @param  array<string, array<int, string>|string>  $params
     */
    public static function buildQuery(array $params, bool $encode = true): string
    {
        return (new HttpParameterCollection)
            ->merge(params: $params)
            ->toQueryString(encode: $encode);
    }
}
