<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Builder;

use Chr15k\HttpCliGenerator\Builder\Concerns\BuildsHttpRequests;
use Chr15k\HttpCliGenerator\Contracts\Generator;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Chr15k\HttpCliGenerator\Generators\CurlGenerator;
use InvalidArgumentException;

final class HttpRequestBuilder
{
    use BuildsHttpRequests;

    /** @var array<string, Generator> */
    private array $generators = [];

    /**
     * Create a new HTTP request builder instance
     */
    public function __construct()
    {
        $this->registerGenerator('curl', new CurlGenerator);
    }

    /**
     * Register a custom generator
     */
    public function registerGenerator(string $name, Generator $generator): self
    {
        $this->generators[$name] = $generator;

        return $this;
    }

    /**
     * Generate curl command for the request
     */
    public function toCurl(): string
    {
        return $this->generators['curl']->generate($this->toRequestData());
    }

    /**
     * Generate command using a registered generator
     *
     * @throws InvalidArgumentException If generator isn't registered
     */
    public function to(string $generator): string
    {
        if (! isset($this->generators[$generator])) {
            throw new InvalidArgumentException("Generator '{$generator}' is not registered.");
        }

        return $this->generators[$generator]->generate($this->toRequestData());
    }

    /**
     * Get available generator types
     *
     * @return array<string>
     */
    public function availableGenerators(): array
    {
        return array_keys($this->generators);
    }

    /**
     * Convert builder state to RequestData DTO
     */
    private function toRequestData(): RequestData
    {
        return new RequestData(
            url: $this->url,
            method: $this->method,
            headers: $this->headers,
            auth: $this->auth,
            body: $this->body
        );
    }
}
