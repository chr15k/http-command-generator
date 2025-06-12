<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Auth;

use Chr15k\HttpCliGenerator\Enums\Algorithm;
use Chr15k\HttpCliGenerator\Contracts\AuthDataTransfer;

final class JWTData implements AuthDataTransfer
{
    public function __construct(
        public Algorithm $algorithm = Algorithm::HS256,
        public string $key = '',
        public bool $secretBase64Encoded = false,
        public array $payload = [],
        public array $headers = [],
        public string $headerPrefix = 'Bearer',
        public bool $inQuery = false,
        public string $queryKey = 'token',
    ) {
        //
    }
}
