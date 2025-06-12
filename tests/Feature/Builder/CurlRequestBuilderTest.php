<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Chr15k\HttpCliGenerator\Enums\Algorithm;
use Chr15k\HttpCliGenerator\Generators\CurlGenerator;
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
        ->withPreEncodedBasicAuth('username', 'password');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ='");
});

test('curl request builder generates curl command with raw basic auth', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withBasicAuth('username', 'password');

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --user 'username:password'");
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
        ->withBasicAuth('', '');

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

test('curl request builder generates curl command with JWT pre-encoded in header', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withJWTAuth(
            payload: ['user' => 'test'],
            key: 'your_secret',
            headerPrefix: 'Bearer'
        );

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'");
});

test('curl request builder generates curl command with JWT pre-encoded in query', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withJWTAuth(
            payload: ['user' => 'test'],
            key: 'your_secret',
            inQuery: true,
            queryKey: 'jwt'
        );

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'");
});

test('curl request builder generates curl command with JWT pre-encoded in header with base64 key', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withJWTAuth(
            payload: ['user' => 'test'],
            key: base64_encode('your_secret'),
            secretBase64Encoded: true,
            headerPrefix: 'Bearer'
        );

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'");
});

test('curl request builder generates curl command with asymmetric JWT', function (): void {

    $privateKey = <<<EOD
    -----BEGIN RSA PRIVATE KEY-----
    MIIEowIBAAKCAQEAuzWHNM5f+amCjQztc5QTfJfzCC5J4nuW+L/aOxZ4f8J3Frew
    M2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJhzkPYLae7bTVro3hok0zDITR8F6S
    JGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548tu4czCuqU8BGVOlnp6IqBHhAswNMM
    78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vSopcT51koWOgiTf3C7nJUoMWZHZI5
    HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTzTTqo1SCSH2pooJl9O8at6kkRYsrZ
    WwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/BwQIDAQABAoIBAFtGaOqNKGwggn9k
    6yzr6GhZ6Wt2rh1Xpq8XUz514UBhPxD7dFRLpbzCrLVpzY80LbmVGJ9+1pJozyWc
    VKeCeUdNwbqkr240Oe7GTFmGjDoxU+5/HX/SJYPpC8JZ9oqgEA87iz+WQX9hVoP2
    oF6EB4ckDvXmk8FMwVZW2l2/kd5mrEVbDaXKxhvUDf52iVD+sGIlTif7mBgR99/b
    c3qiCnxCMmfYUnT2eh7Vv2LhCR/G9S6C3R4lA71rEyiU3KgsGfg0d82/XWXbegJW
    h3QbWNtQLxTuIvLq5aAryV3PfaHlPgdgK0ft6ocU2de2FagFka3nfVEyC7IUsNTK
    bq6nhAECgYEA7d/0DPOIaItl/8BWKyCuAHMss47j0wlGbBSHdJIiS55akMvnAG0M
    39y22Qqfzh1at9kBFeYeFIIU82ZLF3xOcE3z6pJZ4Dyvx4BYdXH77odo9uVK9s1l
    3T3BlMcqd1hvZLMS7dviyH79jZo4CXSHiKzc7pQ2YfK5eKxKqONeXuECgYEAyXlG
    vonaus/YTb1IBei9HwaccnQ/1HRn6MvfDjb7JJDIBhNClGPt6xRlzBbSZ73c2QEC
    6Fu9h36K/HZ2qcLd2bXiNyhIV7b6tVKk+0Psoj0dL9EbhsD1OsmE1nTPyAc9XZbb
    OPYxy+dpBCUA8/1U9+uiFoCa7mIbWcSQ+39gHuECgYAz82pQfct30aH4JiBrkNqP
    nJfRq05UY70uk5k1u0ikLTRoVS/hJu/d4E1Kv4hBMqYCavFSwAwnvHUo51lVCr/y
    xQOVYlsgnwBg2MX4+GjmIkqpSVCC8D7j/73MaWb746OIYZervQ8dbKahi2HbpsiG
    8AHcVSA/agxZr38qvWV54QKBgCD5TlDE8x18AuTGQ9FjxAAd7uD0kbXNz2vUYg9L
    hFL5tyL3aAAtUrUUw4xhd9IuysRhW/53dU+FsG2dXdJu6CxHjlyEpUJl2iZu/j15
    YnMzGWHIEX8+eWRDsw/+Ujtko/B7TinGcWPz3cYl4EAOiCeDUyXnqnO1btCEUU44
    DJ1BAoGBAJuPD27ErTSVtId90+M4zFPNibFP50KprVdc8CR37BE7r8vuGgNYXmnI
    RLnGP9p3pVgFCktORuYS2J/6t84I3+A17nEoB4xvhTLeAinAW/uTQOUmNicOP4Ek
    2MsLL2kHgL8bLTmvXV4FX+PXphrDKg1XxzOYn0otuoqdAQrkK4og
    -----END RSA PRIVATE KEY-----
    EOD;

    $publicKey = <<<EOD
    -----BEGIN PUBLIC KEY-----
    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuzWHNM5f+amCjQztc5QT
    fJfzCC5J4nuW+L/aOxZ4f8J3FrewM2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJ
    hzkPYLae7bTVro3hok0zDITR8F6SJGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548t
    u4czCuqU8BGVOlnp6IqBHhAswNMM78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vS
    opcT51koWOgiTf3C7nJUoMWZHZI5HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTz
    TTqo1SCSH2pooJl9O8at6kkRYsrZWwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/B
    wQIDAQAB
    -----END PUBLIC KEY-----
    EOD;

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
        'iat' => 1356999524,
        'nbf' => 1357000000
    ];

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withJWTAuth(
            payload: $payload,
            key: $privateKey,
            algorithm: Algorithm::RS256
        );

    $output = $builder->toCurl();

    expect($output)
        ->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.fm0h0Ec3yp7S3JNBLeFa2owu4a91IXFJs8NPgBUxgKKSfd_-Mqes2zxuxmkYpmQDG936u739mIDZyG9KQm0ER5HB243MVbFZHcaC7VAIqmoCZ4dcS6yoJ1ltH6vdwc8o3xkYVEWKvLr8a7ck21u-pASWB_tpqM7XtIu7xyCZhfpmxTbhNyTgsJ1HN4fVYrHn2535qYsetOTps_zM2cgVRQYbkp1RovL-ZDsp3rMxzEiGQ7F80JXh2fsTHKlpPqAyF40GEXvCZa0MIoRa7g1pIjRtNLYgOgO94YsSRGB0VDDsLNdXzkjv0Ujfk_uqtD0IKgt7ffQzh8b8dUl8onXA_g'");
});

test('curl request builder generates curl command with empty JWT key', function (): void {

    $builder = $this->builder
        ->url('https://example.com/api')
        ->get()
        ->withJWTAuth(
            payload: ['user' => 'test'],
            key: ''
        );

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});