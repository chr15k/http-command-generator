<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Closure;
use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;

final readonly class CurlHeaders implements Pipe
{
    public function __invoke(RequestData $data, Closure $next)
    {
        foreach ($data->headers as $key => $value) {
            if (strtolower((string) $key) === 'Content-Type') {

                $data->output .= " -H \"{$key}: {$value}\"";

                if (stripos($value, 'application/json') !== false) {
                    $data->output .= ' --header "Accept: application/json"';
                }
            } else {
                $data->output .= " -H \"{$key}: {$value}\"";
            }

        }

        return $next($data);
    }
}
