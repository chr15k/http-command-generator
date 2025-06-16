<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Wget;

use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

final class WgetTimeout implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $data->output .= ' --timeout=0';

        return $next($data);
    }
}
