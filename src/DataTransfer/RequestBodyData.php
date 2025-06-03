<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer;

use Chr15k\HttpCliGenerator\Enums\BodyType;

final class RequestBodyData
{
    /**
     * @param  array<mixed>  $data
     */
    public function __construct(
        public BodyType $type = BodyType::NONE,
        public array $data = [],
    ) {
        //
    }

    public function getContent(): string
    {
        $content = match ($this->type) {
            BodyType::RAW_JSON => $this->data[0] ?? '',
            BodyType::FORM_URLENCODED => http_build_query($this->data),
            BodyType::FORM => http_build_query($this->data),
            default => '',
        };

        return is_string($content) ? $content : '';
    }

    public function getContentTypeHeader(): string
    {
        return match ($this->type) {
            BodyType::RAW_JSON => 'application/json',
            BodyType::FORM_URLENCODED => 'application/x-www-form-urlencoded',
            BodyType::FORM => 'multipart/form-data',
            default => '',
        };
    }
}
