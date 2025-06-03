<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Auth;

final class ApiKeyData
{
    public function __construct(
        public string $key,
        public string $value,
        public bool $inQuery = false
    ) {
        //
    }
}
