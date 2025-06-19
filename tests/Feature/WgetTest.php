<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use Chr15k\HttpCommand\HttpCommand;

// ======================================================================
// General Tests
// ======================================================================

test('wget request builder generates basic wget command', function (): void {

    $command = HttpCommand::get('https://example.com/api');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'");
});

test('wget request builder generates wget command with method', function (): void {

    $command = HttpCommand::post('https://example.com/api');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 'https://example.com/api'");
});

test('wget request builder generates wget command with header method', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->header('Authorization', 'Bearer token')
        ->header('Accept', 'application/json');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header \"Authorization: Bearer token\" --header \"Accept: application/json\" 'https://example.com/api'");
});

test('wget request builder generates wget command with headers method', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->headers([
            'Authorization' => 'Bearer token',
            'Accept' => 'application/json',
            'X-Custom-Header' => 'CustomValue',
        ]);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header \"Authorization: Bearer token\" --header \"Accept: application/json\" --header \"X-Custom-Header: CustomValue\" 'https://example.com/api'");
});

test('wget request builder generates wget command with query parameters', function (): void {

    $command = HttpCommand::get('https://example.com/api?param1=value1&param2=value2');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value1&param2=value2'");
});

test('wget request builder generates wget command with custom method', function (): void {

    $command = HttpCommand::patch('https://example.com/api');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method PATCH --timeout=0 'https://example.com/api'");
});

test('wget request builder generates wget deletion command', function (): void {

    $command = HttpCommand::delete('https://example.com/api/resource/123');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method DELETE --timeout=0 'https://example.com/api/resource/123'");
});

// ======================================================================
// Query Parameters Tests
// ======================================================================

test('curl request builder generates curl command with query parameters', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->query('param1', 'value1')
        ->query('param2', 'value2');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value1&param2=value2'");
});

test('curl request builder generates curl command with duplicate key query parameters', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->query('param', 'value1')
        ->query('param', 'value2');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param=value1&param=value2'");
});

test('curl request builder generates curl command with query parameters and custom headers', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->query('param1', 'value1')
        ->query('param2', 'value2')
        ->header('Accept', 'application/json');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header \"Accept: application/json\" 'https://example.com/api?param1=value1&param2=value2'");
});

test('curl request builder generates curl command with encoded query parameters', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->query('param1', 'value with spaces')
        ->query('param2', 'value&with=special#chars')
        ->query('param3', 'value with spaces and & special chars')
        ->encodeQuery();

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value%20with%20spaces&param2=value%26with%3Dspecial%23chars&param3=value%20with%20spaces%20and%20%26%20special%20chars'");
});

test('curl request builder generates curl command with no encoding and special characters', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->query('param1', 'value with spaces')
        ->query('param2', 'value&with=special#chars')
        ->query('param3', 'value with spaces and & special chars');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value with spaces&param2=value&with=special#chars&param3=value with spaces and & special chars'");
});

// ======================================================================
// Body data Tests
// ======================================================================

test('wget request builder generates wget command with custom headers and body', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->header('Content-Type', 'application/json')
        ->json('{"key":"value"}', true);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --header \"Content-Type: application/json\" --body-data '{\"key\":\"value\"}' 'https://example.com/api'");
});

test('wget request builder generates wget command with raw JSON body and auto adds Content-Type header', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->json('{"key":"value"}', true);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/json' --body-data '{\"key\":\"value\"}' 'https://example.com/api'");
});

test('wget request builder generates wget command with form data', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->form(['key1' => 'value1', 'key2' => 'value2']);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data 'key1=value1&key2=value2' 'https://example.com/api'");
});

test('wget request builder generates wget command with empty multipart', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->multipart([
            'foo' => 'bar',
            'lang' => 'en',
        ]);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --body-data 'foo=bar&lang=en' 'https://example.com/api'");
});

test('wget request builder generates wget command with multiple multipart body', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->multipart([]);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 'https://example.com/api'");
});

test('wget request builder generates wget command with empty body', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->json('', true);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/json' --body-data '' 'https://example.com/api'");
});

test('wget request builder generates wget command with empty form data', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->form([]);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' 'https://example.com/api'");
});

test('wget request builder generates wget command with json body', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->json(['key' => 'value']);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/json' --body-data '{\"key\":\"value\"}' 'https://example.com/api'");
});

test('wget request builder generates wget command with binary data', function (): void {

    $command = HttpCommand::post('https://example.com/api')
        ->file('/path/to/file');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --body-file='/path/to/file' 'https://example.com/api'");
});

