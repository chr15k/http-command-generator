<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\DataTransfer;

use Chr15k\HttpCliGenerator\Contracts\AuthDataTransfer;

final class RequestData
{
    /**
     * @param  array<string, string>  $headers
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public string $method = '',
        public string $url = '',
        public array $headers = [],
        public array $parameters = [],
        public ?RequestBodyData $body = null,
        public ?AuthDataTransfer $auth = null,
        public string $output = '',
    ) {
        //
    }
}
