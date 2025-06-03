<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Closure;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;

final readonly class CurlInit
{
    public function __invoke(RequestData $data, Closure $next)
    {
        if ($data->url === '') {
            return $data;
        }

        $data->output = 'curl --location';

        return $next($data);
    }
}
