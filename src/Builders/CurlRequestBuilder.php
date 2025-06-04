<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Builders;

use Chr15k\HttpCliGenerator\Concerns\BuildsHttpRequests;
use Chr15k\HttpCliGenerator\Contracts\Builder;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Chr15k\HttpCliGenerator\Generators\CurlGenerator;

final class CurlRequestBuilder implements Builder
{
    use BuildsHttpRequests;

    public function generate(): string
    {
        $requestData = new RequestData(
            url: $this->url,
            method: $this->method,
            headers: $this->headers,
            body: $this->body,
            auth: $this->auth
        );

        return CurlGenerator::generate($requestData);
    }
}
