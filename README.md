# HTTP Command Generator

<p>
    <a href="https://github.com/chr15k/http-command-generator/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/chr15k/http-command-generator/main.yml"></a>
    <a href="https://packagist.org/packages/chr15k/http-command-generator"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/chr15k/http-command-generator"></a>
    <a href="https://packagist.org/packages/chr15k/http-command-generator"><img alt="Latest Version" src="https://img.shields.io/packagist/v/chr15k/http-command-generator"></a>
    <a href="https://packagist.org/packages/chr15k/http-command-generator"><img alt="License" src="https://img.shields.io/packagist/l/chr15k/http-command-generator"></a>
</p>

### 📦 Overview

http-command-generator is a fluent PHP library for building curl commands and other HTTP CLI requests programmatically.

Designed for developers who want to generate real, runnable HTTP requests—not just send them—this package is perfect for:

- 🧪 API testing and debugging
- 🖥️ CLI tool integrations
- 📋 Generating copy-pasteable commands for docs or logs

With a clear fluent API, you can dynamically build requests with full control over methods, headers, query parameters, payloads, and authentication.

### ✨ Features

- ✅ Fluent builder for curl and wget commands
- ✅ Supports headers, query params, JSON bodies, and files
- ✅ Built-in auth integration 
- ✅ Zero external dependencies

