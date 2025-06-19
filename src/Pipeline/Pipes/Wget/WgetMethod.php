<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Wget;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
final class WgetMethod implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $data = $data->copyWithOutput($data->output." --method {$data->method}");

        return $next($data);
    }
}
