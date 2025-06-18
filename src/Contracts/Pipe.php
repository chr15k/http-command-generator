<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Contracts;

use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
interface Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData;
}