test('wget request builder generates wget command with test file and determines content-type', function (): void {

    try {
        $tmpFile = tempnam(sys_get_temp_dir(), 'wget_request_builder_test__');
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

        $output = $command->toWget();

        expect($output)->toBe("wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: text/plain' --body-file='{$path}' 'https://example.com/api'");

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

test('wget request builder generates wget command with basic auth', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->basic('username', 'password');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ=' 'https://example.com/api'");
});

test('wget request builder generates wget command with no basic auth data', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->basic('', '');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'");
});

// ======================================================================
// BEARER TOKEN Tests
// ======================================================================

test('wget request builder generates wget command with bearer token', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->withBearerToken('your_token_here');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Bearer your_token_here' 'https://example.com/api'");
});

test('wget request builder generates wget command with empty bearer token', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->withBearerToken('');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'");
});

// ======================================================================
// DIGEST AUTH Tests
// ======================================================================

test('wget request builder generates wget command with digest auth', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->digest(
            username: 'username',
            password: 'password',
            algorithm: DigestAlgorithm::MD5,
            realm: 'example_realm',
            method: 'GET',
            uri: '/api',
            nonce: 'nonce_value',
            nc: '00000001',
            cnonce: 'cnonce_value',
            qop: 'auth'
        );

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Digest username=\"username\", realm=\"example_realm\", nonce=\"nonce_value\", uri=\"/api\", algorithm=\"MD5\", qop=auth, nc=00000001, cnonce=\"cnonce_value\", response=\"6d83896b8040bd7dc9c5602ae8730fb6\"' 'https://example.com/api'");
});

test('wget request builder generates wget command with empty digest auth', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->digest('', '');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'");
});

// ======================================================================
// API TOKEN Tests
// ======================================================================

test('wget request builder generates wget command with API key in query', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->apiKey('token', 'value', true);

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?token=value'");
});

test('wget request builder generates wget command with API key in header', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->apiKey('X-API-Key', 'your_api_key');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header 'X-API-Key: your_api_key' 'https://example.com/api'");
});

test('wget request builder generates wget command with empty API key value', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->apiKey('token', '');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header 'token; ' 'https://example.com/api'");
});

test('wget request builder generates wget command with empty API key and value', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->apiKey('', '');

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'");
});

// ======================================================================
// JWT Pre-encoded Tests
// ======================================================================

test('wget request builder generates wget command with JWT pre-encoded in header', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->jwt(
            key: 'your_secret',
            payload: ['user' => 'test'],
            headerPrefix: 'Bearer'
        );

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU' 'https://example.com/api'");
});

test('wget request builder generates wget command with JWT pre-encoded in query', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->jwt(
            key: 'your_secret',
            payload: ['user' => 'test'],
            inQuery: true,
            queryKey: 'jwt'
        );

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'");
});

test('wget request builder generates wget command with JWT pre-encoded in header with base64 key', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->jwt(
            key: base64_encode('your_secret'),
            payload: ['user' => 'test'],
            secretBase64Encoded: true,
            headerPrefix: 'Bearer'
        );

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU' 'https://example.com/api'");
});

test('wget request builder generates wget command with asymmetric JWT', function (): void {

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

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->jwt(
            key: $privateKey,
            payload: $payload,
            algorithm: Algorithm::RS256
        );

    $output = $command->toWget();

    expect($output)
        ->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.fm0h0Ec3yp7S3JNBLeFa2owu4a91IXFJs8NPgBUxgKKSfd_-Mqes2zxuxmkYpmQDG936u739mIDZyG9KQm0ER5HB243MVbFZHcaC7VAIqmoCZ4dcS6yoJ1ltH6vdwc8o3xkYVEWKvLr8a7ck21u-pASWB_tpqM7XtIu7xyCZhfpmxTbhNyTgsJ1HN4fVYrHn2535qYsetOTps_zM2cgVRQYbkp1RovL-ZDsp3rMxzEiGQ7F80JXh2fsTHKlpPqAyF40GEXvCZa0MIoRa7g1pIjRtNLYgOgO94YsSRGB0VDDsLNdXzkjv0Ujfk_uqtD0IKgt7ffQzh8b8dUl8onXA_g' 'https://example.com/api'");
});

test('wget request builder generates wget command with empty JWT key', function (): void {

    $command = HttpCommand::get('https://example.com/api')
        ->auth()->jwt(
            key: '',
            payload: ['user' => 'test']
        );

    $output = $command->toWget();

    expect($output)->toBe("wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'");
});
