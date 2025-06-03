<?php

declare(strict_types=1);

namespace Tests\Unit\Pipeline\FakePipes;

use Closure;
use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;

final class FakePipeTwo implements Pipe
{
    public function __invoke(RequestData $data, Closure $next)
    {
        $data->output .= 'Fake 2!';

        return $next($data);
    }
}
