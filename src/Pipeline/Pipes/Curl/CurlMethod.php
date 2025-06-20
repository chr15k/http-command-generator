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
        $method = match ($data->method) {
            'HEAD' => '--request --head',
            default => "--request {$data->method}",
        };

        $output = sprintf('%s%s%s', $data->output, $data->separator(), $method);

        $data = $data->copyWithOutput($output);

        return $next($data);
    }
}
