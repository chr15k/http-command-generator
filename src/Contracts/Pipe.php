<?php

namespace Chr15k\HttpCliGenerator\Contracts;

use Closure;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;

interface Pipe
{
    public function __invoke(RequestData $data, Closure $next);
}