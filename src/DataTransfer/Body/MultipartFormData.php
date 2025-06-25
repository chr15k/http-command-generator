<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\DataTransfer\Body;

use Chr15k\HttpCommand\Contracts\BodyDataTransfer;

/**
 * @internal
 */
final readonly class MultipartFormData implements BodyDataTransfer
{
    /**
     * @param  array<string, string|resource>  $data
     */
    public function __construct(public array $data = [])
    {
        //
    }

    public function getContent(): string
    {
        $data = json_encode($this->data);

        return is_string($data) ? $data : '';
    }

    public function getContentTypeHeader(): string
    {
        return '';
    }
}
