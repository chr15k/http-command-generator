<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Contracts;

use Chr15k\HttpCliGenerator\DataTransfer\RequestData;

interface Generator
{
    public static function generate(RequestData $data): string;
}
