<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Contracts;

interface BodyDataTransfer
{
    public function getContent(): string;

    public function getContentTypeHeader(): string;
}
