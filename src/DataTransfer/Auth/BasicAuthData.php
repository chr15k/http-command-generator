<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer\Auth;

use Chr15k\HttpCliGenerator\Contracts\AuthDataTransfer;

final readonly class BasicAuthData implements AuthDataTransfer
{
    /**
     * @param  string  $username  The plain-text username for basic authentication
     * @param  string  $password  The plain-text password for basic authentication
    * @param  bool  $preEncode  If true, credentials will be pre-encoded and added to Authorization header
     */
    public function __construct(
        public string $username,
        public string $password,
        public bool $preEncode = false,
    ) {
        //
    }
}
