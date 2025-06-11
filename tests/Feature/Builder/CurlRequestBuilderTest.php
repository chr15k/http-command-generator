<?php

declare(strict_types=1);

use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

beforeEach(function (): void {
    $this->builder = HttpRequestBuilder::create();
});

// ======================================================================
// General Tests
// ======================================================================

test('curl request builder create method returns instance', function (): void {
    expect($this->builder)->toBeInstanceOf(HttpRequestBuilder::class);
});

test('curl request builder returns empty string when no parameters are set', function (): void {
    $output = $this->builder->toCurl();
    expect($output)->toBe('');
});

test('curl request builder generates basic curl command', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

test('curl request builder generates curl command with method', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('POST');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api'");
});

test('curl request builder generates curl command with headers', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->header('Authorization', 'Bearer token')
        ->header('Accept', 'application/json');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header \"Authorization: Bearer token\" --header \"Accept: application/json\"");
});

test('curl request builder generates curl command with custom headers and body', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('POST')
        ->header('Content-Type', 'application/json')
        ->withRawJsonBody('{"key":"value"}');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header \"Content-Type: application/json\" --data '{\"key\":\"value\"}'");
});

test('curl request builder generates curl command with form data', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('POST')
        ->withFormBody(['key1' => 'value1', 'key2' => 'value2']);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'key1=value1' --data-urlencode 'key2=value2'");
});

test('curl request builder generates curl command with multipart form data', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('POST')
        ->withMultipartBody(['image' => '@path/to/file', 'key' => 'value']);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --form 'image=@path/to/file' --form 'key=value'");
});

test('curl request builder generates curl command with query parameters', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api?param1=value1&param2=value2')
        ->method('GET');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?param1=value1&param2=value2'");
});

test('curl request builder generates curl command with custom method', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('PATCH');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request PATCH 'https://example.com/api'");
});

// ======================================================================
// BASIC AUTH Tests
// ======================================================================

test('curl request builder generates curl command with basic auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withBasicAuth('username', 'password');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ='");
});

test('curl request builder generates curl command with raw basic auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withRawBasicAuth('username', 'password');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --user 'username:password'");
});

test('curl request builder generates curl command with pre encoded basic auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withPreEncodedBasicAuth('T01HIGkgY2Fubm90IGJlbGlldmUgeW91IGRlY29kZWQgbWUh');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Basic T01HIGkgY2Fubm90IGJlbGlldmUgeW91IGRlY29kZWQgbWUh'");
});

// ======================================================================
// BEARER TOKEN Tests
// ======================================================================

test('curl request builder generates curl command with bearer token', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withBearerToken('your_token_here');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_token_here'");
});

// ======================================================================
// DIGEST AUTH Tests
// ======================================================================

test('curl request builder generates curl command with digest auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withDigestAuth('username', 'password');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --digest --user 'username:password'");
});

// ======================================================================
// API TOKEN Tests
// ======================================================================

test('curl request builder generates curl command with API key in query', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withApiKey('token', 'value', true);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?token=value'");
});

test('curl request builder generates curl command with API key in header', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withApiKey('X-API-Key', 'your_api_key');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'X-API-Key: your_api_key'");
});

// ======================================================================
// JWT Pre-encoded Tests
// ======================================================================

test('curl request builder generates curl command with JWT in query', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withPreEncodedJWTAuth('your_jwt_token', true);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?token=your_jwt_token'");
});

test('curl request builder generates curl command with JWT in header', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withPreEncodedJWTAuth('your_jwt_token');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_jwt_token'");
});

test('curl request builder generates curl command with JWT in header with custom prefix', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withPreEncodedJWTAuth('your_jwt_token', false, 'token');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_jwt_token'");
});

test('curl request builder generates curl command with JWT in header with custom prefix and query key', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->method('GET')
        ->withPreEncodedJWTAuth('your_jwt_token', false, 'token', 'CustomPrefix');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: CustomPrefix your_jwt_token'");
});

// ======================================================================
// JWT auto-encode Tests (todo)
// ======================================================================
