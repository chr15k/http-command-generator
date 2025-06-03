<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Auth;

final class DigestAuthData
{
    public function __construct(
        public string $username,
        public string $password
    ) {
        //
    }
}
