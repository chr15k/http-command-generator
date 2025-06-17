# HTTP CLI Generator

[![Latest Stable Version](https://img.shields.io/packagist/v/chr15k/http-cli-generator.svg?style=flat-square)](https://packagist.org/packages/chr15k/http-cli-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/chr15k/http-cli-generator.svg?style=flat-square)](https://packagist.org/packages/chr15k/http-cli-generator)
[![License](https://img.shields.io/packagist/l/chr15k/http-cli-generator.svg?style=flat-square)](https://packagist.org/packages/chr15k/http-cli-generator)
[![PHP Version Require](https://img.shields.io/packagist/php-v/chr15k/http-cli-generator.svg?style=flat-square)](https://packagist.org/packages/chr15k/http-cli-generator)

A PHP library for generating HTTP CLI commands with a fluent builder API.

## Installation

Requires [PHP 8.2+](https://www.php.net/releases/)

```bash
composer require chr15k/http-cli-generator
```

## Features

- **Fluent Builder API**: Construct HTTP requests with a clean, chainable interface
- **Multiple HTTP Methods**: Support for GET, POST, PUT, DELETE and custom methods
- **Authentication Options**: Basic Auth, Bearer Token, API Key, JWT, and Digest Auth
- **Body Formats**: JSON, form URL-encoded, multipart form data, and binary file data
- **Multiple Command Generators**: Support for cURL and wget commands out of the box
- **Extensible Design**: Add custom command generators beyond the included ones
- **Zero External Dependencies**: Only requires chr15k/php-auth-generator for advanced auth options

## Basic Usage

### Simple GET Request

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Build a simple GET request with cURL
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/users')
    ->get()
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/users'

// Generate the same request with wget
$wget = HttpRequestBuilder::create()
    ->url('https://api.example.com/users')
    ->get()
    ->toWget();

// Output: wget --no-check-certificate --quiet --method GET --timeout=0 'https://api.example.com/users'
```

### POST Request with JSON Body

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Build a POST request with JSON data using cURL
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/users')
    ->post()
    ->withJsonBody([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/users' \
//  --header "Content-Type: application/json" \
//  --data '{"name":"John Doe","email":"john@example.com"}'

// Generate the same POST request using wget
$wget = HttpRequestBuilder::create()
    ->url('https://api.example.com/users')
    ->post()
    ->withJsonBody([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->toWget();

// Output: wget --no-check-certificate --quiet --method POST --timeout=0 \
//  --header 'Content-Type: application/json' \
//  --body-data '{"name":"John Doe","email":"john@example.com"}' \
//  'https://api.example.com/users'
```

### Authentication Examples

#### Bearer Token Authentication

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Using a Bearer Token
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/protected-resource')
    ->get()
    ->withBearerToken('your-access-token')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header "Authorization: Bearer your-access-token"
```

#### Basic Authentication

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Using Basic Auth
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/protected-resource')
    ->get()
    ->withBasicAuth('username', 'password')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header "Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ="
```

#### API Key Authentication

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Using an API Key in header
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/data')
    ->get()
    ->withApiKey('X-API-Key', 'your-api-key', false)
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/data' \
//  --header "X-API-Key: your-api-key"

// Using an API Key in query string
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/data')
    ->get()
    ->withApiKey('api_key', 'your-api-key', true)
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/data?api_key=your-api-key'
```

#### Digest Authentication

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;

// Basic Digest Auth
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/protected-resource')
    ->get()
    ->withDigestAuth(
        username: 'username',
        password: 'password'
    )
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header 'Authorization: Digest username="username", realm="", nonce="", uri="", algorithm="MD5", response="..."'

// Advanced Digest Auth with all parameters
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/protected-resource')
    ->get()
    ->withDigestAuth(
        username: 'username',
        password: 'password',
        algorithm: DigestAlgorithm::MD5,
        realm: 'example.com',
        method: 'GET',
        uri: '/protected-resource',
        nonce: 'nonce_value',
        nc: '00000001',
        cnonce: 'cnonce_value',
        qop: 'auth'
    )
    ->toCurl();

// Using Digest Auth with wget
$wget = HttpRequestBuilder::create()
    ->url('https://api.example.com/protected-resource')
    ->get()
    ->withDigestAuth(
        username: 'username',
        password: 'password',
        algorithm: DigestAlgorithm::MD5,
        realm: 'example_realm',
        method: 'GET',
        uri: '/protected-resource',
        nonce: 'nonce_value',
        nc: '00000001',
        cnonce: 'cnonce_value',
        qop: 'auth'
    )
    ->toWget();

// Output: wget --no-check-certificate --quiet --method GET --timeout=0 \
//  --header 'Authorization: Digest username="username", realm="example_realm", nonce="nonce_value", uri="/protected-resource", algorithm="MD5", qop=auth, nc=00000001, cnonce="cnonce_value", response="..."' \
//  'https://api.example.com/protected-resource'
```

## Advanced Usage

### Working with Request Bodies

#### Form URL-encoded Data

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Form URL-encoded data
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/form')
    ->post()
    ->withFormBody([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/form' \
//  --header "Content-Type: application/x-www-form-urlencoded" \
//  --data-urlencode "name=John Doe" \
//  --data-urlencode "email=john@example.com"
```

#### Multipart Form Data

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Multipart form data (useful for file uploads)
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/upload')
    ->post()
    ->withMultipartBody([
        'file' => '@/path/to/file.jpg',
        'name' => 'Profile Photo',
    ])
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/upload' \
//  --form "file=@/path/to/file.jpg" \
//  --form "name=Profile Photo"
```

#### Binary Data

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Send binary file content
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/upload-binary')
    ->post()
    ->withBinaryBody('/path/to/file.bin')
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/upload-binary' \
//  --data-binary '@/path/to/file.bin'
```

### Custom Request Headers

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

// Adding custom headers
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/data')
    ->get()
    ->header('Accept', 'application/json')
    ->header('Cache-Control', 'no-cache')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/data' \
//  --header "Accept: application/json" \
//  --header "Cache-Control: no-cache"

// Alternative way to add multiple headers
$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/data')
    ->get()
    ->headers([
        'Accept' => 'application/json',
        'Cache-Control' => 'no-cache',
        'X-Custom-Header' => 'CustomValue',
    ])
    ->toCurl();
```

## Documentation

- [User Guide](https://github.com/chr15k/http-cli-generator/blob/main/docs/USER_GUIDE.md) - Comprehensive guide with examples
- [API Cheat Sheet](https://github.com/chr15k/http-cli-generator/blob/main/docs/API_CHEATSHEET.md) - Quick reference of all available methods

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
