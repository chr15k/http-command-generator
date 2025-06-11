<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Body;

use Chr15k\HttpCliGenerator\Contracts\BodyDataTransfer;

final class JsonBodyData implements BodyDataTransfer
{
    private string $rawJson = '';

    public function __construct(
        private readonly array|string $data = '',
        bool $preserveAsRaw = false,
        private readonly int $encodeOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
    ) {
        if (is_string($data) && $preserveAsRaw) {
            $this->rawJson = $data;
        }
    }

    public static function fromRawJson(string $json): self
    {
        return new self($json, true);
    }

    public static function fromData(array $data): self
    {
        return new self($data);
    }

    public function getContent(): string
    {
        if ($this->rawJson !== '') {
            return $this->rawJson;
        }

        if ($this->data === null) {
            return '';
        }

        return json_encode(
            $this->data,
            $this->encodeOptions | JSON_THROW_ON_ERROR
        );
    }

    public function getContentTypeHeader(): string
    {
        return 'application/json';
    }

    public function hasContent(): bool
    {
        return $this->data !== null || $this->rawJson !== '';
    }
}
