<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Closure;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;

final readonly class CurlUrl
{
    public function __invoke(RequestData $data, Closure $next)
    {
        $data->output .= " '{$data->url}'";

        return $next($data);
    }
}
