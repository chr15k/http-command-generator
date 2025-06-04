<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

final readonly class CurlUrl implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $data->output .= " '{$data->url}'";

        return $next($data);
    }
}
