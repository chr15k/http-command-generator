<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\DataTransfer\Auth;

use Chr15k\HttpCommand\Contracts\AuthDataTransfer;

/**
 * @internal
 */
final readonly class BearerTokenData implements AuthDataTransfer
{
    public function __construct(
        public string $token
    ) {
        //
    }
}
