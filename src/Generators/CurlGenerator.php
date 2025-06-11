<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Generators;

use Chr15k\HttpCliGenerator\Contracts\Generator;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Chr15k\HttpCliGenerator\Pipeline\Pipeline;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Common\CommonUrl;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl\CurlAuth;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl\CurlBody;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl\CurlHeaders;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl\CurlInit;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl\CurlMethod;

final class CurlGenerator implements Generator
{
    public static function generate(RequestData $data): string
    {
        return Pipeline::send($data)
            ->through([
                CurlInit::class,
                CurlMethod::class,
                CommonUrl::class,
                CurlHeaders::class,
                CurlAuth::class,
                CurlBody::class,
            ])
            ->thenReturn()
            ->output;
    }
}
