<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use Chr15k\HttpCommand\HttpCommand;

// ======================================================================
// General Tests
// ======================================================================

test('curl request builder create method returns builder instance', function (): void {
    $command = HttpCommand::get('https://example.com');
    expect($command)->toBeInstanceOf(Chr15k\HttpCommand\Builder\CommandBuilder::class);
});

test('curl request builder generates basic curl command', function (): void {

    $command = HttpCommand::get('https://example.com/api');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

test('curl request builder generates curl command with method', function (): void {

    $command = HttpCommand::post('https://example.com/api');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api'");
});

test('curl request builder generates curl command with header method', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->header('Authorization', 'Bearer token')
        ->header('Accept', 'application/json');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header \"Authorization: Bearer token\" --header \"Accept: application/json\"");
});

test('curl request builder generates curl command with headers method', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->headers([
            'Authorization' => 'Bearer token',
            'Accept' => 'application/json',
            'X-Custom-Header' => 'CustomValue',
        ]);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header \"Authorization: Bearer token\" --header \"Accept: application/json\" --header \"X-Custom-Header: CustomValue\"");
});

test('curl request builder generates curl command with query parameters', function (): void {

    $command = HttpCommand::get('https://example.com/api?param1=value1&param2=value2');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?param1=value1&param2=value2'");
});

test('curl request builder generates curl command with custom method', function (): void {

    $command = HttpCommand::patch('https://example.com/api');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request PATCH 'https://example.com/api'");
});

test('curl request builder generates curl deletion command', function (): void {

    $command = HttpCommand::delete('https://example.com/api/resource/123');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request DELETE 'https://example.com/api/resource/123'");
});

test('curl request builder generates curl with custom parameters', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->query('param1', 'value1')
        ->query('param2', 'value2');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?param1=value1&param2=value2'");
});

test('curl request builder generates curl command with custom parameters and auth in query', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->queries([
            'param1' => 'value1',
            'param2' => 'value2',
        ])
        ->header('Accept', 'application/json')
        ->auth()
        ->apiKey('token', 'pass', true);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?param1=value1&param2=value2&token=pass' --header \"Accept: application/json\"");
});

// ======================================================================
// Body data Tests
// ======================================================================

test('curl request builder generates curl command with custom headers and body', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->header('Content-Type', 'application/json')
        ->json('{"key":"value"}', true);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header \"Content-Type: application/json\" --data '{\"key\":\"value\"}'");
});

test('curl request builder generates curl command with raw JSON body and auto adds Content-Type header', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->json('{"key":"value"}', true);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data '{\"key\":\"value\"}'");
});

test('curl request builder generates curl command with form data', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->form(['key1' => 'value1', 'key2' => 'value2']);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'key1=value1' --data-urlencode 'key2=value2'");
});

test('curl request builder generates curl command with multipart form data', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->multipart(['image' => '@path/to/file', 'key' => 'value']);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --form 'image=@path/to/file' --form 'key=value'");
});

test('curl request builder generates curl command with multipart form data using put', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->method('PUT')
        ->multipart(['image' => '@path/to/file', 'key' => 'value']);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request PUT 'https://example.com/api' --form 'image=@path/to/file' --form 'key=value'");
});

test('curl request builder generates curl command with empty multipart', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->multipart([]);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api'");
});

test('curl request builder generates curl command with empty body', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->json('', true);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data ''");
});

test('curl request builder generates curl command with empty form data', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->form([]);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded'");
});

test('curl request builder generates curl command with json body', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->json(['key' => 'value']);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data '{\"key\":\"value\"}'");
});

test('curl request builder generates curl command with binary data', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->file('/path/to/file');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request POST 'https://example.com/api' --data-binary '@/path/to/file'");
});

test('curl request builder generates curl command with test file and determines content-type', function (): void {

    try {
        $tmpFile = tempnam(sys_get_temp_dir(), 'curl_request_builder_test__');
        if ($tmpFile === false) {
            throw new Exception('Unable to create temporary file for testing');
        }

        $path = $tmpFile.'.txt';
        if (! rename($tmpFile, $path)) {
            throw new Exception('Unable to rename temporary file for testing');
        }

        if (in_array(file_put_contents($path, "This is some temporary content.\n"), [0, false], true)) {
            throw new Exception('Unable to write to temporary file for testing');
        }

        $command = HttpCommand::post('https://example.com/api')
            ->file($path);

        $output = $command->toCurl();

        expect($output)->toBe("curl --location --request POST 'https://example.com/api' --header 'Content-Type: text/plain' --data-binary '@{$path}'");

    } catch (Exception|Throwable $e) {
        $this->markTestSkipped('Unable to create temporary file for testing: '.$e->getMessage());
    } finally {
        if (isset($path) && file_exists($path)) {
            unlink($path);
        }
    }
});

