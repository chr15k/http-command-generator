<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestBodyData;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

final readonly class CurlHeaders implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        foreach ($data->headers as $key => $value) {
            if (strtolower((string) $key) === 'content-type') {

                $data->output .= " --header \"{$key}: {$value}\"";

                if (stripos($value, 'application/json') !== false) {
                    $data->output .= ' --header "Accept: application/json"';
                }
            } else {
                $data->output .= " --header \"{$key}: {$value}\"";
            }

        }

        if ($data->body instanceof RequestBodyData
            && str_contains(strtolower($data->output), 'content-type:') === false
        ) {
            $value = $data->body->getContentTypeHeader();
            $data->output .= $value !== '' && $value !== '0' ? " --header \"Content-Type: {$value}\"" : '';
        }

        return $next($data);
    }
}
