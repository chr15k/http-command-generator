<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\DataTransfer;

use Chr15k\HttpCommand\Contracts\AuthDataTransfer;
use Chr15k\HttpCommand\Contracts\BodyDataTransfer;
use Chr15k\HttpCommand\Enums\HttpMethod;

/**
 * @internal
 */
final readonly class RequestData
{
    /**
     * @param  array<string, array<int, string>|string>  $headers
     * @param  array<string, array<int, string>|string>  $queries
     */
    public function __construct(
        public HttpMethod $method = HttpMethod::GET,
        public string $url = '',
        public array $headers = [],
        public array $queries = [],
        public ?BodyDataTransfer $body = null,
        public ?AuthDataTransfer $auth = null,
        public string $output = '',
        public bool $encodeQuery = false,
        public bool $includeLineBreaks = false
    ) {
        //
    }

    /**
     * Creates a copy of the RequestData with a new output string to ensure immutability of DTO.
     */
    public function copyWithOutput(string $output): self
    {
        return new self(
            method: $this->method,
            url: $this->url,
            headers: $this->headers,
            queries: $this->queries,
            body: $this->body,
            auth: $this->auth,
            output: $output,
            encodeQuery: $this->encodeQuery,
            includeLineBreaks: $this->includeLineBreaks
        );
    }

    public function separator(): string
    {
        return $this->includeLineBreaks ? " \\\n " : ' ';
    }
}
