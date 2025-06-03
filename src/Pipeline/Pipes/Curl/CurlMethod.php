<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Closure;
use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;

final class CurlMethod implements Pipe
{
    public function __invoke(RequestData $data, Closure $next)
    {
        match ($data->method) {
            'GET' => null,
            'HEAD' => $data->output .= ' --request --head',
            default => $data->output .= " --request {$data->method}",
        };

        return $next($data);
    }
}
