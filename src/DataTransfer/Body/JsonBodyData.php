<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\DataTransfer\Body;

use Chr15k\HttpCommand\Contracts\BodyDataTransfer;

/**
 * @internal
 */
final class JsonBodyData implements BodyDataTransfer
{
    private string $rawJson = '';

    /**
     * @param  array<string, mixed>|string  $data
     */
    public function __construct(
        private readonly array|string $data = '',
        bool $preserveAsRaw = false,
        private readonly int $encodeOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
    ) {
        if (is_string($data) && $preserveAsRaw) {
            $this->rawJson = $data;
        }
    }

    public function getContent(): string
    {
        if ($this->rawJson !== '') {
            return $this->rawJson;
        }

        if ($this->data === [] || $this->data === '') {
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
}
