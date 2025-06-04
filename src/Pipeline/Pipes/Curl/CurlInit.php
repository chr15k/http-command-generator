<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

final readonly class CurlInit implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        if ($data->url === '') {
            return $data;
        }

        $data->output = 'curl --location';

        return $next($data);
    }
}
