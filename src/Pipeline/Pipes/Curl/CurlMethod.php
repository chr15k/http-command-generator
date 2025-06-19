<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Curl;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
final class CurlMethod implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $output = match ($data->method) {
            'HEAD' => ' --request --head',
            default => " --request {$data->method}",
        };

        $data = $data->copyWithOutput($data->output.$output);

        return $next($data);
    }
}
