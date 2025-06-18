<?php

declare(strict_types=1);

namespace Tests\Unit\Pipeline\FakePipes;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

final class FakePipeOne implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $data->output .= 'Fake 1!';

        return $next($data);
    }
}
