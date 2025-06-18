<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Generators;

use Chr15k\HttpCommand\Contracts\Generator;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Chr15k\HttpCommand\Pipeline\Pipeline;
use Chr15k\HttpCommand\Pipeline\Pipes\Common\CommonAuthHeaders;
use Chr15k\HttpCommand\Pipeline\Pipes\Common\CommonHeaders;
use Chr15k\HttpCommand\Pipeline\Pipes\Common\CommonUrl;
use Chr15k\HttpCommand\Pipeline\Pipes\Curl\CurlBody;
use Chr15k\HttpCommand\Pipeline\Pipes\Curl\CurlInit;
use Chr15k\HttpCommand\Pipeline\Pipes\Curl\CurlMethod;

/**
 * @internal
 */
final class CurlGenerator implements Generator
{
    public static function generate(RequestData $data): string
    {
        return Pipeline::send($data)
            ->through([
                CurlInit::class,
                CurlMethod::class,
                CommonUrl::class,
                CommonHeaders::class,
                CommonAuthHeaders::class,
                CurlBody::class,
            ])
            ->thenReturn()
            ->output;
    }
}
