<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Contracts;

use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

interface Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData;
}
