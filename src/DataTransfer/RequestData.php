<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer;

use Chr15k\HttpCliGenerator\Contracts\AuthDataTransfer;
use Chr15k\HttpCliGenerator\Contracts\BodyDataTransfer;

final class RequestData
{
    /**
     * @param  array<string, string>  $headers
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public readonly string $method = '',
        public string $url = '',
        public readonly array $headers = [],
        public readonly array $parameters = [],
        public readonly ?BodyDataTransfer $body = null,
        public readonly ?AuthDataTransfer $auth = null,
        public string $output = '',
    ) {
        //
    }
}
