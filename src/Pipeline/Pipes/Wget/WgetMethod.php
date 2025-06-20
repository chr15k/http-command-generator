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
        $output = sprintf(
            '%s%s--method %s',
            $data->output,
            $data->separator(),
            $data->method
        );

        $data = $data->copyWithOutput($output);

        return $next($data);
    }
}
