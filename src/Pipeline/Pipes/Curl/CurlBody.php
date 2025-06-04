<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestBodyData;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Chr15k\HttpCliGenerator\Enums\BodyType;
use Closure;

final readonly class CurlBody implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        if (! $data->body instanceof RequestBodyData || $data->body->type === BodyType::NONE) {
            return $next($data);
        }

        if ($data->body->type === BodyType::RAW_JSON) {

            $jsonBody = $data->body->getContent();

            $data->output .= " --data '$jsonBody'";
        }

        return $next($data);
    }
}
