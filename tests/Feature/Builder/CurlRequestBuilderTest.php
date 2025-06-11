<?php

declare(strict_types=1);

use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;
use Chr15k\HttpCliGenerator\Generators\CurlGenerator;

beforeEach(function (): void {
    $this->builder = HttpRequestBuilder::create();
});

// ======================================================================
// General Tests
// ======================================================================

test('curl request builder create method returns instance', function (): void {
    expect($this->builder)->toBeInstanceOf(HttpRequestBuilder::class);
});

test('curl request builder can list available generators', function (): void {
    $generators = $this->builder->availableGenerators();
    expect($generators)->toBeArray();
    expect($generators)->toHaveKey('curl');
    expect($generators['curl'])->toBeInstanceOf(CurlGenerator::class);
});

test('curl request builder returns empty string when no parameters are set', function (): void {
    $output = $this->builder->to('curl');
    expect($output)->toBe('');
});

test('curl request builder throws exception for unregistered generator', function (): void {
    $this->builder->to('nonexistent');
})->throws(InvalidArgumentException::class, "Generator 'nonexistent' is not registered.");

test('curl request builder generates basic curl command', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

test('curl request builder generates curl command with method', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post();

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api'");
});

test('curl request builder generates curl command with header method', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->header('Authorization', 'Bearer token')
        ->header('Accept', 'application/json');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header \"Authorization: Bearer token\" --header \"Accept: application/json\"");
});

test('curl request builder generates curl command with headers method', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->headers([
            'Authorization' => 'Bearer token',
            'Accept' => 'application/json',
            'X-Custom-Header' => 'CustomValue',
        ]);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header \"Authorization: Bearer token\" --header \"Accept: application/json\" --header \"X-Custom-Header: CustomValue\"");
});

test('curl request builder generates curl command with query parameters', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api?param1=value1&param2=value2')
        ->get();

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

test('curl request builder generates curl deletion command', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api/resource/123')
        ->delete();

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request DELETE 'https://example.com/api/resource/123'");
});

// ======================================================================
// Body data Tests
// ======================================================================

test('curl request builder generates curl command with custom headers and body', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post()
        ->header('Content-Type', 'application/json')
        ->withRawJsonBody('{"key":"value"}');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header \"Content-Type: application/json\" --data '{\"key\":\"value\"}'");
});

test('curl request builder generates curl command with raw JSON body and auto adds Content-Type header', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post()
        ->withRawJsonBody('{"key":"value"}');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data '{\"key\":\"value\"}'");
});

test('curl request builder generates curl command with form data', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post()
        ->withFormBody(['key1' => 'value1', 'key2' => 'value2']);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'key1=value1' --data-urlencode 'key2=value2'");
});

test('curl request builder generates curl command with multipart form data', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post()
        ->withMultipartBody(['image' => '@path/to/file', 'key' => 'value']);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --form 'image=@path/to/file' --form 'key=value'");
});

test('curl request builder generates curl command with multipart form data using put', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->put()
        ->withMultipartBody(['image' => '@path/to/file', 'key' => 'value']);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request PUT 'https://example.com/api' --form 'image=@path/to/file' --form 'key=value'");
});

test('curl request builder generates curl command with empty multipart', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post()
        ->withMultipartBody([]);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api'");
});

test('curl request builder generates curl command with empty body', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post()
        ->withRawJsonBody('');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data ''");
});

test('curl request builder generates curl command with empty form data', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post()
        ->withFormBody([]);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded'");
});

test('curl request builder generates curl command with json body', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->post()
        ->withJsonBody(['key' => 'value']);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data '{\"key\":\"value\"}'");
});

// ======================================================================
// BASIC AUTH Tests
// ======================================================================

test('curl request builder generates curl command with basic auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withBasicAuth('username', 'password');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ='");
});

test('curl request builder generates curl command with raw basic auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withRawBasicAuth('username', 'password');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --user 'username:password'");
});

test('curl request builder generates curl command with pre encoded basic auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withPreEncodedBasicAuth('T01HIGkgY2Fubm90IGJlbGlldmUgeW91IGRlY29kZWQgbWUh');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Basic T01HIGkgY2Fubm90IGJlbGlldmUgeW91IGRlY29kZWQgbWUh'");
});

test('curl request builder generates curl command with no basic auth data', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withBasicAuth('', '');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

test('curl request builder generates curl command with no raw basic auth data', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withRawBasicAuth('', '');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

test('curl request builder generates curl command with no pre-encoded basic auth data', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withPreEncodedBasicAuth('');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// BEARER TOKEN Tests
// ======================================================================

test('curl request builder generates curl command with bearer token', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withBearerToken('your_token_here');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_token_here'");
});

test('curl request builder generates curl command with empty bearer token', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withBearerToken('');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// DIGEST AUTH Tests
// ======================================================================

test('curl request builder generates curl command with digest auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withDigestAuth('username', 'password');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --digest --user 'username:password'");
});

test('curl request builder generates curl command with empty digest auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withDigestAuth('', '');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// API TOKEN Tests
// ======================================================================

test('curl request builder generates curl command with API key in query', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withApiKey('token', 'value', true);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?token=value'");
});

test('curl request builder generates curl command with API key in header', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withApiKey('X-API-Key', 'your_api_key');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'X-API-Key: your_api_key'");
});

test('curl request builder generates curl command with empty API key value', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withApiKey('token', '');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'token; '");
});

test('curl request builder generates curl command with empty API key and value', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withApiKey('', '');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// JWT Pre-encoded Tests
// ======================================================================

test('curl request builder generates curl command with JWT in query', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withPreEncodedJWTAuth('your_jwt_token', true);

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?token=your_jwt_token'");
});

test('curl request builder generates curl command with JWT in header', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withPreEncodedJWTAuth('your_jwt_token');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_jwt_token'");
});

test('curl request builder generates curl command with JWT in header with custom prefix', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withPreEncodedJWTAuth('your_jwt_token', false, 'token');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_jwt_token'");
});

test('curl request builder generates curl command with JWT in header with custom prefix and query key', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withPreEncodedJWTAuth('your_jwt_token', false, 'token', 'CustomPrefix');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: CustomPrefix your_jwt_token'");
});

test('curl request builder generates curl command with empty JWT token', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withPreEncodedJWTAuth('');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// JWT auto-encode Tests (todo)
// ======================================================================
