<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Generators;

use Chr15k\HttpCommand\Contracts\Generator;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Chr15k\HttpCommand\Pipeline\Pipeline;
use Chr15k\HttpCommand\Pipeline\Pipes\Common\CommonAuthHeaders;
use Chr15k\HttpCommand\Pipeline\Pipes\Common\CommonHeaders;
use Chr15k\HttpCommand\Pipeline\Pipes\Common\CommonUrl;
use Chr15k\HttpCommand\Pipeline\Pipes\Wget\WgetBody;
use Chr15k\HttpCommand\Pipeline\Pipes\Wget\WgetInit;
use Chr15k\HttpCommand\Pipeline\Pipes\Wget\WgetMethod;
use Chr15k\HttpCommand\Pipeline\Pipes\Wget\WgetTimeout;

/**
 * @internal
 */
final class WgetGenerator implements Generator
{
    public static function generate(RequestData $data): string
    {
        return Pipeline::send($data)
            ->through([
                WgetInit::class,
                WgetMethod::class,
                WgetTimeout::class,
                CommonHeaders::class,
                CommonAuthHeaders::class,
                WgetBody::class,
                CommonUrl::class,
            ])
            ->thenReturn()
            ->output;
    }
}
