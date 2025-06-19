<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Common;

use Chr15k\HttpCommand\Collections\HttpParameterCollection;
use Chr15k\HttpCommand\Contracts\BodyDataTransfer;
use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
final readonly class CommonHeaders implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $collection = (new HttpParameterCollection)
            ->merge($data->headers);

        $headers = [];
        foreach ($collection->all() as $key => $values) {
            foreach ($values as $value) {
                $headers[] = " --header \"{$key}: {$value}\"";
            }
        }

        $contentTypeExists = array_filter($headers,
            fn ($value): bool => str_contains(strtolower($value), 'content-type:')
        );

        if ($data->body instanceof BodyDataTransfer && $contentTypeExists === []) {
            $value = $data->body->getContentTypeHeader();

            if ($value !== '' && $value !== '0') {
                $headers[] = " --header 'Content-Type: {$value}'";
            }
        }

        $data = $data->copyWithOutput($data->output.implode('', $headers));

        return $next($data);
    }
}
