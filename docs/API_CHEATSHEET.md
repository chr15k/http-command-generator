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
| `includeLineBreaks()` | Format command with line breaks for readability | `->includeLineBreaks()` |
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
| `encodeQuery(bool $encode = true)` | Enable/disable query parameter encoding (default: disabled) | `->encodeQuery()` |

### Authentication

| Method | Description | Example |
|--------|-------------|---------|
| `auth()->basic(string $username, string $password)` | Add Basic auth | `->auth()->basic('user', 'pass')` |
| `auth()->bearer(string $token)` | Add Bearer token | `->auth()->bearer('token123')` |
| `auth()->apiKey(string $key, string $value, bool $inQuery = false)` | Add API key auth | `->auth()->apiKey('X-API-Key', 'key123')` |
| `auth()->jwt(string $key = '', array $payload = [], array $headers = [], Algorithm $algorithm = Algorithm::HS256, bool $secretBase64Encoded = false, string $headerPrefix = 'Bearer', bool $inQuery = false, string $queryKey = 'token')` | Add JWT auth | `->auth()->jwt('secret', ['user_id' => 123])` |
| `auth()->digest(...)` | Add Digest auth | `->auth()->digest('user', 'pass', DigestAlgorithm::MD5)` |

### Request Body

| Method | Description | Example |
|--------|-------------|---------|
| `json(array\|string $data, bool $preserveAsRaw = false)` | Add JSON body | `->json(['name' => 'John', 'age' => 30])` |
| `form(array $data)` | Add form-urlencoded body (supports multi-value arrays) | `->form(['tags' => ['php', 'api'], 'name' => 'John'])` |
| `multipart(array $data)` | Add multipart form data (supports multi-value arrays) | `->multipart(['files' => ['@file1.jpg', '@file2.jpg']])` |
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
    ->auth()->bearer('your-token')
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
// By default, query parameters are NOT URL-encoded
HttpCommand::get('https://api.example.com')
    ->query('filter', 'status:active OR type:premium')
    ->toCurl();
// Results in: ?filter=status:active OR type:premium

// Enable encoding to URL-encode query parameters
HttpCommand::get('https://api.example.com')
    ->query('filter', 'status:active OR type:premium')
    ->encodeQuery()
    ->toCurl();
// Results in: ?filter=status%3Aactive%20OR%20type%3Apremium
```

## Line Break Formatting

Format commands with line breaks for better readability:

```php
// Default output (single line)
HttpCommand::post('https://api.example.com/users')
    ->header('Authorization', 'Bearer token')
    ->json(['name' => 'John'])
    ->toCurl();
// Output: curl --location --request POST 'https://api.example.com/users' --header "Authorization: Bearer token" --data '{"name":"John"}'

// With line breaks for better readability
HttpCommand::post('https://api.example.com/users')
    ->header('Authorization', 'Bearer token')
    ->json(['name' => 'John'])
    ->includeLineBreaks()
    ->toCurl();
// Output: curl --location \
//   --request POST \
//   'https://api.example.com/users' \
//   --header "Authorization: Bearer token" \
//   --data '{"name":"John"}'

// Works with wget too
HttpCommand::get('https://api.example.com/data')
    ->header('Accept', 'application/json')
    ->includeLineBreaks()
    ->toWget();
// Output: wget --no-check-certificate \
//   --quiet \
//   --method GET \
//   --timeout=0 \
//   --header 'Accept: application/json' \
//   'https://api.example.com/data'
```

### Multi-Value Array Examples
```php
// Form with multiple values for the same field
HttpCommand::post('https://api.example.com/search')
    ->form([
        'categories' => ['technology', 'programming'],
        'skills' => ['PHP', 'JavaScript', 'Python'],
        'name' => 'John Doe'
    ])
    ->toCurl();
// Generates: --data-urlencode 'categories=technology' --data-urlencode 'categories=programming'
//           --data-urlencode 'skills=PHP' --data-urlencode 'skills=JavaScript' etc.

// Multipart with multiple files
HttpCommand::post('https://api.example.com/upload')
    ->multipart([
        'documents' => ['@/path/to/doc1.pdf', '@/path/to/doc2.pdf'],
        'tags' => ['important', 'work'],
        'description' => 'Project files'
    ])
    ->toCurl();
// Generates: --form 'documents=@/path/to/doc1.pdf' --form 'documents=@/path/to/doc2.pdf'
//           --form 'tags=important' --form 'tags=work' etc.
```


