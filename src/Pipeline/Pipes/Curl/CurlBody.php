<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Chr15k\HttpCliGenerator\Contracts\BodyDataTransfer;
use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\Body\FormUrlEncodedData;
use Chr15k\HttpCliGenerator\DataTransfer\Body\JsonBodyData;
use Chr15k\HttpCliGenerator\DataTransfer\Body\MultipartFormData;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

final readonly class CurlBody implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        if (! $data->body instanceof BodyDataTransfer) {
            return $next($data);
        }

        match (true) {
            $data->body instanceof JsonBodyData => $this->handleJsonBody($data),
            $data->body instanceof FormUrlEncodedData => $this->handleFormUrlEncodedBody($data),
            $data->body instanceof MultipartFormData => $this->handleMultiPartFormData($data),
            default => null
        };

        return $next($data);
    }

    private function handleJsonBody(RequestData &$data): void
    {
        $jsonBody = $data->body->getContent();

        $data->output .= " --data '$jsonBody'";
    }

    private function handleFormUrlEncodedBody(RequestData &$data): void
    {
        $formData = $data->body->getContent();

        if ($formData === '') {
            return;
        }

        $decoded = json_decode($formData, true);

        if (! is_array($decoded)) {
            return;
        }

        foreach ($decoded as $key => $value) {
            $data->output .= " --data-urlencode '$key=$value'";
        }
    }

    private function handleMultiPartFormData(RequestData &$data): void
    {
        $formData = $data->body->getContent();

        if ($formData === '') {
            return;
        }

        $decoded = json_decode($formData, true);

        if (! is_array($decoded)) {
            return;
        }

        foreach ($decoded as $key => $value) {
            $data->output .= " --form '$key=$value'";
        }
    }
}
