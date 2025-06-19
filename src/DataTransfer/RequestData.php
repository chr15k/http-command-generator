<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\DataTransfer;

use Chr15k\HttpCommand\Contracts\AuthDataTransfer;
use Chr15k\HttpCommand\Contracts\BodyDataTransfer;

/**
 * @internal
 */
final class RequestData
{
    /**
     * @param  array<string, array<int, string>|string>  $headers
     * @param  array<string, array<int, string>|string>  $queries
     */
    public function __construct(
        public readonly string $method = '',
        public readonly string $url = '',
        public readonly array $headers = [],
        public readonly array $queries = [],
        public readonly ?BodyDataTransfer $body = null,
        public readonly ?AuthDataTransfer $auth = null,
        public string $output = '',
        public readonly bool $encodeQuery = false
    ) {
        //
    }
}
