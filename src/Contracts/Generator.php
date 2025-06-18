<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Contracts;

use Chr15k\HttpCommand\DataTransfer\RequestData;

/**
 * @internal
 */
interface Generator
{
    public static function generate(RequestData $data): string;
}
