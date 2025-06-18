# API Cheat Sheet

Quick reference guide for the HTTP CLI Generator library.

## Builder Methods

### Core Methods

| Method | Description | Example |
|--------|-------------|---------|
| `get()` | Create a new builder instance with GET method | `CommandBuilder::get()` |
| `post()` | Create a new builder instance with POST method | `CommandBuilder::post()` |
| `put()` | Create a new builder instance with PUT method | `CommandBuilder::put()` |
| `patch()` | Create a new builder instance with PATCH method | `CommandBuilder::patch()` |
| `delete()` | Create a new builder instance with DELETE method | `CommandBuilder::delete()` |
| `head()` | Create a new builder instance with HEAD method | `CommandBuilder::head()` |
| `options()` | Create a new builder instance with OPTIONS method | `CommandBuilder::options()` |
| `url(string $url)` | Set the URL | `->url('https://api.example.com')` |
| `method(string $method)` | Change HTTP method | `->method('PATCH')` |
| `toCurl()` | Generate cURL command | `->toCurl()` |
| `to(string $generator)` | Use specific generator | `->to('curl')` or `->to('wget')` |
| `toCurl()` | Generate cURL command | `->toCurl()` |
| `toWget()` | Generate wget command | `->toWget()` |
| `availableGenerators()` | List available generators | `$builder->availableGenerators()` |
| `registerGenerator(string $name, Generator $generator)` | Register custom generator | `->registerGenerator('httpie', new HttpieGenerator())` |

### Headers

| Method | Description | Example |
|--------|-------------|---------|
| `header(string $name, string $value)` | Add single header | `->header('Accept', 'application/json')` |
| `headers(array $headers)` | Add multiple headers | `->headers(['Accept' => 'application/json', 'User-Agent' => 'MyApp/1.0'])` |

### Authentication

| Method | Description | Example |
|--------|-------------|---------|
| `auth(AuthDataTransfer $auth)` | Set auth with DTO | `->auth(new BasicAuthData('user', 'pass'))` |
| `withApiKey(string $key, string $value, bool $inQuery = false)` | Add API key auth | `->withApiKey('X-API-Key', 'key123')` |
| `withBasicAuth(string $username, string $password)` | Add Basic auth | `->withBasicAuth('user', 'pass')` |
| `withDigestAuth(string $username, string $password, DigestAlgorithm $algorithm = DigestAlgorithm::MD5, string $realm = '', string $method = '', string $uri = '', string $nonce = '', string $nc = '', string $cnonce = '', string $qop = '')` | Add Digest auth | `->withDigestAuth('user', 'pass', DigestAlgorithm::MD5, 'example.com', 'GET', '/api')` |
| `withBearerToken(string $token)` | Add Bearer token | `->withBearerToken('token123')` |
| `withJWTAuth(string $key, array $payload = [], array $headers = [], Algorithm $algorithm = Algorithm::HS256, bool $secretBase64Encoded = false, string $headerPrefix = 'Bearer', bool $inQuery = false, string $queryKey = 'token')` | Add JWT auth | `->withJWTAuth('secret', ['user_id' => 123])` |

### Request Body

| Method | Description | Example |
|--------|-------------|---------|
| `body(BodyDataTransfer $body)` | Set body with DTO | `->body(new JsonBodyData(['name' => 'John']))` |
| `withJsonBody(array $data)` | Add JSON body | `->withJsonBody(['name' => 'John', 'age' => 30])` |
| `withRawJsonBody(string $json)` | Add raw JSON body | `->withRawJsonBody('{"name":"John"}')` |
| `withFormBody(array $data)` | Add form-urlencoded body | `->withFormBody(['name' => 'John', 'age' => 30])` |
| `withMultipartBody(array $data)` | Add multipart form data | `->withMultipartBody(['file' => '@path/to/file'])` |
| `withBinaryBody(string $filePath)` | Add binary file content | `->withBinaryBody('/path/to/file.bin')` |


