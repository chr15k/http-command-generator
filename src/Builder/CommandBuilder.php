<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Builder;

use Chr15k\HttpCommand\Collections\HttpParameterCollection;
use Chr15k\HttpCommand\Contracts\AuthDataTransfer;
use Chr15k\HttpCommand\Contracts\BodyDataTransfer;
use Chr15k\HttpCommand\Contracts\Builder;
use Chr15k\HttpCommand\DataTransfer\Body\BinaryData;
use Chr15k\HttpCommand\DataTransfer\Body\FormUrlEncodedData;
use Chr15k\HttpCommand\DataTransfer\Body\JsonBodyData;
use Chr15k\HttpCommand\DataTransfer\Body\MultipartFormData;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Chr15k\HttpCommand\Generators\CurlGenerator;
use Chr15k\HttpCommand\Generators\WgetGenerator;
use InvalidArgumentException;

/**
 * @internal
 */
final class CommandBuilder implements Builder
{
    private string $url = '';

    private string $method = 'GET';

    /** @var array<string, array<int, string>> */
    private array $headers = [];

    /** @var array<string, array<int, string>> */
    private array $queries = [];

    private ?AuthDataTransfer $auth = null;

    private ?BodyDataTransfer $body = null;

    private bool $encodeQuery = false;

    private bool $includeLineBreaks = false;

    public function to(string $generator): string
    {
        return match ($generator) {
            'curl' => $this->toCurl(),
            'wget' => $this->toWget(),
            default => throw new InvalidArgumentException("Unsupported generator: {$generator}")
        };
    }

    public function toCurl(): string
    {
        return (new CurlGenerator)->generate($this->toRequestData());
    }

    public function toWget(): string
    {
        return (new WgetGenerator)->generate($this->toRequestData());
    }

    public function auth(): AuthBuilder
    {
        return new AuthBuilder($this);
    }

    public function setAuth(?AuthDataTransfer $auth = null): self
    {
        $this->auth = $auth;

        return $this;
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

    public function header(string $name, string $value = ''): self
    {
        $collection = new HttpParameterCollection;

        $updated = $collection
            ->merge(params: $this->headers)
            ->add(key: $name, value: $value);

        $this->headers = $updated->all();

        return $this;
    }

    public function query(string $name, string $value = ''): self
    {
        $collection = new HttpParameterCollection;

        $updated = $collection
            ->merge(params: $this->queries)
            ->add(key: $name, value: $value);

        $this->queries = $updated->all();

        return $this;
    }

    public function body(BodyDataTransfer $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param  array<string, string>|string  $data
     */
    public function json(array|string $data = '', bool $preserveAsRaw = false): self
    {
        return $this->body(new JsonBodyData(data: $data, preserveAsRaw: $preserveAsRaw));
    }

    /**
     * @param  array<string, string>  $data
     */
    public function form(array $data): self
    {
        return $this->body(new FormUrlEncodedData(data: $data));
    }

    /**
     * @param  array<string, string>  $data
     */
    public function multipart(array $data): self
    {
        return $this->body(new MultipartFormData(data: $data));
    }

    public function file(string $filePath): self
    {
        return $this->body(new BinaryData(filePath: $filePath));
    }

    public function encodeQuery(): self
    {
        $this->encodeQuery = true;

        return $this;
    }

    public function includeLineBreaks(): self
    {
        $this->includeLineBreaks = true;

        return $this;
    }

    private function toRequestData(): RequestData
    {
        return new RequestData(
            url: $this->url,
            method: $this->method,
            headers: $this->headers,
            queries: $this->queries,
            auth: $this->auth,
            body: $this->body,
            encodeQuery: $this->encodeQuery,
            includeLineBreaks: $this->includeLineBreaks
        );
    }
}
