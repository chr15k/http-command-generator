<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\DataTransfer\Auth;

use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use Chr15k\HttpCommand\Contracts\AuthDataTransfer;
use SensitiveParameter;

/**
 * @internal
 */
final readonly class DigestAuthData implements AuthDataTransfer
{
    public function __construct(
        public string $username = '',
        #[SensitiveParameter] public string $password = '',
        public DigestAlgorithm $algorithm = DigestAlgorithm::MD5,
        public string $realm = '',
        public string $method = 'GET',
        public string $uri = '/',
        public string $nonce = '',
        public string $nc = '',
        public string $cnonce = '',
        public string $qop = '',
        public string $opaque = '',
        public string $entityBody = ''
    ) {
        //
    }
}
