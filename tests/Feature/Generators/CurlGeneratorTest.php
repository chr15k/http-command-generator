<?php

declare(strict_types=1);

use Chr15k\HttpCliGenerator\DataTransfer\Auth\BasicAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BearerTokenData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\DigestAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\RequestBodyData;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Chr15k\HttpCliGenerator\Enums\BodyType;
use Chr15k\HttpCliGenerator\Generators\CurlGenerator;

test('curl generator no auth output', function (array $data): void {

    $requestData = new RequestData(
        url: $data['url'],
        method: $data['method'],
        headers: $data['headers'],
        body: new RequestBodyData(
            type: $data['body']['type'] ?? BodyType::NONE,
            data: (array) $data['body']['data'] ?? [],
        ),
        auth: $data['auth']
    );

    $output = CurlGenerator::generate($requestData);

    expect($output)->toBe($data['expected']);
})->with([
    'curl get request' => [
        'data' => [
            'url' => 'https://example.com',
            'method' => 'GET',
            'headers' => [],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => null,
            'expected' => "curl --location --request GET 'https://example.com'",
        ],
    ],
    'curl post request with body' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['type' => BodyType::RAW_JSON, 'data' => '{"key":"value"}'],
            'auth' => null,
            'expected' => "curl --location --request POST 'https://example.com/api' --header \"Content-Type: application/json\" --header \"Accept: application/json\" --data '{\"key\":\"value\"}'",
        ],
    ],
    'curl post request with form data' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'body' => ['type' => BodyType::FORM, 'data' => ['key1' => 'value1', 'key2' => 'value2']],
            'auth' => null,
            'expected' => "curl --location --request POST 'https://example.com/api' --header \"Content-Type: application/x-www-form-urlencoded\" --data 'key1=value1&key2=value2'",
        ],
    ],
    'curl put request with json body' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'PUT',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['type' => BodyType::RAW_JSON, 'data' => '{"key":"value"}'],
            'auth' => null,
            'expected' => "curl --location --request PUT 'https://example.com/api' --header \"Content-Type: application/json\" --header \"Accept: application/json\" --data '{\"key\":\"value\"}'",
        ],
    ],
    'curl delete request with no body' => [
        'data' => [
            'url' => 'https://example.com/api/resource/1',
            'method' => 'DELETE',
            'headers' => [],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => null,
            'expected' => "curl --location --request DELETE 'https://example.com/api/resource/1'",
        ],
    ],
    'curl patch request with json body' => [
        'data' => [
            'url' => 'https://example.com/api/resource/1',
            'method' => 'PATCH',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['type' => BodyType::RAW_JSON, 'data' => '{"key":"value"}'],
            'auth' => null,
            'expected' => "curl --location --request PATCH 'https://example.com/api/resource/1' --header \"Content-Type: application/json\" --header \"Accept: application/json\" --data '{\"key\":\"value\"}'",
        ],
    ],
    'curl request with custom headers' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'GET',
            'headers' => ['X-Custom-Header' => 'CustomValue'],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => null,
            'expected' => "curl --location --request GET 'https://example.com/api' --header \"X-Custom-Header: CustomValue\"",
        ],
    ],
    'curl request with multiple headers' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'GET',
            'headers' => [
                'X-Custom-Header' => 'CustomValue',
                'Accept' => 'application/json',
            ],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => null,
            'expected' => "curl --location --request GET 'https://example.com/api' --header \"X-Custom-Header: CustomValue\" --header \"Accept: application/json\"",
        ],
    ],
    'curl request with empty body' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['type' => BodyType::RAW_JSON, 'data' => '{}'],
            'auth' => null,
            'expected' => "curl --location --request POST 'https://example.com/api' --header \"Content-Type: application/json\" --header \"Accept: application/json\" --data '{}'",
        ],
    ],
    'curl request with no url' => [
        'data' => [
            'url' => '',
            'method' => 'GET',
            'headers' => [],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => null,
            'expected' => '',
        ],
    ],
    'curl request with auth basic' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'GET',
            'headers' => [],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => new BasicAuthData(
                username: 'user',
                password: 'pass'
            ),
            'expected' => "curl --location --request GET 'https://example.com/api' --header 'Authorization: Basic dXNlcjpwYXNz'",
        ],
    ],
    'curl request with auth basic and custom headers' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'GET',
            'headers' => ['X-Custom-Header' => 'CustomValue'],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => new BasicAuthData(
                username: 'user',
                password: 'pass'
            ),
            'expected' => "curl --location --request GET 'https://example.com/api' --header \"X-Custom-Header: CustomValue\" --header 'Authorization: Basic dXNlcjpwYXNz'",
        ],
    ],
    'curl request with auth basic and body' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['type' => BodyType::RAW_JSON, 'data' => '{"key":"value"}'],
            'auth' => new BasicAuthData(
                username: 'user',
                password: 'pass'
            ),
            'expected' => "curl --location --request POST 'https://example.com/api' --header \"Content-Type: application/json\" --header \"Accept: application/json\" --header 'Authorization: Basic dXNlcjpwYXNz' --data '{\"key\":\"value\"}'",
        ],
    ],
    'curl request with digest auth' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'GET',
            'headers' => [],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => new DigestAuthData(
                username: 'user',
                password: 'pass'
            ),
            'expected' => "curl --location --request GET 'https://example.com/api' --digest --user 'user:pass'",
        ],
    ],
    'curl request with bearer token auth' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'GET',
            'headers' => [],
            'body' => ['type' => BodyType::NONE, 'data' => []],
            'auth' => new BearerTokenData(
                token: 'your_token_here'
            ),
            'expected' => "curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_token_here'",
        ],
    ],
]);
