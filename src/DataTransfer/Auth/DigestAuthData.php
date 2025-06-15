<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Auth;

use Chr15k\HttpCliGenerator\Contracts\AuthDataTransfer;

final readonly class DigestAuthData implements AuthDataTransfer
{
    public function __construct(
        public string $username,
        public string $password
    ) {
        //
    }
}
