<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Builder\Concerns;

use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\HttpCliGenerator\Contracts\AuthDataTransfer;
use Chr15k\HttpCliGenerator\Contracts\BodyDataTransfer;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\ApiKeyData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BasicAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BearerTokenData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\DigestAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\JWTData;
use Chr15k\HttpCliGenerator\DataTransfer\Body\BinaryData;
use Chr15k\HttpCliGenerator\DataTransfer\Body\FormUrlEncodedData;
use Chr15k\HttpCliGenerator\DataTransfer\Body\JsonBodyData;
use Chr15k\HttpCliGenerator\DataTransfer\Body\MultipartFormData;

trait BuildsHttpRequests
{
    private string $url = '';

    private string $method = 'GET';

    /** @var array<string, string> */
    private array $headers = [];

    private ?AuthDataTransfer $auth = null;

    private ?BodyDataTransfer $body = null;

    public static function create(): self
    {
        return new self;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function get(): self
    {
        $this->method = 'GET';

        return $this;
    }

    public function post(): self
    {
        $this->method = 'POST';

        return $this;
    }

    public function put(): self
    {
        $this->method = 'PUT';

        return $this;
    }

    public function delete(): self
    {
        $this->method = 'DELETE';

        return $this;
    }

    /**
     * @param  array<string, string>  $headers
     */
    public function headers(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function auth(AuthDataTransfer $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    public function body(BodyDataTransfer $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param  array<string, string>  $data
     */
    public function withJsonBody(array $data): self
    {
        return $this->body(new JsonBodyData(data: $data));
    }

    public function withRawJsonBody(string $json): self
    {
        return $this->body(new JsonBodyData(data: $json, preserveAsRaw: true));
    }

    /**
     * @param  array<string, string>  $data
     */
    public function withFormBody(array $data): self
    {
        return $this->body(new FormUrlEncodedData(data: $data));
    }

    /**
     * @param  array<string, string>  $data
     */
    public function withMultipartBody(array $data): self
    {
        return $this->body(new MultipartFormData(data: $data));
    }

    public function withBinaryBody(string $filePath): self
    {
        return $this->body(new BinaryData(filePath: $filePath));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    public function withJWTAuth(
        string $key = '',
        array $payload = [],
        array $headers = [],
        Algorithm $algorithm = Algorithm::HS256,
        bool $secretBase64Encoded = false,
        string $headerPrefix = 'Bearer',
        bool $inQuery = false,
        string $queryKey = 'token',
    ): self {
        return $this->auth(new JWTData(
            key: $key,
            payload: $payload,
            headers: $headers,
            algorithm: $algorithm,
            secretBase64Encoded: $secretBase64Encoded,
            headerPrefix: $headerPrefix,
            inQuery: $inQuery,
            queryKey: $queryKey,
        ));
    }

    public function withApiKey(string $key, string $value, bool $inQuery = false): self
    {
        return $this->auth(new ApiKeyData(key: $key, value: $value, inQuery: $inQuery));
    }

    public function withBasicAuth(string $username, string $password): self
    {
        return $this->auth(new BasicAuthData(username: $username, password: $password));
    }

    public function withDigestAuth(string $username, string $password): self
    {
        return $this->auth(new DigestAuthData(username: $username, password: $password));
    }

    public function withBearerToken(string $token): self
    {
        return $this->auth(new BearerTokenData(token: $token));
    }
}
