<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Auth;

final class JWTData
{
    public function __construct(
        public string $token,
        public bool $inQuery = false,
        public string $queryKey = 'token',
        public string $headerPrefix = ''
    ) {
        //
    }
}
