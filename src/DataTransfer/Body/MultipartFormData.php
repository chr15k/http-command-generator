<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Body;

use Chr15k\HttpCliGenerator\Contracts\BodyDataTransfer;

final readonly class MultiPartFormData implements BodyDataTransfer
{
    /**
     * @param  array<string, string|resource>  $data
     */
    public function __construct(private array $data = [])
    {
        //
    }

    public static function fromData(array $data): self
    {
        return new self($data);
    }

    public function getContent(): string
    {
        return json_encode($this->data);
    }

    public function getContentTypeHeader(): string
    {
        return 'multipart/form-data';
    }

    public function hasContent(): bool
    {
        return $this->data === [];
    }
}
