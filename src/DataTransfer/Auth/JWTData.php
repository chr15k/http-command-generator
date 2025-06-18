<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\DataTransfer\Auth;

use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\HttpCommand\Contracts\AuthDataTransfer;

/**
 * @internal
 */
final readonly class JWTData implements AuthDataTransfer
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    public function __construct(
        public string $key = '',
        public array $payload = [],
        public array $headers = [],
        public Algorithm $algorithm = Algorithm::HS256,
        public bool $secretBase64Encoded = false,
        public string $headerPrefix = 'Bearer',
        public bool $inQuery = false,
        public string $queryKey = 'token',
    ) {
        //
    }
}
