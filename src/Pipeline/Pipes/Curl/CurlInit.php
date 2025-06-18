<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Curl;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
final readonly class CurlInit implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $data->output = 'curl --location';

        return $next($data);
    }
}
