<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

final class CurlMethod implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        match ($data->method) {
            'HEAD' => $data->output .= ' --request --head',
            default => $data->output .= " --request {$data->method}",
        };

        return $next($data);
    }
}
