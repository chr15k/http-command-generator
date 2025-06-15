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
- [Extending with Custom Generators](#extending-with-custom-generators)
- [Full Examples](#full-examples)

## Introduction

HTTP CLI Generator is a PHP library that allows you to generate CLI commands for HTTP requests using a fluent builder pattern. It currently supports cURL commands but is designed to be extended to support other command formats.

## Getting Started

First, install the library using Composer:

```bash
composer require chr15k/http-cli-generator
```

Then, create a new builder instance:

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

$builder = HttpRequestBuilder::create();
```

## Request Methods

You can specify the HTTP method for your request in two ways:

```php
// Using dedicated method helpers
$builder->get()->url('https://api.example.com/users');
$builder->post()->url('https://api.example.com/users');
$builder->put()->url('https://api.example.com/users/1');
$builder->delete()->url('https://api.example.com/users/1');

// Using the generic method() method for any HTTP verb
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

Add Digest authentication to your request:

```php
$builder->withDigestAuth('username', 'password');
```

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

## Full Examples

### GET Request with Query Parameters

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/search?q=test&page=1')
    ->get()
    ->header('Accept', 'application/json')
    ->withBearerToken('your-access-token')
    ->toCurl();
```

### POST Request with JSON Body and Authentication

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

$curl = HttpRequestBuilder::create()
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
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

$curl = HttpRequestBuilder::create()
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
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;

$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/users/123')
    ->put()
    ->withBearerToken('your-access-token')
    ->withJsonBody([
        'name' => 'John Updated',
        'email' => 'john.updated@example.com'
    ])
    ->toCurl();
```

### DELETE Request with JWT Authentication

```php
use Chr15k\HttpCliGenerator\Builder\HttpRequestBuilder;
use Chr15k\AuthGenerator\Enums\Algorithm;

$curl = HttpRequestBuilder::create()
    ->url('https://api.example.com/users/123')
    ->delete()
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
