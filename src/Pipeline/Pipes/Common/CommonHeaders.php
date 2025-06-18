<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Common;

use Chr15k\HttpCommand\Contracts\BodyDataTransfer;
use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
final readonly class CommonHeaders implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        foreach ($data->headers as $key => $value) {
            $data->output .= " --header \"{$key}: {$value}\"";
        }

        if ($data->body instanceof BodyDataTransfer
            && str_contains(strtolower($data->output), 'content-type:') === false
        ) {
            $value = $data->body->getContentTypeHeader();
            $data->output .= $value !== '' && $value !== '0' ? " --header 'Content-Type: {$value}'" : '';
        }

        return $next($data);
    }
}
