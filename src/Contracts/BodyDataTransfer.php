<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Contracts;

/**
 * @internal
 */
interface BodyDataTransfer
{
    public function getContent(): string;

    public function getContentTypeHeader(): string;
}
