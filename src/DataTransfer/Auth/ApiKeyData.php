<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\DataTransfer\Auth;

use Chr15k\HttpCommand\Contracts\AuthDataTransfer;

/**
 * @internal
 */
final readonly class ApiKeyData implements AuthDataTransfer
{
    public function __construct(
        public string $key,
        public string $value,
        public bool $inQuery = false
    ) {
        //
    }
}
