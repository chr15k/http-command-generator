<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;

dataset('scenarios', [

    // Basic requests
    'basic GET request' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'",
        ],
    ],

    'basic POST request' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 'https://example.com/api'",
        ],
    ],

    'basic PATCH request' => [
        'method' => 'PATCH',
        'url' => 'https://example.com/api',
        'expected' => [
            'curl' => "curl --location --request PATCH 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method PATCH --timeout=0 'https://example.com/api'",
        ],
    ],

    'basic DELETE request' => [
        'method' => 'DELETE',
        'url' => 'https://example.com/api/resource/123',
        'expected' => [
            'curl' => "curl --location --request DELETE 'https://example.com/api/resource/123'",
            'wget' => "wget --no-check-certificate --quiet --method DELETE --timeout=0 'https://example.com/api/resource/123'",
        ],
    ],

    // Headers
    'request with single header' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'headers' => [
            'Authorization' => 'Bearer token',
            'Accept' => 'application/json',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header \"Authorization: Bearer token\" --header \"Accept: application/json\"",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header \"Authorization: Bearer token\" --header \"Accept: application/json\" 'https://example.com/api'",
        ],
    ],

    'request with multiple headers' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'headers' => [
            'Authorization' => 'Bearer token',
            'Accept' => 'application/json',
            'X-Custom-Header' => 'CustomValue',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header \"Authorization: Bearer token\" --header \"Accept: application/json\" --header \"X-Custom-Header: CustomValue\"",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header \"Authorization: Bearer token\" --header \"Accept: application/json\" --header \"X-Custom-Header: CustomValue\" 'https://example.com/api'",
        ],
    ],

    // Query parameters in URL
    'request with query parameters in URL' => [
        'method' => 'GET',
        'url' => 'https://example.com/api?param1=value1&param2=value2',
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?param1=value1&param2=value2'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value1&param2=value2'",
        ],
    ],

    // Query parameters via builder
    'request with query parameters via builder' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'queries' => [
            'param1' => 'value1',
            'param2' => 'value2',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?param1=value1&param2=value2'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value1&param2=value2'",
        ],
    ],

    'request with duplicate query parameters' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'queries' => [
            ['param', 'value1'],
            ['param', 'value2'],
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?param=value1&param=value2'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param=value1&param=value2'",
        ],
    ],

    'request with duplicate header parameters' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'headers' => [
            ['param' => 'value1'],
            ['param' => 'value2'],
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header \"param: value1\" --header \"param: value2\"",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header \"param: value1\" --header \"param: value2\" 'https://example.com/api'",
        ],
    ],

    'request with query parameters and headers' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'queries' => [
            'param1' => 'value1',
            'param2' => 'value2',
        ],
        'headers' => [
            'Accept' => 'application/json',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?param1=value1&param2=value2' --header \"Accept: application/json\"",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header \"Accept: application/json\" 'https://example.com/api?param1=value1&param2=value2'",
        ],
    ],

    'request with encoded query parameters' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'queries' => [
            'param1' => 'value with spaces',
            'param2' => 'value&with=special#chars',
            'param3' => 'value with spaces and & special chars',
        ],
        'encodeQuery' => true,
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?param1=value%20with%20spaces&param2=value%26with%3Dspecial%23chars&param3=value%20with%20spaces%20and%20%26%20special%20chars'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value%20with%20spaces&param2=value%26with%3Dspecial%23chars&param3=value%20with%20spaces%20and%20%26%20special%20chars'",
        ],
    ],

    'request with unencoded special characters' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'queries' => [
            'param1' => 'value with spaces',
            'param2' => 'value&with=special#chars',
            'param3' => 'value with spaces and & special chars',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?param1=value with spaces&param2=value&with=special#chars&param3=value with spaces and & special chars'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value with spaces&param2=value&with=special#chars&param3=value with spaces and & special chars'",
        ],
    ],

    'request with auth and queries' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'queries' => [
            'param1' => 'value1',
            'param2' => 'value2',
        ],
        'headers' => [
            'Accept' => 'application/json',
        ],
        'auth' => [
            'type' => 'apiKey',
            'key' => 'token',
            'value' => 'pass',
            'inQuery' => true,
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?param1=value1&param2=value2&token=pass' --header \"Accept: application/json\"",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header \"Accept: application/json\" 'https://example.com/api?param1=value1&param2=value2&token=pass'",
        ],
    ],

    // Body data
    'request with custom headers and JSON body (raw)' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => [
            'type' => 'json',
            'data' => '{"key":"value"}',
            'raw' => true,
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header \"Content-Type: application/json\" --data '{\"key\":\"value\"}'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header \"Content-Type: application/json\" --body-data '{\"key\":\"value\"}' 'https://example.com/api'",
        ],
    ],

    'request with JSON body and auto Content-Type' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'json',
            'data' => '{"key":"value"}',
            'raw' => true,
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data '{\"key\":\"value\"}'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/json' --body-data '{\"key\":\"value\"}' 'https://example.com/api'",
        ],
    ],

    'request with form data' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => ['key1' => 'value1', 'key2' => 'value2'],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'key1=value1' --data-urlencode 'key2=value2'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data 'key1=value1&key2=value2' 'https://example.com/api'",
        ],
    ],

    'request with form data must auto encode' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => ['key with spaces' => 'value with spaces', 'key&with=special#chars' => 'value&with=special#chars'],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'key%20with%20spaces=value%20with%20spaces' --data-urlencode 'key%26with%3Dspecial%23chars=value%26with%3Dspecial%23chars'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data 'key%20with%20spaces=value%20with%20spaces&key%26with%3Dspecial%23chars=value%26with%3Dspecial%23chars' 'https://example.com/api'",
        ],
    ],

    'request with multipart form data' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'multipart',
            'data' => ['image' => '@path/to/file', 'key' => 'value'],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --form 'image=@path/to/file' --form 'key=value'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --body-data 'image=@path/to/file&key=value' 'https://example.com/api'",
        ],
    ],

    'request with multipart using PUT method' => [
        'method' => 'PUT',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'multipart',
            'data' => ['image' => '@path/to/file', 'key' => 'value'],
        ],
        'expected' => [
            'curl' => "curl --location --request PUT 'https://example.com/api' --form 'image=@path/to/file' --form 'key=value'",
            'wget' => "wget --no-check-certificate --quiet --method PUT --timeout=0 --body-data 'image=@path/to/file&key=value' 'https://example.com/api'",
        ],
    ],

    'request with empty multipart' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'multipart',
            'data' => [],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 'https://example.com/api'",
        ],
    ],

    'request with empty JSON body' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'json',
            'data' => '',
            'raw' => true,
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data ''",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/json' --body-data '' 'https://example.com/api'",
        ],
    ],

    'request with empty form data' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => [],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' 'https://example.com/api'",
        ],
    ],

    'request with JSON array body' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'json',
            'data' => ['key' => 'value'],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/json' --data '{\"key\":\"value\"}'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/json' --body-data '{\"key\":\"value\"}' 'https://example.com/api'",
        ],
    ],

    'request with binary file data' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'file',
            'path' => '/path/to/file',
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --data-binary '@/path/to/file'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --body-file='/path/to/file' 'https://example.com/api'",
        ],
    ],

    // Authentication
    'request with basic auth' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'basic',
            'username' => 'username',
            'password' => 'password',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header 'Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ='",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ=' 'https://example.com/api'",
        ],
    ],

    'request with empty basic auth' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'basic',
            'username' => '',
            'password' => '',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'",
        ],
    ],

    'request with bearer token' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'bearer',
            'token' => 'your_token_here',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer your_token_here'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Bearer your_token_here' 'https://example.com/api'",
        ],
    ],

    'request with empty bearer token' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'bearer',
            'token' => '',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'",
        ],
    ],

    'request with digest auth' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'digest',
            'username' => 'username',
            'password' => 'password',
            'algorithm' => DigestAlgorithm::MD5,
            'realm' => 'example.com',
            'method' => 'GET',
            'uri' => '/api',
        ],
        'expected' => [
            'curl' => 'curl --location --request GET \'https://example.com/api\' --header \'Authorization: Digest username="username", realm="example.com", nonce="", uri="/api", algorithm="MD5", response="d7edf3213a7a22e4d0b15526b6bdd919"\'',
            'wget' => 'wget --no-check-certificate --quiet --method GET --timeout=0 --header \'Authorization: Digest username="username", realm="example.com", nonce="", uri="/api", algorithm="MD5", response="d7edf3213a7a22e4d0b15526b6bdd919"\' \'https://example.com/api\'',
        ],
    ],

    'request with empty digest auth' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'digest',
            'username' => '',
            'password' => '',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'",
        ],
    ],

    'request with API key in query' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'apiKey',
            'key' => 'token',
            'value' => 'value',
            'inQuery' => true,
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?token=value'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?token=value'",
        ],
    ],

    'request with API key in header' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'apiKey',
            'key' => 'X-API-Key',
            'value' => 'your_api_key',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header 'X-API-Key: your_api_key'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header 'X-API-Key: your_api_key' 'https://example.com/api'",
        ],
    ],

    'request with empty API key value' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'apiKey',
            'key' => 'token',
            'value' => '',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header 'token; '",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header 'token; ' 'https://example.com/api'",
        ],
    ],

    'request with empty API key and value' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'apiKey',
            'key' => '',
            'value' => '',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'",
        ],
    ],

    // JWT Authentication
    'request with JWT in header' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'jwt',
            'key' => 'your_secret',
            'payload' => ['user' => 'test'],
            'headerPrefix' => 'Bearer',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU' 'https://example.com/api'",
        ],
    ],

    'request with JWT in query' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'jwt',
            'payload' => ['user' => 'test'],
            'key' => 'your_secret',
            'inQuery' => true,
            'queryKey' => 'jwt',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'",
        ],
    ],

    'request with JWT using base64 encoded key' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'jwt',
            'payload' => ['user' => 'test'],
            'key' => 'eW91cl9zZWNyZXQ=', // base64_encode('your_secret')
            'secretBase64Encoded' => true,
            'headerPrefix' => 'Bearer',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVzdCJ9.GlO4bXeWhU1as72XerhPfJtj1H92s9dnwDV-gJDjrKU' 'https://example.com/api'",
        ],
    ],

    'request with asymmetric JWT (RS256)' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'jwt',
            'payload' => [
                'iss' => 'example.org',
                'aud' => 'example.com',
                'iat' => 1356999524,
                'nbf' => 1357000000,
            ],
            'key' => "-----BEGIN RSA PRIVATE KEY-----\nMIIEowIBAAKCAQEAuzWHNM5f+amCjQztc5QTfJfzCC5J4nuW+L/aOxZ4f8J3Frew\nM2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJhzkPYLae7bTVro3hok0zDITR8F6S\nJGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548tu4czCuqU8BGVOlnp6IqBHhAswNMM\n78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vSopcT51koWOgiTf3C7nJUoMWZHZI5\nHqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTzTTqo1SCSH2pooJl9O8at6kkRYsrZ\nWwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/BwQIDAQABAoIBAFtGaOqNKGwggn9k\n6yzr6GhZ6Wt2rh1Xpq8XUz514UBhPxD7dFRLpbzCrLVpzY80LbmVGJ9+1pJozyWc\nVKeCeUdNwbqkr240Oe7GTFmGjDoxU+5/HX/SJYPpC8JZ9oqgEA87iz+WQX9hVoP2\noF6EB4ckDvXmk8FMwVZW2l2/kd5mrEVbDaXKxhvUDf52iVD+sGIlTif7mBgR99/b\nc3qiCnxCMmfYUnT2eh7Vv2LhCR/G9S6C3R4lA71rEyiU3KgsGfg0d82/XWXbegJW\nh3QbWNtQLxTuIvLq5aAryV3PfaHlPgdgK0ft6ocU2de2FagFka3nfVEyC7IUsNTK\nbq6nhAECgYEA7d/0DPOIaItl/8BWKyCuAHMss47j0wlGbBSHdJIiS55akMvnAG0M\n39y22Qqfzh1at9kBFeYeFIIU82ZLF3xOcE3z6pJZ4Dyvx4BYdXH77odo9uVK9s1l\n3T3BlMcqd1hvZLMS7dviyH79jZo4CXSHiKzc7pQ2YfK5eKxKqONeXuECgYEAyXlG\nvonaus/YTb1IBei9HwaccnQ/1HRn6MvfDjb7JJDIBhNClGPt6xRlzBbSZ73c2QEC\n6Fu9h36K/HZ2qcLd2bXiNyhIV7b6tVKk+0Psoj0dL9EbhsD1OsmE1nTPyAc9XZbb\nOPYxy+dpBCUA8/1U9+uiFoCa7mIbWcSQ+39gHuECgYAz82pQfct30aH4JiBrkNqP\nnJfRq05UY70uk5k1u0ikLTRoVS/hJu/d4E1Kv4hBMqYCavFSwAwnvHUo51lVCr/y\nxQOVYlsgnwBg2MX4+GjmIkqpSVCC8D7j/73MaWb746OIYZervQ8dbKahi2HbpsiG\n8AHcVSA/agxZr38qvWV54QKBgCD5TlDE8x18AuTGQ9FjxAAd7uD0kbXNz2vUYg9L\nhFL5tyL3aAAtUrUUw4xhd9IuysRhW/53dU+FsG2dXdJu6CxHjlyEpUJl2iZu/j15\nYnMzGWHIEX8+eWRDsw/+Ujtko/B7TinGcWPz3cYl4EAOiCeDUyXnqnO1btCEUU44\nDJ1BAoGBAJuPD27ErTSVtId90+M4zFPNibFP50KprVdc8CR37BE7r8vuGgNYXmnI\nRLnGP9p3pVgFCktORuYS2J/6t84I3+A17nEoB4xvhTLeAinAW/uTQOUmNicOP4Ek\n2MsLL2kHgL8bLTmvXV4FX+PXphrDKg1XxzOYn0otuoqdAQrkK4og\n-----END RSA PRIVATE KEY-----",
            'algorithm' => Algorithm::RS256,
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api' --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.fm0h0Ec3yp7S3JNBLeFa2owu4a91IXFJs8NPgBUxgKKSfd_-Mqes2zxuxmkYpmQDG936u739mIDZyG9KQm0ER5HB243MVbFZHcaC7VAIqmoCZ4dcS6yoJ1ltH6vdwc8o3xkYVEWKvLr8a7ck21u-pASWB_tpqM7XtIu7xyCZhfpmxTbhNyTgsJ1HN4fVYrHn2535qYsetOTps_zM2cgVRQYbkp1RovL-ZDsp3rMxzEiGQ7F80JXh2fsTHKlpPqAyF40GEXvCZa0MIoRa7g1pIjRtNLYgOgO94YsSRGB0VDDsLNdXzkjv0Ujfk_uqtD0IKgt7ffQzh8b8dUl8onXA_g'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.fm0h0Ec3yp7S3JNBLeFa2owu4a91IXFJs8NPgBUxgKKSfd_-Mqes2zxuxmkYpmQDG936u739mIDZyG9KQm0ER5HB243MVbFZHcaC7VAIqmoCZ4dcS6yoJ1ltH6vdwc8o3xkYVEWKvLr8a7ck21u-pASWB_tpqM7XtIu7xyCZhfpmxTbhNyTgsJ1HN4fVYrHn2535qYsetOTps_zM2cgVRQYbkp1RovL-ZDsp3rMxzEiGQ7F80JXh2fsTHKlpPqAyF40GEXvCZa0MIoRa7g1pIjRtNLYgOgO94YsSRGB0VDDsLNdXzkjv0Ujfk_uqtD0IKgt7ffQzh8b8dUl8onXA_g' 'https://example.com/api'",
        ],
    ],

    'request with empty JWT key' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'jwt',
            'payload' => ['user' => 'test'],
            'key' => '',
        ],
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api'",
        ],
    ],

    // Line breaks formatting tests
    'basic GET request with line breaks' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'includeLineBreaks' => true,
        'expected' => [
            'curl' => "curl --location \\\n --request GET \\\n 'https://example.com/api'",
            'wget' => "wget --no-check-certificate --quiet \\\n --method GET \\\n --timeout=0 \\\n 'https://example.com/api'",
        ],
    ],

    'request with headers and line breaks' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'headers' => [
            'Authorization' => 'Bearer token',
            'Content-Type' => 'application/json',
        ],
        'includeLineBreaks' => true,
        'expected' => [
            'curl' => "curl --location \\\n --request POST \\\n 'https://example.com/api' \\\n --header \"Authorization: Bearer token\" \\\n --header \"Content-Type: application/json\"",
            'wget' => "wget --no-check-certificate --quiet \\\n --method POST \\\n --timeout=0 \\\n --header \"Authorization: Bearer token\" \\\n --header \"Content-Type: application/json\" \\\n 'https://example.com/api'",
        ],
    ],

    'request with authentication and line breaks' => [
        'method' => 'GET',
        'url' => 'https://example.com/api',
        'auth' => [
            'type' => 'bearer',
            'token' => 'your_token_here',
        ],
        'includeLineBreaks' => true,
        'expected' => [
            'curl' => "curl --location \\\n --request GET \\\n 'https://example.com/api' \\\n --header 'Authorization: Bearer your_token_here'",
            'wget' => "wget --no-check-certificate --quiet \\\n --method GET \\\n --timeout=0 \\\n --header 'Authorization: Bearer your_token_here' \\\n 'https://example.com/api'",
        ],
    ],

    'request with pre-encoded query parameters in the URL should not double encode when merging with queries' => [
        'method' => 'GET',
        'url' => 'https://example.com/api?param1=value%201with%20spaces',
        'queries' => [
            'param2' => 'value&with=special#chars',
        ],
        'encodeQuery' => true,
        'expected' => [
            'curl' => "curl --location --request GET 'https://example.com/api?param1=value%201with%20spaces&param2=value%26with%3Dspecial%23chars'",
            'wget' => "wget --no-check-certificate --quiet --method GET --timeout=0 'https://example.com/api?param1=value%201with%20spaces&param2=value%26with%3Dspecial%23chars'",
        ],
    ],

    'request with multiple encoded form params and line breaks' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => [
                'param1' => 'value with spaces',
                'param2' => 'value&with=special#chars',
            ],
        ],
        'includeLineBreaks' => true,
        'expected' => [
            'curl' => "curl --location \\\n --request POST \\\n 'https://example.com/api' \\\n --header 'Content-Type: application/x-www-form-urlencoded' \\\n --data-urlencode 'param1=value%20with%20spaces' \\\n --data-urlencode 'param2=value%26with%3Dspecial%23chars'",
            'wget' => "wget --no-check-certificate --quiet \\\n --method POST \\\n --timeout=0 \\\n --header 'Content-Type: application/x-www-form-urlencoded' \\\n --body-data 'param1=value%20with%20spaces&param2=value%26with%3Dspecial%23chars' \\\n 'https://example.com/api'",
        ],
    ],

    'request with multi value urlencoded form keys' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => [
                'test' => ['123', '124'],
            ],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'test=123' --data-urlencode 'test=124'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data 'test=123&test=124' 'https://example.com/api'",
        ],
    ],

    'request with multi value multipart form keys' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'multipart',
            'data' => [
                'test' => ['123', '124'],
            ],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --form 'test=123' --form 'test=124'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --body-data 'test=123&test=124' 'https://example.com/api'",
        ],
    ],

    'request with multi value form keys only allows scalar or null multi value types' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => [
                'test' => ['123', '124'],
                'key1' => ['value1' => ['nested'], 'value2'],
                'key2' => 'value2',
                'key3' => null,
            ],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'test=123' --data-urlencode 'test=124' --data-urlencode 'key1=value2' --data-urlencode 'key2=value2' --data-urlencode 'key3='",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data 'test=123&test=124&key1=value2&key2=value2&key3=' 'https://example.com/api'",
        ],
    ],

    'request with multi value form keys with empty values' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => [
                'test' => ['123', '', '124'],
                'key1' => ['value1' => '', 'value2'],
                'key2' => '',
            ],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'test=123' --data-urlencode 'test=' --data-urlencode 'test=124' --data-urlencode 'key1=' --data-urlencode 'key1=value2' --data-urlencode 'key2='",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data 'test=123&test=&test=124&key1=&key1=value2&key2=' 'https://example.com/api'",
        ],
    ],

    'request with multi value form keys with numeric keys' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => [
                '1' => 'value1',
                '2' => 'value2',
                '3' => 'value3',
            ],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode '1=value1' --data-urlencode '2=value2' --data-urlencode '3=value3'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data '1=value1&2=value2&3=value3' 'https://example.com/api'",
        ],
    ],

    'request with non-scalar key will be filtered out' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => [
                'key1' => ['value1', 'value2'],
                'key2' => ['value3', 123],
                'key3' => ['value4', null],
                'key4' => ['value5', []],
                'key5' => [new stdClass],
            ],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'key1=value1' --data-urlencode 'key1=value2' --data-urlencode 'key2=value3' --data-urlencode 'key2=123' --data-urlencode 'key3=value4' --data-urlencode 'key3=' --data-urlencode 'key4=value5'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data 'key1=value1&key1=value2&key2=value3&key2=123&key3=value4&key3=&key4=value5' 'https://example.com/api'",
        ],
    ],

    'request with null and empty form values' => [
        'method' => 'POST',
        'url' => 'https://example.com/api',
        'body' => [
            'type' => 'form',
            'data' => [
                'key1' => null,
                'key2' => '',
                'key3' => 'value3',
            ],
        ],
        'expected' => [
            'curl' => "curl --location --request POST 'https://example.com/api' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'key1=' --data-urlencode 'key2=' --data-urlencode 'key3=value3'",
            'wget' => "wget --no-check-certificate --quiet --method POST --timeout=0 --header 'Content-Type: application/x-www-form-urlencoded' --body-data 'key1=&key2=&key3=value3' 'https://example.com/api'",
        ],
    ],
]);
