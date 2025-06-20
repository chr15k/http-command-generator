<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Wget;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
final class WgetTimeout implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $output = sprintf(
            '%s%s--timeout=0',
            $data->output,
            $data->separator()
        );

        $data = $data->copyWithOutput($output);

        return $next($data);
    }
}
