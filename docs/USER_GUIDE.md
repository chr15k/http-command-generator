# User Guide

This guide provides comprehensive examples of how to use the HTTP Command Generator library.

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
  - [Generic to() Method](#generic-to-method)
  - [cURL Generator](#curl-generator)
  - [wget Generator](#wget-generator)
- [Full Examples](#full-examples)
- [Query Parameters](#query-parameters)
- [Advanced Method Chaining Examples](#advanced-method-chaining-examples)

## Introduction

HTTP Command Generator is a PHP library that allows you to generate CLI commands for HTTP requests using a fluent builder pattern. It supports cURL and wget commands out of the box and is designed to be extended to support other command formats.

## Getting Started

First, install the library using Composer:

```bash
composer require chr15k/http-command-generator
```

Then, create a new request instance with the appropriate HTTP method:

```php
use Chr15k\HttpCommand\HttpCommand;

// Create instances with specific HTTP methods
$getRequest = HttpCommand::get('https://api.example.com/users');
$postRequest = HttpCommand::post('https://api.example.com/users');
$putRequest = HttpCommand::put('https://api.example.com/users/1');
$deleteRequest = HttpCommand::delete('https://api.example.com/users/1');
$patchRequest = HttpCommand::patch('https://api.example.com/users/1');
$headRequest = HttpCommand::head('https://api.example.com/users/1');
$optionsRequest = HttpCommand::options('https://api.example.com/users');
```

## Request Methods

You can specify the HTTP method when creating the request or change it later:

```php
// Create a request with a specific HTTP method and URL
$builder = HttpCommand::get('https://api.example.com/users');
$builder = HttpCommand::post('https://api.example.com/users');
$builder = HttpCommand::put('https://api.example.com/users/1');
$builder = HttpCommand::delete('https://api.example.com/users/1');

// Or change the method after creation
$builder = HttpCommand::get();
$builder->method('PATCH')->url('https://api.example.com/users/1');
```

## Working with Headers

Add headers to your request using the `header()` method. The method is chainable, allowing you to add multiple headers:

```php
// Add a single header
$builder->header('Accept', 'application/json');

// Chain multiple headers
$builder->header('Accept', 'application/json')
        ->header('User-Agent', 'MyApp/1.0')
        ->header('Cache-Control', 'no-cache')
        ->header('X-Custom-Header', 'custom-value');

// Headers can be added throughout the chain
$curl = HttpCommand::get('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->auth()->bearerToken('your-token')
    ->header('X-Request-ID', 'req-123')
    ->query('page', '1')
    ->header('Cache-Control', 'no-cache')
    ->toCurl();
```

## Authentication

### Bearer Token

Add a Bearer token to your request:

```php
$builder->auth()->bearerToken('your-access-token');
```

This will add the `Authorization: Bearer your-access-token` header to your request.

### Basic Authentication

Add Basic authentication to your request:

```php
$builder->auth()->basic('username', 'password');
```

This will encode the credentials and add the `Authorization: Basic <encoded-credentials>` header.

### API Key

Add an API key to your request:

```php
// In the header
$builder->auth()->apiKey('X-API-Key', 'your-api-key');

// Or in the query string
$builder->auth()->apiKey('api_key', 'your-api-key', true);
```

### JWT Authentication

Add JWT authentication to your request:

```php
use Chr15k\AuthGenerator\Enums\Algorithm;

// Basic JWT with default settings
$builder->auth()->jwt(
    key: 'your-secret-key',
    payload: [
        'user_id' => 123,
        'role' => 'admin'
    ]
);

// Advanced JWT with custom algorithm and header prefix
$builder->auth()->jwt(
    key: 'your-secret-key',
    payload: [
        'user_id' => 123,
        'role' => 'admin',
        'exp' => time() + 3600  // 1 hour expiration
    ],
    headers: [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ],
    algorithm: Algorithm::HS256,
    headerPrefix: 'Bearer'
);

// JWT in query string instead of header
$builder->auth()->jwt(
    key: 'your-secret-key',
    payload: ['user_id' => 123],
    inQuery: true,
    queryKey: 'jwt_token'
);
```

This will generate a JWT and add it as a Bearer token in the Authorization header (or as a query parameter if specified).

### Digest Authentication

Add Digest authentication to your request. Digest authentication can be used with minimal parameters or with full control over all digest components:

```php
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;

// Basic usage with only username and password
$builder->auth()->digest('username', 'password');

// Advanced usage with all parameters
$builder->auth()->digest(
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

Add a JSON body to your request using the `json()` method:

```php
// Using an array that will be converted to JSON automatically
$builder->json([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'preferences' => [
        'newsletter' => true,
        'notifications' => false
    ]
]);

// Using raw JSON string with preserveAsRaw = true
$jsonString = '{"name":"John Doe","email":"john@example.com","timestamp":"2024-01-01T12:00:00Z"}';
$builder->json($jsonString, true);

// Without preserveAsRaw (default false), the string would be JSON-encoded again
$builder->json('{"already":"json"}');           // Results in: "{\"already\":\"json\"}"
$builder->json('{"already":"json"}', true);     // Results in: {"already":"json"}

// Complex nested data structures
$builder->json([
    'user' => [
        'profile' => [
            'name' => 'John Doe',
            'contacts' => [
                'email' => 'john@example.com',
                'phone' => '+1234567890'
            ]
        ],
        'permissions' => ['read', 'write'],
        'metadata' => [
            'created_at' => date('c'),
            'source' => 'api'
        ]
    ]
]);
```

### Form URL-encoded

Add form URL-encoded data to your request:

```php
$builder->form([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);
```

### Multipart Form Data

Add multipart form data to your request (useful for file uploads):

```php
$builder->multipart([
    'profile_picture' => '@/path/to/image.jpg',
    'name' => 'John Doe'
]);
```

### Binary Data

Add binary file data to your request:

```php
$builder->file('/path/to/file.bin');
```

This sends the raw binary content of the file without any additional encoding.

## Command Generators

HTTP Command Generator supports multiple command-line tools for making HTTP requests. You can choose which generator to use based on your preferences or requirements.

### Generic to() Method

You can use the generic `to()` method to specify which generator to use:

```php
use Chr15k\HttpCommand\HttpCommand;

// Generate cURL command
$curl = HttpCommand::get('https://api.example.com/users')
    ->to('curl');

// Generate wget command
$wget = HttpCommand::get('https://api.example.com/users')
    ->to('wget');

// This is equivalent to using the specific methods:
$curl = HttpCommand::get('https://api.example.com/users')->toCurl();
$wget = HttpCommand::get('https://api.example.com/users')->toWget();
```

### cURL Generator

cURL is the default generator and can be explicitly specified using the `toCurl()` method:

```php
use Chr15k\HttpCommand\HttpCommand;

$curl = HttpCommand::get('https://api.example.com/users')
    ->toCurl();
```

### wget Generator

wget is an alternative command-line tool for making HTTP requests. Use the `toWget()` method to generate wget commands:

```php
use Chr15k\HttpCommand\HttpCommand;

$wget = HttpCommand::get('https://api.example.com/users')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method GET --timeout=0 'https://api.example.com/users'
```

#### wget specific characteristics

- Uses `--body-data` for request bodies
- Uses `--method` to specify HTTP methods
- Sets default timeout to 0 (--timeout=0)
- Uses `--no-check-certificate` and `--quiet` by default

## Full Examples

### GET Request with Query Parameters

```php
use Chr15k\HttpCommand\HttpCommand;

// Using cURL
$curl = HttpCommand::get('https://api.example.com/search?q=test&page=1')
    ->header('Accept', 'application/json')
    ->auth()->bearerToken('your-access-token')
    ->toCurl();

// Using wget
$wget = HttpCommand::get('https://api.example.com/search?q=test&page=1')
    ->header('Accept', 'application/json')
    ->auth()->bearerToken('your-access-token')
    ->toWget();
```

### POST Request with JSON Body and Authentication

```php
use Chr15k\HttpCommand\HttpCommand;

$curl = HttpCommand::post('https://api.example.com/users')
    ->auth()->basic('username', 'password')
    ->json([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'user'
    ])
    ->toCurl();
```

### File Upload with Multipart Form Data

```php
use Chr15k\HttpCommand\HttpCommand;

$curl = HttpCommand::post('https://api.example.com/upload')
    ->auth()->apiKey('X-API-Key', 'your-api-key')
    ->multipart([
        'file' => '@/path/to/file.pdf',
        'description' => 'My important document'
    ])
    ->toCurl();
```

### PUT Request to Update a Resource

```php
use Chr15k\HttpCommand\HttpCommand;

$curl = HttpCommand::put('https://api.example.com/users/123')
    ->auth()->bearerToken('your-access-token')
    ->json([
        'name' => 'John Updated',
        'email' => 'john.updated@example.com'
    ])
    ->toCurl();
```

### DELETE Request with JWT Authentication

```php
use Chr15k\HttpCommand\HttpCommand;
use Chr15k\AuthGenerator\Enums\Algorithm;

$curl = HttpCommand::delete('https://api.example.com/users/123')
    ->auth()->jwt(
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
use Chr15k\HttpCommand\HttpCommand;

$wget = HttpCommand::delete('https://api.example.com/users/123')
    ->auth()->bearerToken('your-access-token')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method DELETE --timeout=0 \
//   --header "Authorization: Bearer your-access-token" \
//   'https://api.example.com/users/123'
```

## Query Parameters

Add query parameters to your request using the `query()` method. Like headers, this method is chainable:

```php
// Add a single query parameter
$builder->query('page', '1');

// Chain multiple query parameters
$builder->query('page', '1')
        ->query('limit', '10')
        ->query('sort', 'created_at')
        ->query('order', 'desc');

// Query parameters can be mixed with other methods
$curl = HttpCommand::get('https://api.example.com/search')
    ->query('q', 'search term')
    ->header('Accept', 'application/json')
    ->query('category', 'books')
    ->query('page', '2')
    ->auth()->bearerToken('token123')
    ->query('limit', '20')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/search?q=search+term&category=books&page=2&limit=20' \
//  --header "Accept: application/json" \
//  --header "Authorization: Bearer token123"
```

### Query Parameter Encoding

By default, query parameters are NOT URL-encoded. You can control this behavior:

```php
// Default behavior - query parameters are NOT encoded
$builder->query('search', 'hello world')
        ->toCurl();
// Results in: ?search=hello world

// Enable encoding to URL-encode query parameters
$builder->query('search', 'hello world')
        ->encodeQuery()
        ->toCurl();
// Results in: ?search=hello+world

// Disable encoding again (it's disabled by default)
$builder->encodeQuery(false);
```

Query parameters will NOT be automatically URL-encoded by default. Use `encodeQuery()` to enable encoding when needed.

## Advanced Method Chaining Examples

The HTTP Command Generator uses a fluent builder pattern, which means you can chain methods together in any order to build your HTTP requests. Here are some comprehensive examples:

### Complex API Request with Multiple Features

```php
use Chr15k\HttpCommand\HttpCommand;

// Build a complex POST request with multiple headers, query parameters, and authentication
$curl = HttpCommand::post('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->header('Content-Type', 'application/json')
    ->header('User-Agent', 'MyApp/1.0.0')
    ->query('notify', 'true')
    ->query('source', 'api')
    ->auth()->bearerToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...')
    ->json([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'role' => 'user'
    ])
    ->toCurl();

// The same request can be built in a different order
$curl = HttpCommand::post()
    ->url('https://api.example.com/users')
    ->auth()->bearerToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...')
    ->query('notify', 'true')
    ->header('Accept', 'application/json')
    ->json(['name' => 'John Doe', 'email' => 'john.doe@example.com'])
    ->query('source', 'api')
    ->header('User-Agent', 'MyApp/1.0.0')
    ->toCurl();
```

### Search API with Pagination and Filtering

```php
// Build a search request with multiple query parameters and headers
$searchCommand = HttpCommand::get('https://api.example.com/search')
    ->query('q', 'PHP framework')
    ->query('category', 'technology')
    ->query('page', '2')
    ->query('per_page', '25')
    ->query('sort_by', 'relevance')
    ->query('order', 'desc')
    ->header('Accept', 'application/json')
    ->header('Accept-Language', 'en-US,en;q=0.9')
    ->header('Cache-Control', 'no-cache')
    ->auth()->apiKey('X-API-Key', 'your-api-key-here');

// Generate for different tools
$curl = $searchCommand->toCurl();
$wget = $searchCommand->toWget();
$generic = $searchCommand->to('curl'); // Same as toCurl()
```

### File Upload with Authentication

```php
// Upload a file with custom headers and form data
$uploadCommand = HttpCommand::post('https://api.example.com/files/upload')
    ->header('Accept', 'application/json')
    ->header('X-Client-Version', '2.1.0')
    ->query('folder', 'documents')
    ->query('overwrite', 'false')
    ->auth()->basic('username', 'password')
    ->multipart([
        'file' => '@/path/to/document.pdf',
        'description' => 'Important document',
        'tags' => 'work,important'
    ])
    ->toCurl();
```

### API Request with JWT Authentication

```php
use Chr15k\AuthGenerator\Enums\Algorithm;

// Build a request with JWT authentication and custom claims
$apiRequest = HttpCommand::get('https://api.example.com/protected/data')
    ->header('Accept', 'application/json')
    ->header('Content-Type', 'application/json')
    ->query('include', 'metadata')
    ->query('format', 'detailed')
    ->auth()->jwt(
        key: 'your-secret-key',
        payload: [
            'user_id' => 12345,
            'role' => 'admin',
            'permissions' => ['read', 'write'],
            'exp' => time() + 3600
        ],
        algorithm: Algorithm::HS256
    )
    ->toCurl();
```

### Debugging and Development Request

```php
// Build a request for testing with various debugging headers
$debugRequest = HttpCommand::post('https://staging-api.example.com/test')
    ->header('Accept', 'application/json')
    ->header('Content-Type', 'application/json')
    ->header('X-Debug-Mode', 'true')
    ->header('X-Request-ID', uniqid('req_'))
    ->header('X-Client-IP', '192.168.1.100')
    ->query('debug', 'true')
    ->query('verbose', '1')
    ->query('trace_id', 'trace_' . time())
    ->auth()->bearerToken('dev-token-123')
    ->json([
        'test_data' => true,
        'environment' => 'staging',
        'timestamp' => date('c')
    ])
    ->// Don't encode query parameters (default)
    ->toCurl();
```

### Building Requests Conditionally

```php
// Start with a base request and add features conditionally
$request = HttpCommand::get('https://api.example.com/users')
    ->header('Accept', 'application/json');

// Add authentication if available
if ($authToken) {
    $request->auth()->bearerToken($authToken);
}

// Add pagination parameters
if ($page) {
    $request->query('page', (string)$page);
}

if ($limit) {
    $request->query('limit', (string)$limit);
}

// Add filtering
if ($status) {
    $request->query('status', $status);
}

// Add custom headers for specific environments
if ($environment === 'development') {
    $request->header('X-Debug', 'true')
           ->header('X-Environment', 'dev');
}

// Generate the final command
$command = $request->toCurl();
```

These examples demonstrate the flexibility of the fluent builder pattern. You can:

- **Chain methods in any order** - headers, queries, authentication, and body methods can be called in any sequence
- **Mix different types of methods** - combine headers, queries, authentication, and body configuration as needed
- **Build requests incrementally** - start with a base request and add features conditionally
- **Generate multiple formats** - use the same builder to generate both cURL and wget commands
- **Reuse builders** - create a base configuration and generate multiple commands from it

The key principle is that each method returns the builder instance, allowing for continuous chaining until you call a generator method (`toCurl()`, `toWget()`, or `to()`).
