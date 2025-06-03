<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Closure;
use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\Enums\BodyType;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;

final readonly class CurlBody implements Pipe
{
    public function __invoke(RequestData $data, Closure $next)
    {
        if ($data->body->type === BodyType::RAW_JSON && in_array($data->method, ['POST', 'PUT', 'PATCH'])) {

            // TODO - implement different body types (currently just raw json)

            $jsonBody = $data->body->getContent();

            $data->output .= " --data '$jsonBody'";
        }

        return $next($data);
    }
}