// ======================================================================
// BASIC AUTH Tests
// ======================================================================

test('curl request builder generates curl command with basic auth', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->basic('username', 'password');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ='");
});

test('curl request builder generates curl command with no basic auth data', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->basic('', '');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// BEARER TOKEN Tests
// ======================================================================

test('curl request builder generates curl command with bearer token', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->withBearerToken('your_token_here');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_token_here'");
});

test('curl request builder generates curl command with empty bearer token', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->withBearerToken('');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// DIGEST AUTH Tests
// ======================================================================

test('curl request builder generates curl command with digest auth', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->digest(
            username: 'username',
            password: 'password',
            algorithm: DigestAlgorithm::MD5,
            realm: 'example.com',
            method: 'GET',
            uri: '/api'
        );

    $output = $command->toCurl();

    expect($output)->toBe('curl --location --request GET \'https://example.com/api\' --header \'Authorization: Digest username="username", realm="example.com", nonce="", uri="/api", algorithm="MD5", response="d7edf3213a7a22e4d0b15526b6bdd919"\'');
});

test('curl request builder generates curl command with empty digest auth', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->digest('', '');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// API TOKEN Tests
// ======================================================================

test('curl request builder generates curl command with API key in query', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->apiKey('token', 'value', true);

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?token=value'");
});

test('curl request builder generates curl command with API key in header', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->apiKey('X-API-Key', 'your_api_key');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'X-API-Key: your_api_key'");
});

test('curl request builder generates curl command with empty API key value', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->apiKey('token', '');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'token; '");
});

test('curl request builder generates curl command with empty API key and value', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->apiKey('', '');

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});

// ======================================================================
// JWT Pre-encoded Tests
// ======================================================================

test('curl request builder generates curl command with JWT pre-encoded in header', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->jwt(
            key: 'your_secret',
            payload: ['user' => 'test'],
            headerPrefix: 'Bearer'
        );

    $output = $command->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'");
});

test('curl request builder generates curl command with JWT pre-encoded in query', function (): void {

    $builder = HttpCommand::get('https://example.com/api')
        ->auth()
        ->jwt(
            payload: ['user' => 'test'],
            key: 'your_secret',
            inQuery: true,
            queryKey: 'jwt'
        );

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'");
});

test('curl request builder generates curl command with JWT pre-encoded in header with base64 key', function (): void {

    $builder = HttpCommand::get('https://example.com/api')
        ->auth()
        ->jwt(
            payload: ['user' => 'test'],
            key: base64_encode('your_secret'),
            secretBase64Encoded: true,
            headerPrefix: 'Bearer'
        );

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'");
});

test('curl request builder generates curl command with asymmetric JWT', function (): void {

    $privateKey = <<<'EOD'
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

    $publicKey = <<<'EOD'
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
        'nbf' => 1357000000,
    ];

    $builder = HttpCommand::get('https://example.com/api')
        ->auth()
        ->jwt(
            payload: $payload,
            key: $privateKey,
            algorithm: Algorithm::RS256
        );

    $output = $builder->toCurl();

    expect($output)
        ->toBe("curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.fm0h0Ec3yp7S3JNBLeFa2owu4a91IXFJs8NPgBUxgKKSfd_-Mqes2zxuxmkYpmQDG936u739mIDZyG9KQm0ER5HB243MVbFZHcaC7VAIqmoCZ4dcS6yoJ1ltH6vdwc8o3xkYVEWKvLr8a7ck21u-pASWB_tpqM7XtIu7xyCZhfpmxTbhNyTgsJ1HN4fVYrHn2535qYsetOTps_zM2cgVRQYbkp1RovL-ZDsp3rMxzEiGQ7F80JXh2fsTHKlpPqAyF40GEXvCZa0MIoRa7g1pIjRtNLYgOgO94YsSRGB0VDDsLNdXzkjv0Ujfk_uqtD0IKgt7ffQzh8b8dUl8onXA_g'");
});

test('curl request builder generates curl command with empty JWT key', function (): void {

    $builder = HttpCommand::get('https://example.com/api')
        ->auth()
        ->jwt(
            payload: ['user' => 'test'],
            key: ''
        );

    $output = $builder->toCurl();

    expect($output)->toBe("curl --location --request GET 'https://example.com/api'");
});
