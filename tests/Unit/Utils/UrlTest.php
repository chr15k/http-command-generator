<?php

declare(strict_types=1);

use Chr15k\HttpCommand\Utils\Url;

it('appends query parameters to a URL', function (
    string $url,
    array $params,
    string $expected,
    bool $encode = true
): void {
    expect(Url::mergeQuery($url, $params, $encode))->toBe($expected);
})->with(
    [
        'empty query' => [
            'url' => 'https://example.com/api/resource',
            'params' => [],
            'expected' => 'https://example.com/api/resource',
        ],
        'no existing query' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key1' => 'value1'],
            'expected' => 'https://example.com/api/resource?key1=value1',
        ],
        'existing query' => [
            'url' => 'https://example.com/api/resource?existing=value',
            'params' => ['key1' => 'value1'],
            'expected' => 'https://example.com/api/resource?existing=value&key1=value1',
        ],
        'multiple existing parameters' => [
            'url' => 'https://example.com/api/resource?existing=value&another=123',
            'params' => ['key1' => 'value1', 'key2' => 'value2'],
            'expected' => 'https://example.com/api/resource?existing=value&another=123&key1=value1&key2=value2',
        ],
        'with fragment' => [
            'url' => 'https://example.com/api/resource#section',
            'params' => ['key1' => 'value1'],
            'expected' => 'https://example.com/api/resource?key1=value1#section',
        ],
        'with port' => [
            'url' => 'https://example.com:8080/api/resource',
            'params' => ['key1' => 'value1'],
            'expected' => 'https://example.com:8080/api/resource?key1=value1',
        ],
        'with special characters' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key with spaces' => 'value with spaces', 'key&with&specials' => 'value&with&specials'],
            'expected' => 'https://example.com/api/resource?key%20with%20spaces=value%20with%20spaces&key%26with%26specials=value%26with%26specials',
        ],
        'with encoding disabled' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key with spaces' => 'value with spaces', 'key&with&specials' => 'value&with&specials'],
            'expected' => 'https://example.com/api/resource?key with spaces=value with spaces&key&with&specials=value&with&specials',
            'encode' => false,
        ],
        'with numeric values' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key1' => '123', 'key2' => '456.78', 'key3' => '0'],
            'expected' => 'https://example.com/api/resource?key1=123&key2=456.78&key3=0',
        ],
        'with boolean values' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key1' => 'true', 'key2' => 'false', 'key3' => '1', 'key4' => '0'],
            'expected' => 'https://example.com/api/resource?key1=true&key2=false&key3=1&key4=0',
        ],
        'with null values' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key1' => null, 'key2' => 'value2', 'key3' => ''],
            'expected' => 'https://example.com/api/resource?key1=&key2=value2&key3=',
        ],
        'with empty string values' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key1' => '', 'key2' => 'value2', 'key3' => ''],
            'expected' => 'https://example.com/api/resource?key1=&key2=value2&key3=',
        ],
        'with multi value keys' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key1' => ['value1', 'value2']],
            'expected' => 'https://example.com/api/resource?key1=value1&key1=value2',
        ],
        'with existing duplicate keys' => [
            'url' => 'https://example.com/api/resource?key1=value1&key1=value2',
            'params' => ['key1' => 'value3'],
            'expected' => 'https://example.com/api/resource?key1=value1&key1=value2&key1=value3',
        ],
        'with mixed types' => [
            'url' => 'https://example.com/api/resource',
            'params' => ['key1' => 'value1', 'key2' => 123, 'key3' => true],
            'expected' => 'https://example.com/api/resource?key1=value1&key2=123&key3=1',
        ],
        'with malformed URL' => [
            'url' => 'http://',
            'params' => ['key1' => 'value1'],
            'expected' => 'http://?key1=value1',
        ],
        'with URL containing only query' => [
            'url' => '?existing=value',
            'params' => ['key1' => 'value1'],
            'expected' => '?existing=value&key1=value1',
        ],
        'with URL containing only fragment' => [
            'url' => '#section',
            'params' => ['key1' => 'value1'],
            'expected' => '?key1=value1#section',
        ],
        'with empty URL' => [
            'url' => '',
            'params' => ['key1' => 'value1'],
            'expected' => '?key1=value1',
        ],
    ]
);
