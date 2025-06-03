<?php

use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Chr15k\HttpCliGenerator\Generators\CurlGenerator;
use Chr15k\HttpCliGenerator\DataTransfer\RequestBodyData;
use Chr15k\HttpCliGenerator\Enums\BodyType;

test('curl generator no auth output', function (array $data) {

    $requestData = new RequestData(
        url: $data['url'],
        method: $data['method'],
        headers: $data['headers'],
        body: new RequestBodyData(
            type: BodyType::RAW_JSON,
            data: $data['body']
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
            'body' => [],
            'auth' => null,
            'expected' => "curl --location 'https://example.com'",
        ],
    ],
    'curl post request with body' => [
        'data' => [
            'url' => 'https://example.com/api',
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['{"key":"value"}'],
            'auth' => null,
            'expected' => "curl --location --request POST 'https://example.com/api' -H \"Content-Type: application/json\" --data '{\"key\":\"value\"}'",
        ],
    ],
    // Add more test cases as needed
]);