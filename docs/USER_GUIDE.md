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
    - [Multi-Value Array Keys](#multi-value-array-keys)
  - [Multipart Form Data](#multipart-form-data)
    - [Multi-Value Array Keys in Multipart Data](#multi-value-array-keys-in-multipart-data)
  - [Binary Data](#binary-data)
- [Command Generators](#command-generators)
  - [Generic to() Method](#generic-to-method)
  - [cURL Generator](#curl-generator)
  - [wget Generator](#wget-generator)
- [Line Break Formatting](#line-break-formatting)
- [Full Examples](#full-examples)
- [Query Parameters](#query-parameters)
- [Advanced Method Chaining Examples](#advanced-method-chaining-examples)
  - [Complex API Request with Multiple Features](#complex-api-request-with-multiple-features)
  - [Search API with Pagination and Filtering](#search-api-with-pagination-and-filtering)
  - [Form Submission with Multi-Value Arrays](#form-submission-with-multi-value-arrays)
  - [File Upload with Multiple Files and Metadata](#file-upload-with-multiple-files-and-metadata)

## Introduction

HTTP Command Generator is a PHP library that allows you to generate CLI commands for HTTP requests using a fluent builder pattern.

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

use Chr15k\HttpCommand\Enums\HttpMethod;

$builder = HttpCommand::url('https://api.example.com/users/1');
$builder->method(HttpMethod::PATCH);
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
    ->auth()->bearer('your-token')
    ->header('X-Request-ID', 'req-123')
    ->query('page', '1')
    ->header('Cache-Control', 'no-cache')
    ->toCurl();
```

## Authentication

### Bearer Token

Add a Bearer token to your request:

```php
$builder->auth()->bearer('your-access-token');
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

#### Multi-Value Array Keys

Both form URL-encoded and multipart form data support multi-value array keys, allowing you to send multiple values for the same field name:

```php
// Single values (standard approach)
$builder->form([
    'name' => 'John Doe',
    'category' => 'technology'
]);

// Multi-value arrays - useful for checkboxes, multiple selections, etc.
$builder->form([
    'categories' => ['technology', 'programming', 'web-development'],
    'skills' => ['PHP', 'JavaScript', 'Python'],
    'tags' => ['api', 'rest', 'http']
]);

// Mixed single values and multi-value arrays
$builder->form([
    'name' => 'John Doe',
    'categories' => ['technology', 'programming'],  // Multiple values
    'primary_skill' => 'PHP',                       // Single value
    'languages' => ['English', 'Spanish']           // Multiple values
]);
```

**Generated Output for Multi-Value Keys:**

For curl, each value gets its own `--data-urlencode` parameter:
```bash
curl --location --request POST 'https://api.example.com/form' \
  --header 'Content-Type: application/x-www-form-urlencoded' \
  --data-urlencode 'categories=technology' \
  --data-urlencode 'categories=programming' \
  --data-urlencode 'categories=web-development'
```

For wget, values are combined in the query string format:
```bash
wget --method POST \
  --header 'Content-Type: application/x-www-form-urlencoded' \
  --body-data 'categories=technology&categories=programming&categories=web-development' \
  'https://api.example.com/form'
```

**Important Notes:**
- Only scalar values (strings, numbers) and null values are allowed in multi-value arrays
- Nested arrays or objects within the array values will be filtered out
- Empty or null values are preserved and sent as empty fields

### Multipart Form Data

Add multipart form data to your request (useful for file uploads):

```php
$builder->multipart([
    'profile_picture' => '@/path/to/image.jpg',
    'name' => 'John Doe'
]);
```

#### Multi-Value Array Keys in Multipart Data

Just like form URL-encoded data, multipart form data also supports multi-value arrays:

```php
// File uploads with multiple files
$builder->multipart([
    'documents' => ['@/path/to/doc1.pdf', '@/path/to/doc2.pdf'],
    'images' => ['@/path/to/image1.jpg', '@/path/to/image2.png'],
    'name' => 'John Doe'
]);

// Mixed file uploads and regular fields
$builder->multipart([
    'profile_picture' => '@/path/to/profile.jpg',
    'gallery_images' => ['@/path/to/img1.jpg', '@/path/to/img2.jpg'],
    'tags' => ['personal', 'portfolio'],
    'description' => 'My portfolio images'
]);
```

**Generated Output for Multi-Value Multipart:**

For curl, each value gets its own `--form` parameter:
```bash
curl --location --request POST 'https://api.example.com/upload' \
  --form 'documents=@/path/to/doc1.pdf' \
  --form 'documents=@/path/to/doc2.pdf' \
  --form 'tags=personal' \
  --form 'tags=portfolio'
```

For wget, values are formatted as standard form data:
```bash
wget --method POST \
  --body-data 'documents=@/path/to/doc1.pdf&documents=@/path/to/doc2.pdf&tags=personal&tags=portfolio' \
  'https://api.example.com/upload'
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

## Line Break Formatting

For better readability, especially when working with complex commands or when copying commands for use in scripts, you can enable line break formatting using the `includeLineBreaks()` method. This will format the command with line breaks and backslashes for continuation.

### Basic Usage

```php
use Chr15k\HttpCommand\HttpCommand;

// Default output (single line)
$command = HttpCommand::get('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->toCurl();

echo $command;
// Output: curl --location --request GET 'https://api.example.com/users' --header "Accept: application/json"

// With line breaks for better readability
$command = HttpCommand::get('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->includeLineBreaks()
    ->toCurl();

echo $command;
// Output: curl --location \
//   --request GET \
//   'https://api.example.com/users' \
//   --header "Accept: application/json"
```

### Complex Example with Multiple Features

```php
use Chr15k\HttpCommand\HttpCommand;

// Complex command with authentication, headers, query parameters, and body
$command = HttpCommand::post('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->header('Content-Type', 'application/json')
    ->header('User-Agent', 'MyApp/1.0')
    ->query('notify', 'true')
    ->query('source', 'api')
    ->auth()->bearer('your-access-token')
    ->json([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'user'
    ])
    ->includeLineBreaks()
    ->toCurl();

echo $command;
// Output: curl --location \
//   --request POST \
//   'https://api.example.com/users?notify=true&source=api' \
//   --header "Accept: application/json" \
//   --header "Content-Type: application/json" \
//   --header "User-Agent: MyApp/1.0" \
//   --header "Authorization: Bearer your-access-token" \
//   --data '{"name":"John Doe","email":"john@example.com","role":"user"}'
```

### Works with Both Generators

The `includeLineBreaks()` method works with both cURL and wget generators:

```php
use Chr15k\HttpCommand\HttpCommand;

$request = HttpCommand::get('https://api.example.com/data')
    ->header('Authorization', 'Bearer token')
    ->header('Accept', 'application/json')
    ->query('page', '1')
    ->includeLineBreaks();

// cURL with line breaks
$curl = $request->toCurl();
echo $curl;
// Output: curl --location \
//   --request GET \
//   'https://api.example.com/data?page=1' \
//   --header "Authorization: Bearer token" \
//   --header "Accept: application/json"

// wget with line breaks
$wget = $request->toWget();
echo $wget;
// Output: wget --no-check-certificate --quiet \
//   --method GET \
//   --timeout=0 \
//   --header "Authorization: Bearer token" \
//   --header "Accept: application/json" \
//   'https://api.example.com/data?page=1'
```

### Method Chaining

The `includeLineBreaks()` method is chainable and can be placed anywhere in your method chain:

```php
use Chr15k\HttpCommand\HttpCommand;

// includeLineBreaks() can be called at any point in the chain
$command = HttpCommand::post('https://api.example.com/users')
    ->includeLineBreaks()  // Can be called early
    ->header('Accept', 'application/json')
    ->auth()->bearer('token')
    ->json(['name' => 'John'])
    ->toCurl();

// Or at the end before generation
$command = HttpCommand::post('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->auth()->bearer('token')
    ->json(['name' => 'John'])
    ->includeLineBreaks()  // Can be called late
    ->toCurl();
```

This feature is particularly useful when you need to:
- Copy commands to shell scripts
- Share commands with team members
- Debug complex HTTP requests
- Generate documentation examples
- Work with commands that have many parameters

## Full Examples

### GET Request with Query Parameters

```php
use Chr15k\HttpCommand\HttpCommand;

// Using cURL
$curl = HttpCommand::get('https://api.example.com/search?q=test&page=1')
    ->header('Accept', 'application/json')
    ->auth()->bearer('your-access-token')
    ->toCurl();

// Using wget
$wget = HttpCommand::get('https://api.example.com/search?q=test&page=1')
    ->header('Accept', 'application/json')
    ->auth()->bearer('your-access-token')
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
    ->auth()->bearer('your-access-token')
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
    ->auth()->bearer('your-access-token')
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
    ->auth()->bearer('token123')
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
    ->auth()->bearer('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...')
    ->json([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'role' => 'user'
    ])
    ->toCurl();

// The same request can be built in a different order
$curl = HttpCommand::post()
    ->url('https://api.example.com/users')
    ->auth()->bearer('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...')
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

### Form Submission with Multi-Value Arrays

```php
// Real-world example: Job application form with multiple skills and categories
$jobApplicationCommand = HttpCommand::post('https://api.jobboard.com/applications')
    ->header('Accept', 'application/json')
    ->header('User-Agent', 'JobApp/1.0')
    ->auth()->bearer('your-access-token')
    ->form([
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'position' => 'Senior Developer',
        // Multi-value arrays for skills and interests
        'skills' => ['PHP', 'JavaScript', 'Python', 'Docker', 'AWS'],
        'job_categories' => ['backend', 'full-stack', 'devops'],
        'work_types' => ['remote', 'hybrid'],
        'experience_level' => 'senior',
        // Optional fields with arrays
        'certifications' => ['AWS Certified', 'Docker Certified'],
        'languages' => ['English', 'Spanish', 'French']
    ]);

$curl = $jobApplicationCommand->toCurl();
// This generates multiple --data-urlencode parameters:
// --data-urlencode 'skills=PHP' --data-urlencode 'skills=JavaScript'
// --data-urlencode 'skills=Python' --data-urlencode 'skills=Docker'
// --data-urlencode 'skills=AWS' --data-urlencode 'job_categories=backend' etc.

$wget = $jobApplicationCommand->toWget();
// This generates: --body-data 'name=Jane+Smith&email=jane.smith@example.com&skills=PHP&skills=JavaScript&skills=Python...'
```

### File Upload with Multiple Files and Metadata

```php
// Multi-file upload with additional form data
$uploadCommand = HttpCommand::post('https://api.fileservice.com/upload')
    ->header('Accept', 'application/json')
    ->auth()->apiKey('X-API-Key', 'your-upload-key')
    ->multipart([
        'project_name' => 'Website Redesign',
        'description' => 'Design assets for the new website',
        // Multiple file uploads
        'design_files' => [
            '@/path/to/homepage.psd',
            '@/path/to/about.psd',
            '@/path/to/contact.psd'
        ],
        'preview_images' => [
            '@/path/to/preview1.jpg',
            '@/path/to/preview2.jpg'
        ],
        // Multiple categories and tags
        'categories' => ['design', 'web', 'ui'],
        'tags' => ['homepage', 'responsive', 'modern', 'clean'],
        'client_id' => '12345',
        'visibility' => 'private'
    ]);

$curl = $uploadCommand->toCurl();
// Generates: --form 'design_files=@/path/to/homepage.psd' --form 'design_files=@/path/to/about.psd'
//           --form 'categories=design' --form 'categories=web' --form 'categories=ui' etc.
```
