# API Cheat Sheet

Quick reference guide for the HTTP Command Generator library.

## Builder Methods

### Core Methods

| Method | Description | Example |
|--------|-------------|---------|
| `HttpCommand::get(string $url = '')` | Create a GET request | `HttpCommand::get('https://api.example.com')` |
| `HttpCommand::post(string $url = '')` | Create a POST request | `HttpCommand::post('https://api.example.com')` |
| `HttpCommand::put(string $url = '')` | Create a PUT request | `HttpCommand::put('https://api.example.com')` |
| `HttpCommand::patch(string $url = '')` | Create a PATCH request | `HttpCommand::patch('https://api.example.com')` |
| `HttpCommand::delete(string $url = '')` | Create a DELETE request | `HttpCommand::delete('https://api.example.com')` |
| `HttpCommand::head(string $url = '')` | Create a HEAD request | `HttpCommand::head('https://api.example.com')` |
| `HttpCommand::options(string $url = '')` | Create an OPTIONS request | `HttpCommand::options('https://api.example.com')` |
| `url(string $url)` | Set the URL | `->url('https://api.example.com')` |
| `method(string $method)` | Change HTTP method | `->method('PATCH')` |
| `to(string $generator)` | Generate command using specified generator | `->to('curl')` or `->to('wget')` |
| `toCurl()` | Generate cURL command | `->toCurl()` |
| `toWget()` | Generate wget command | `->toWget()` |

### Headers

| Method | Description | Example |
|--------|-------------|---------|
| `header(string $name, string $value)` | Add single header (chainable) | `->header('Accept', 'application/json')->header('Cache-Control', 'no-cache')` |

### Query Parameters

| Method | Description | Example |
|--------|-------------|---------|
| `query(string $name, string $value)` | Add single query parameter (chainable) | `->query('page', '1')->query('limit', '10')` |
| `encodeQuery(bool $encode = true)` | Enable/disable query parameter encoding | `->encodeQuery(false)` |

### Authentication

| Method | Description | Example |
|--------|-------------|---------|
| `auth()->basic(string $username, string $password)` | Add Basic auth | `->auth()->basic('user', 'pass')` |
| `auth()->bearerToken(string $token)` | Add Bearer token | `->auth()->bearerToken('token123')` |
| `auth()->apiKey(string $key, string $value, bool $inQuery = false)` | Add API key auth | `->auth()->apiKey('X-API-Key', 'key123')` |
| `auth()->jwt(string $key = '', array $payload = [], array $headers = [], Algorithm $algorithm = Algorithm::HS256, bool $secretBase64Encoded = false, string $headerPrefix = 'Bearer', bool $inQuery = false, string $queryKey = 'token')` | Add JWT auth | `->auth()->jwt('secret', ['user_id' => 123])` |
| `auth()->digest(...)` | Add Digest auth | `->auth()->digest('user', 'pass', DigestAlgorithm::MD5)` |

### Request Body

| Method | Description | Example |
|--------|-------------|---------|
| `json(array\|string $data, bool $preserveAsRaw = false)` | Add JSON body | `->json(['name' => 'John', 'age' => 30])` |
| `form(array $data)` | Add form-urlencoded body | `->form(['name' => 'John', 'age' => 30])` |
| `multipart(array $data)` | Add multipart form data | `->multipart(['file' => '@path/to/file'])` |
| `file(string $filePath)` | Add binary file content | `->file('/path/to/file.bin')` |
| `body(BodyDataTransfer $body)` | Add custom body data transfer object | `->body(new CustomBodyData())` |

## Method Chaining Examples

### Headers and Query Parameters
```php
// Chain multiple headers
HttpCommand::get('https://api.example.com')
    ->header('Accept', 'application/json')
    ->header('User-Agent', 'MyApp/1.0')
    ->header('Cache-Control', 'no-cache');

// Chain multiple query parameters
HttpCommand::get('https://api.example.com/search')
    ->query('q', 'search term')
    ->query('page', '1')
    ->query('limit', '10')
    ->query('sort', 'created_at');

// Chain headers and queries together
HttpCommand::get('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->header('Authorization', 'Bearer token123')
    ->query('status', 'active')
    ->query('page', '2');
```

### Complete Request Building
```php
// Build a complete request with all features
HttpCommand::post('https://api.example.com/users')
    ->header('Accept', 'application/json')
    ->header('Content-Type', 'application/json')
    ->query('notify', 'true')
    ->auth()->bearerToken('your-token')
    ->json(['name' => 'John', 'email' => 'john@example.com'])
    ->toCurl();

// Using the generic to() method
HttpCommand::get('https://api.example.com/data')
    ->header('Accept', 'application/json')
    ->query('format', 'json')
    ->to('curl');  // equivalent to ->toCurl()

HttpCommand::get('https://api.example.com/data')
    ->header('Accept', 'application/json')
    ->query('format', 'json')
    ->to('wget');  // equivalent to ->toWget()
```

## Additional Options

### Query Parameter Encoding
```php
// By default, query parameters are URL-encoded
HttpCommand::get('https://api.example.com')
    ->query('filter', 'status:active OR type:premium')
    ->toCurl();
// Results in: ?filter=status%3Aactive+OR+type%3Apremium

// Disable encoding to use pre-encoded values
HttpCommand::get('https://api.example.com')
    ->query('filter', 'status%3Aactive+OR+type%3Apremium')
    ->encodeQuery(false)
    ->toCurl();
// Results in: ?filter=status%3Aactive+OR+type%3Apremium
```

### Custom Body Data Transfer Objects
```php
// For advanced use cases, implement BodyDataTransfer interface
use Chr15k\HttpCommand\Contracts\BodyDataTransfer;

class CustomBodyData implements BodyDataTransfer {
    // Implementation details...
}

HttpCommand::post('https://api.example.com')
    ->body(new CustomBodyData())
    ->toCurl();
```


