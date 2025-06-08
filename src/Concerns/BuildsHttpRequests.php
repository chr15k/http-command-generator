<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Concerns;

use Chr15k\HttpCliGenerator\Contracts\AuthDataTransfer;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\ApiKeyData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BasicAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BearerTokenData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\DigestAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\JWTData;
use Chr15k\HttpCliGenerator\DataTransfer\RequestBodyData;
use Chr15k\HttpCliGenerator\Enums\BodyType;

trait BuildsHttpRequests
{
    private string $url = '';

    private string $method = 'GET';

    /**
     * @var array<string, string>
     */
    private array $headers = [];

    private ?AuthDataTransfer $auth = null;

    private ?RequestBodyData $body = null;

    public static function make(): self
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

    /**
     * @param  array<string, string>|string  $data
     */
    public function withJsonBody(array|string $data): self
    {
        $this->body = new RequestBodyData(type: BodyType::RAW_JSON, data: (array) $data);

        $this->header('Content-Type', 'application/json');

        return $this;
    }

    /**
     * @param  array<string, string>  $data
     */
    public function withFormBody(array $data): self
    {
        $this->body = new RequestBodyData(type: BodyType::FORM_URLENCODED, data: $data);

        $this->header('Content-Type', 'application/x-www-form-urlencoded');

        return $this;
    }

    /**
     * @param  array<string, string>  $data
     */
    public function withMultipartBody(array $data): self
    {
        $this->body = new RequestBodyData(type: BodyType::FORM, data: $data);

        $this->header('Content-Type', 'multipart/form-data');

        return $this;
    }

    public function withJWTAuth(
        string $token,
        bool $inQuery = false,
        string $queryKey = 'token',
        string $headerPrefix = ''
    ): self {
        $this->auth = new JWTData(
            token: $token,
            inQuery: $inQuery,
            queryKey: $queryKey,
            headerPrefix: $headerPrefix
        );

        return $this;
    }

    public function withApiKey(
        string $key,
        string $value,
        bool $inQuery = false
    ): self {
        $this->auth = new ApiKeyData(
            key: $key,
            value: $value,
            inQuery: $inQuery
        );

        return $this;
    }

    public function withBasicAuth(
        string $username,
        string $password
    ): self {
        $this->auth = new BasicAuthData(
            username: $username,
            password: $password
        );

        return $this;
    }

    public function withDigestAuth(
        string $username,
        string $password,
    ): self {
        $this->auth = new DigestAuthData(
            username: $username,
            password: $password,
        );

        return $this;
    }

    public function withBearerToken(string $token): self
    {
        $this->auth = new BearerTokenData(token: $token);

        return $this;
    }
}
