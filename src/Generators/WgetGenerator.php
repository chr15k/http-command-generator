<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Generators;

use Chr15k\HttpCliGenerator\Contracts\Generator;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Chr15k\HttpCliGenerator\Pipeline\Pipeline;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Common\CommonAuthHeaders;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Common\CommonAuthQuery;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Common\CommonHeaders;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Common\CommonUrl;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Wget\WgetBody;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Wget\WgetInit;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Wget\WgetMethod;
use Chr15k\HttpCliGenerator\Pipeline\Pipes\Wget\WgetTimeout;

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
                CommonAuthQuery::class,
            ])
            ->thenReturn()
            ->output;
    }
}
