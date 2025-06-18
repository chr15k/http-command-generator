# HTTP CLI Generator

[![Latest Stable Version](https://poser.pugx.org/chr15k/http-c// Using a Bearer Token
$curl = CommandBuilder::get()
    ->url('https://api.example.com/protected-resource')
    ->withBearerToken('your-access-token')
    ->toCurl();erator/v)](https://packagist.org/packages/chr15k/http-command-generator) [![Total Downloads](https://poser.pugx.org/chr15k/http-command-generator/downloads)](https://packagist.org/packages/chr15k/http-command-generator) [![Latest Unstable Version](https://poser.pugx.org/chr15k/http-command-generator/v/unstable)](https://packagist.org/packages/chr15k/http-command-generator) [![License](https://poser.pugx.org/chr15k/http-command-generator/license)](https://packagist.org/packages/chr15k/http-command-generator) [![PHP Version Require](https://poser.pugx.org/chr15k/http-command-generator/require/php)](https://packagist.org/packages/chr15k/http-command-generator)

A PHP library for generating HTTP CLI commands with a fluent builder API.

## Installation

Requires [PHP 8.2+](https://www.php.net/releases/)

```bash
composer require chr15k/http-command-generator
```

## Features

- **Fluent Builder API**: Construct HTTP requests with a clean, chainable interface
- **Multiple HTTP Methods**: Support for GET, POST, PUT, DELETE and custom methods
- **Authentication Options**: Basic Auth, Bearer Token, API Key, JWT, and Digest Auth
- **Body Formats**: JSON, form URL-encoded, multipart form data, and binary file data
- **Zero External Dependencies**: Only requires chr15k/http-command-generator for advanced auth options

## Basic Usage

### Simple GET Request

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Build a simple GET request with cURL
$curl = CommandBuilder::get()
    ->url('https://api.example.com/users')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/users'

// Generate the same request with wget
$wget = CommandBuilder::get()
    ->url('https://api.example.com/users')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method GET --timeout=0 'https://api.example.com/users'
```

### POST Request with JSON Body

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Build a POST request with JSON data using cURL
$curl = CommandBuilder::post()
    ->url('https://api.example.com/users')
    ->withJsonBody([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/users' \
//  --header "Content-Type: application/json" \
//  --data '{"name":"John Doe","email":"john@example.com"}'

// Generate the same POST request using wget
$wget = CommandBuilder::post()
    ->url('https://api.example.com/users')
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
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Using a Bearer Token
$curl = CommandBuilder::get()
    ->url('https://api.example.com/protected-resource')
    ->withBearerToken(token: 'your-access-token')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header "Authorization: Bearer your-access-token"
```

#### Basic Authentication

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Using Basic Auth
$curl = CommandBuilder::get()
    ->url('https://api.example.com/protected-resource')
    ->withBasicAuth(
        username: 'username',
        password: 'password'
    )
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header "Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ="
```

#### API Key Authentication

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Using an API Key in header
$curl = CommandBuilder::get()
    ->url('https://api.example.com/data')
    ->withApiKey(
        key: 'X-API-Key',
        value: 'your-api-key'
    )
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/data' \
//  --header "X-API-Key: your-api-key"

// Using an API Key in query string
$curl = CommandBuilder::get()
    ->url('https://api.example.com/data')
    ->withApiKey(
        key: 'X-API-Key',
        value: 'your-api-key',
        inQuery: true
    )
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/data?api_key=your-api-key'
```

#### Digest Authentication

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;

// Basic Digest Auth
$curl = CommandBuilder::get()
    ->url('https://api.example.com/protected-resource')
    ->withDigestAuth(
        username: 'username',
        password: 'password'
    )
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header 'Authorization: Digest username="username", realm="", nonce="", uri="", algorithm="MD5", response="..."'

// Advanced Digest Auth with all parameters
$curl = CommandBuilder::get()
    ->url('https://api.example.com/protected-resource')
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
$wget = CommandBuilder::create()
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
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Form URL-encoded data
$curl = CommandBuilder::post()
    ->url('https://api.example.com/form')
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
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Multipart form data (useful for file uploads)
$curl = CommandBuilder::post()
    ->url('https://api.example.com/upload')
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
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Send binary file content
$curl = CommandBuilder::post()
    ->url('https://api.example.com/upload-binary')
    ->withBinaryBody('/path/to/file.bin')
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/upload-binary' \
//  --data-binary '@/path/to/file.bin'
```

### Custom Request Headers

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Adding custom headers
$curl = CommandBuilder::get()
    ->url('https://api.example.com/data')
    ->header('Accept', 'application/json')
    ->header('Cache-Control', 'no-cache')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/data' \
//  --header "Accept: application/json" \
//  --header "Cache-Control: no-cache"

// Alternative way to add multiple headers
$curl = CommandBuilder::get()
    ->url('https://api.example.com/data')
    ->headers([
        'Accept' => 'application/json',
        'Cache-Control' => 'no-cache',
        'X-Custom-Header' => 'CustomValue',
    ])
    ->toCurl();
```

## Documentation

- [User Guide](https://github.com/chr15k/http-command-generator/blob/main/docs/USER_GUIDE.md) - Comprehensive guide with examples
- [API Cheat Sheet](https://github.com/chr15k/http-command-generator/blob/main/docs/API_CHEATSHEET.md) - Quick reference of all available methods

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