> [!NOTE]
> CLI tools currently supported are [cURL](https://curl.se/) and [wget](https://www.gnu.org/software/wget/)

## Installation

Requires [PHP 8.2+](https://www.php.net/releases/)

```bash
composer require chr15k/http-command-generator
```

## Basic Usage

### Simple GET Request

```php
use Chr15k\HttpCommand\HttpCommand;

// Build a simple GET request with cURL
$curl = HttpCommand::get('https://api.example.com/users')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/users'

// Generate the same request with wget
$wget = HttpCommand::get('https://api.example.com/users')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method GET --timeout=0 'https://api.example.com/users'
```

### POST Request with JSON Body

```php
use Chr15k\HttpCommand\HttpCommand;

// Build a POST request with JSON data using cURL
$curl = HttpCommand::post('https://api.example.com/users')
    ->json([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/users' \
//  --header "Content-Type: application/json" \
//  --data '{"name":"John Doe","email":"john@example.com"}'

// Generate the same POST request using wget
$wget = HttpCommand::post('https://api.example.com/users')
    ->json([
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
use Chr15k\HttpCommand\HttpCommand;

// Using a Bearer Token with cURL
$curl = HttpCommand::get('https://api.example.com/protected-resource')
    ->auth()->bearer('your-access-token')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header "Authorization: Bearer your-access-token"

// Using a Bearer Token with wget
$wget = HttpCommand::get('https://api.example.com/protected-resource')
    ->auth()->bearer('your-access-token')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method GET --timeout=0 \
//  --header "Authorization: Bearer your-access-token" \
//  'https://api.example.com/protected-resource'
```

#### Basic Authentication

```php
use Chr15k\HttpCommand\HttpCommand;

// Using Basic Auth with cURL
$curl = HttpCommand::get('https://api.example.com/protected-resource')
    ->auth()->basic('username', 'password')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header "Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ="

// Using Basic Auth with wget
$wget = HttpCommand::get('https://api.example.com/protected-resource')
    ->auth()->basic('username', 'password')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method GET --timeout=0 \
//  --header "Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ=" \
//  'https://api.example.com/protected-resource'
```

#### API Key Authentication

```php
use Chr15k\HttpCommand\HttpCommand;

// Using an API Key in header with cURL
$curl = HttpCommand::get('https://api.example.com/data')
    ->auth()->apiKey('X-API-Key', 'your-api-key')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/data' \
//  --header "X-API-Key: your-api-key"

// Using an API Key in query string with both generators
$curl = HttpCommand::get('https://api.example.com/data')
    ->auth()->apiKey('api_key', 'your-api-key', true)
    ->toCurl();

$wget = HttpCommand::get('https://api.example.com/data')
    ->auth()->apiKey('api_key', 'your-api-key', true)
    ->toWget();

// Output: curl --location --request GET 'https://api.example.com/data?api_key=your-api-key'
// Output: wget --no-check-certificate --quiet --method GET --timeout=0 'https://api.example.com/data?api_key=your-api-key'
```

#### Digest Authentication

```php
use Chr15k\HttpCommand\HttpCommand;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;

// Basic Digest Auth
$curl = HttpCommand::get('https://api.example.com/protected-resource')
    ->auth()->digest('username', 'password')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/protected-resource' \
//  --header 'Authorization: Digest username="username", realm="", nonce="", uri="", algorithm="MD5", response="..."'

// Advanced Digest Auth with all parameters
$curl = HttpCommand::get('https://api.example.com/protected-resource')
    ->auth()->digest(
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
$wget = HttpCommand::get('https://api.example.com/protected-resource')
    ->auth()->digest(
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

### HTTP Methods

The library supports all standard HTTP methods:

```php
use Chr15k\HttpCommand\HttpCommand;

// Standard HTTP methods
$get = HttpCommand::get('https://api.example.com/users');           // GET
$post = HttpCommand::post('https://api.example.com/users');         // POST
$put = HttpCommand::put('https://api.example.com/users/1');         // PUT
$patch = HttpCommand::patch('https://api.example.com/users/1');     // PATCH
$delete = HttpCommand::delete('https://api.example.com/users/1');   // DELETE
$head = HttpCommand::head('https://api.example.com/users/1');       // HEAD
$options = HttpCommand::options('https://api.example.com/users');   // OPTIONS
```

### Query Parameters and Headers

Both `query()` and `header()` methods are chainable, allowing you to build complex requests:

```php
use Chr15k\HttpCommand\HttpCommand;

// Chaining multiple query parameters and headers
$curl = HttpCommand::get('https://api.example.com/search')
    ->header('Accept', 'application/json')
    ->header('User-Agent', 'MyApp/1.0')
    ->query('q', 'test')
    ->query('page', '1')
    ->query('limit', '10')
    ->auth()->bearer('your-token')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/search?q=test&page=1&limit=10' \
//  --header "Accept: application/json" \
//  --header "User-Agent: MyApp/1.0" \
//  --header "Authorization: Bearer your-token"
```

### Command Generator Methods

You can use either specific generator methods or the generic `to()` method:

```php
// Using specific methods
$curl = HttpCommand::get('https://api.example.com')->toCurl();
$wget = HttpCommand::get('https://api.example.com')->toWget();

// Using the generic to() method
$curl = HttpCommand::get('https://api.example.com')->to('curl');
$wget = HttpCommand::get('https://api.example.com')->to('wget');
```

## Advanced Usage

### Working with Request Bodies

#### Form URL-encoded Data

```php
use Chr15k\HttpCommand\HttpCommand;

// Form URL-encoded data with cURL
$curl = HttpCommand::post('https://api.example.com/form')
    ->form([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/form' \
//  --header "Content-Type: application/x-www-form-urlencoded" \
//  --data-urlencode "name=John%20Doe" \
//  --data-urlencode "email=john%40example.com"

// Form URL-encoded data with wget
$wget = HttpCommand::post('https://api.example.com/form')
    ->form([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->toWget();

// Output: wget --no-check-certificate --quiet --method POST --timeout=0 \
//  --header 'Content-Type: application/x-www-form-urlencoded' \
//  --body-data 'name=John%20Doe&email=john%40example.com' \
//  'https://api.example.com/form'
```

#### Multipart Form Data

```php
use Chr15k\HttpCommand\HttpCommand;

// Multipart form data (useful for file uploads) with cURL
$curl = HttpCommand::post('https://api.example.com/upload')
    ->multipart([
        'file' => '@/path/to/file.jpg',
        'name' => 'Profile Photo',
    ])
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/upload' \
//  --form "file=@/path/to/file.jpg" \
//  --form "name=Profile Photo"

// Note: wget doesn't support multipart form data natively like cURL does
// Instead, it converts to form data:
$wget = HttpCommand::post('https://api.example.com/upload')
    ->multipart([
        'name' => 'Profile Photo',
    ])
    ->toWget();

// Output: wget --no-check-certificate --quiet --method POST --timeout=0 \
//  --body-data 'name=Profile+Photo' \
//  'https://api.example.com/upload'
```

#### Binary Data

```php
use Chr15k\HttpCommand\HttpCommand;

// Send binary file content with cURL
$curl = HttpCommand::post('https://api.example.com/upload-binary')
    ->file('/path/to/file.bin')
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/upload-binary' \
//  --data-binary '@/path/to/file.bin'

// Send binary file content with wget
$wget = HttpCommand::post('https://api.example.com/upload-binary')
    ->file('/path/to/file.bin')
    ->toWget();

// Output: wget --no-check-certificate --quiet --method POST --timeout=0 \
//  --body-file='/path/to/file.bin' \
//  'https://api.example.com/upload-binary'
```

### Custom Request Headers

The `header()` method is chainable, allowing you to add multiple headers easily:

```php
use Chr15k\HttpCommand\HttpCommand;

// Chain multiple headers together
$curl = HttpCommand::get('https://api.example.com/data')
    ->header('Accept', 'application/json')
    ->header('Cache-Control', 'no-cache')
    ->header('User-Agent', 'MyApp/1.0')
    ->header('X-Custom-Header', 'custom-value')
    ->toCurl();

// Output: curl --location --request GET 'https://api.example.com/data' \
//  --header "Accept: application/json" \
//  --header "Cache-Control: no-cache" \
//  --header "User-Agent: MyApp/1.0" \
//  --header "X-Custom-Header: custom-value"

// Headers can be mixed with other methods in any order
$request = HttpCommand::post('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->auth()->bearer('token123')
    ->header('Content-Type', 'application/json')
    ->query('notify', 'true')
    ->header('X-Request-ID', 'req-456')
    ->json(['name' => 'John'])
    ->toCurl();
```

## Command Generators

HTTP Command Generator supports two command-line tools for making HTTP requests:

### cURL Generator

cURL is the most widely used command-line tool for HTTP requests and is the default generator:

```php
$curl = HttpCommand::get('https://api.example.com/users')->toCurl();
```

### wget Generator

wget is an alternative command-line tool that's commonly available on Unix-like systems:

```php
$wget = HttpCommand::get('https://api.example.com/users')->toWget();
```

Both generators support the same API and features, but have different output formats and capabilities. wget doesn't support all features that cURL does (like native multipart form data), but provides a good alternative for basic HTTP operations.

## Line Break Formatting

For better readability, especially when working with complex commands, you can enable line breaks in the generated commands using the `includeLineBreaks()` method:

```php
use Chr15k\HttpCommand\HttpCommand;

// Default output (single line)
$command = HttpCommand::post('https://api.example.com/users')
    ->header('Authorization', 'Bearer token')
    ->header('Content-Type', 'application/json')
    ->json(['name' => 'John Doe', 'email' => 'john@example.com'])
    ->toCurl();

// Output: curl --location --request POST 'https://api.example.com/users' --header "Authorization: Bearer token" --header "Content-Type: application/json" --data '{"name":"John Doe","email":"john@example.com"}'

// With line breaks for better readability
$command = HttpCommand::post('https://api.example.com/users')
    ->header('Authorization', 'Bearer token')
    ->header('Content-Type', 'application/json')
    ->json(['name' => 'John Doe', 'email' => 'john@example.com'])
    ->includeLineBreaks()
    ->toCurl();

// Output: curl --location \
//   --request POST \
//   'https://api.example.com/users' \
//   --header "Authorization: Bearer token" \
//   --header "Content-Type: application/json" \
//   --data '{"name":"John Doe","email":"john@example.com"}'
```

The `includeLineBreaks()` method works with both cURL and wget generators and can be chained with any other builder methods.

## Documentation

- [User Guide](https://github.com/chr15k/http-command-generator/blob/main/docs/USER_GUIDE.md) - Comprehensive guide with examples
- [API Cheat Sheet](https://github.com/chr15k/http-command-generator/blob/main/docs/API_CHEATSHEET.md) - Quick reference of all available methods

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
