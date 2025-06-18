# User Guide

This guide provides comprehensive examples of how to use the HTTP CLI Generator library.

## Table of Contents

- [Introduction](#introduction)
- [Getting Started](#getting-started)
- [Request Methods](#request-methods)
- [Working with Headers](#working-with-headers)
- [Authentication](#authentication)
  - [Bearer Token](#bearer-token)
  - [Basic Authentication](#basic-authentication)
  - [API Key](#api-key)
  - [JWT Authentication](#jwt-authentication)
  - [Digest Authentication](#digest-authentication)
- [Request Bodies](#request-bodies)
  - [JSON Bodies](#json-bodies)
  - [Form URL-encoded](#form-url-encoded)
  - [Multipart Form Data](#multipart-form-data)
  - [Binary Data](#binary-data)
- [Command Generators](#command-generators)
  - [cURL Generator](#curl-generator)
  - [wget Generator](#wget-generator)
- [Extending with Custom Generators](#extending-with-custom-generators)
- [Full Examples](#full-examples)

## Introduction

HTTP CLI Generator is a PHP library that allows you to generate CLI commands for HTTP requests using a fluent builder pattern. It supports cURL and wget commands out of the box and is designed to be extended to support other command formats.

## Getting Started

First, install the library using Composer:

```bash
composer require chr15k/http-command-generator
```

Then, create a new builder instance with the appropriate HTTP method:

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Create instances with specific HTTP methods
$getRequest = CommandBuilder::get();
$postRequest = CommandBuilder::post();
$putRequest = CommandBuilder::put();
$deleteRequest = CommandBuilder::delete();
$patchRequest = CommandBuilder::patch();
$headRequest = CommandBuilder::head();
$optionsRequest = CommandBuilder::options();
```

## Request Methods

You can specify the HTTP method when creating the builder or change it later:

```php
// Create a builder with a specific HTTP method
$builder = CommandBuilder::get()->url('https://api.example.com/users');
$builder = CommandBuilder::post()->url('https://api.example.com/users');
$builder = CommandBuilder::put()->url('https://api.example.com/users/1');
$builder = CommandBuilder::delete()->url('https://api.example.com/users/1');

// Or change the method after creation
$builder = CommandBuilder::get();
$builder->method('PATCH')->url('https://api.example.com/users/1');
```

## Working with Headers

Add headers to your request using the `header()` method for a single header or the `headers()` method for multiple headers:

```php
// Add a single header
$builder->header('Accept', 'application/json');

// Add multiple headers
$builder->headers([
    'Accept' => 'application/json',
    'User-Agent' => 'MyApp/1.0',
    'X-Custom-Header' => 'CustomValue'
]);
```

## Authentication

### Bearer Token

Add a Bearer token to your request:

```php
$builder->withBearerToken('your-access-token');
```

This will add the `Authorization: Bearer your-access-token` header to your request.

### Basic Authentication

Add Basic authentication to your request:

```php
$builder->withBasicAuth('username', 'password');
```

This will encode the credentials and add the `Authorization: Basic <encoded-credentials>` header.

### API Key

Add an API key to your request:

```php
// In the header
$builder->withApiKey('X-API-Key', 'your-api-key');

// Or in the query string
$builder->withApiKey('api_key', 'your-api-key', true);
```

### JWT Authentication

Add JWT authentication to your request:

```php
use Chr15k\AuthGenerator\Enums\Algorithm;

$builder->withJWTAuth(
    key: 'your-secret-key',
    payload: [
        'user_id' => 123,
        'role' => 'admin'
    ],
    algorithm: Algorithm::HS256,
    headerPrefix: 'Bearer'
);
```

This will generate a JWT and add it as a Bearer token in the Authorization header.

### Digest Authentication

Add Digest authentication to your request. Digest authentication can be used with minimal parameters or with full control over all digest components:

```php
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;

// Basic usage with only username and password
$builder->withDigestAuth(
    username: 'username',
    password: 'password'
);

// Advanced usage with all parameters
$builder->withDigestAuth(
    username: 'username',
    password: 'password',
    algorithm: DigestAlgorithm::MD5, // Default is MD5
    realm: 'example.com',
    method: 'GET',
    uri: '/protected-resource',
    nonce: 'nonce_value',
    nc: '00000001',
    cnonce: 'cnonce_value',
    qop: 'auth'
);
```

The complete set of parameters allows you to have fine-grained control over the digest authentication process, which can be important when working with specific server implementations or when testing security mechanisms.

## Request Bodies

### JSON Bodies

Add a JSON body to your request:

```php
// Using an array that will be converted to JSON
$builder->withJsonBody([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Using raw JSON
$builder->withRawJsonBody('{"name":"John Doe","email":"john@example.com"}');
```

### Form URL-encoded

Add form URL-encoded data to your request:

```php
$builder->withFormBody([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);
```

### Multipart Form Data

Add multipart form data to your request (useful for file uploads):

```php
$builder->withMultipartBody([
    'profile_picture' => '@/path/to/image.jpg',
    'name' => 'John Doe'
]);
```

### Binary Data

Add binary file data to your request:

```php
$builder->withBinaryBody('/path/to/file.bin');
```

This sends the raw binary content of the file without any additional encoding.

## Command Generators

HTTP CLI Generator supports multiple command-line tools for making HTTP requests. You can choose which generator to use based on your preferences or requirements.

### cURL Generator

cURL is the default generator and can be explicitly specified using the `toCurl()` method:

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

$curl = CommandBuilder::get()
    ->url('https://api.example.com/users')
    ->toCurl();
```

You can also use the generic `to()` method:

```php
$curl = CommandBuilder::get()
    ->url('https://api.example.com/users')
    ->to('curl');
```

### wget Generator

wget is an alternative command-line tool for making HTTP requests. Use the `toWget()` method to generate wget commands:

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

$wget = CommandBuilder::get()
    ->url('https://api.example.com/users')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method GET --timeout=0 'https://api.example.com/users'
```

You can also use the generic `to()` method:

```php
$wget = CommandBuilder::get()
    ->url('https://api.example.com/users')
    ->to('wget');
```

#### wget specific characteristics

- Uses `--body-data` for request bodies
- Uses `--method` to specify HTTP methods
- Sets default timeout to 0 (--timeout=0)
- Uses `--no-check-certificate` and `--quiet` by default

## Extending with Custom Generators

You can extend the library with your own command generators. For example:

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

$customCommand = CommandBuilder::create()
    ->url('https://api.example.com/users')
    ->get()
    ->to('myCustomGenerator');
```

## Full Examples

### GET Request with Query Parameters

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

// Using cURL
$curl = CommandBuilder::create()
    ->url('https://api.example.com/search?q=test&page=1')
    ->get()
    ->header('Accept', 'application/json')
    ->withBearerToken('your-access-token')
    ->toCurl();

// Using wget
$wget = CommandBuilder::create()
    ->url('https://api.example.com/search?q=test&page=1')
    ->get()
    ->header('Accept', 'application/json')
    ->withBearerToken('your-access-token')
    ->toWget();
```

### POST Request with JSON Body and Authentication

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

$curl = CommandBuilder::create()
    ->url('https://api.example.com/users')
    ->post()
    ->withBasicAuth('username', 'password')
    ->withJsonBody([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'user'
    ])
    ->toCurl();
```

### File Upload with Multipart Form Data

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

$curl = CommandBuilder::create()
    ->url('https://api.example.com/upload')
    ->post()
    ->withApiKey('X-API-Key', 'your-api-key')
    ->withMultipartBody([
        'file' => '@/path/to/file.pdf',
        'description' => 'My important document'
    ])
    ->toCurl();
```

### PUT Request to Update a Resource

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

$curl = CommandBuilder::put()
    ->url('https://api.example.com/users/123')
    ->withBearerToken('your-access-token')
    ->withJsonBody([
        'name' => 'John Updated',
        'email' => 'john.updated@example.com'
    ])
    ->toCurl();
```

### DELETE Request with JWT Authentication

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;
use Chr15k\AuthGenerator\Enums\Algorithm;

$curl = CommandBuilder::delete()
    ->url('https://api.example.com/users/123')
    ->withJWTAuth(
        key: 'your-secret-key',
        payload: [
            'user_id' => 456,
            'role' => 'admin'
        ],
        algorithm: Algorithm::HS256
    )
    ->toCurl();
```

### DELETE Request with wget

```php
use Chr15k\HttpCommand\Builder\CommandBuilder;

$wget = CommandBuilder::delete()
    ->url('https://api.example.com/users/123')
    ->withBearerToken('your-access-token')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method DELETE --timeout=0 \
//   --header "Authorization: Bearer your-access-token" \
//   'https://api.example.com/users/123'
```
