# API Cheat Sheet

Quick reference guide for the HTTP CLI Generator library.

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
| `toCurl()` | Generate cURL command | `->toCurl()` |
| `toWget()` | Generate wget command | `->toWget()` |

### Headers

| Method | Description | Example |
|--------|-------------|---------|
| `header(string $name, string $value)` | Add single header | `->header('Accept', 'application/json')` |
| `headers(array $headers)` | Add multiple headers | `->headers(['Accept' => 'application/json', 'User-Agent' => 'MyApp/1.0'])` |

### Query Parameters

| Method | Description | Example |
|--------|-------------|---------|
| `query(string $name, string $value)` | Add single query parameter | `->query('page', '1')` |
| `queries(array $queries)` | Add multiple query parameters | `->queries(['page' => '1', 'limit' => '10'])` |

### Authentication

| Method | Description | Example |
|--------|-------------|---------|
| `auth()->basic(string $username, string $password)` | Add Basic auth | `->auth()->basic('user', 'pass')` |
| `auth()->withBearerToken(string $token)` | Add Bearer token | `->auth()->withBearerToken('token123')` |
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


