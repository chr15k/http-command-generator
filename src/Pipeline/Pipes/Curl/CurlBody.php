<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Curl;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\Body\BinaryData;
use Chr15k\HttpCommand\DataTransfer\Body\FormUrlEncodedData;
use Chr15k\HttpCommand\DataTransfer\Body\JsonBodyData;
use Chr15k\HttpCommand\DataTransfer\Body\MultipartFormData;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
final readonly class CurlBody implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        match (true) {
            $data->body instanceof JsonBodyData => $this->handleJsonBody($data),
            $data->body instanceof FormUrlEncodedData => $this->handleFormUrlEncodedBody($data),
            $data->body instanceof MultipartFormData => $this->handleMultiPartFormData($data),
            $data->body instanceof BinaryData => $this->handleBinaryData($data),
            default => null
        };

        return $next($data);
    }

    private function handleJsonBody(RequestData &$data): void
    {
        $jsonBody = $data->body?->getContent() ?? '';

        $data->output .= " --data '$jsonBody'";
    }

    private function handleFormUrlEncodedBody(RequestData &$data): void
    {
        $formData = $data->body?->getContent() ?? '';

        $decoded = json_decode($formData, true);

        if (! is_array($decoded) || $decoded === []) {
            return;
        }

        foreach ($decoded as $key => $value) {
            $data->output .= " --data-urlencode '$key=$value'";
        }
    }

    private function handleMultiPartFormData(RequestData &$data): void
    {
        $formData = $data->body?->getContent() ?? '';

        $decoded = json_decode($formData, true);

        if (! is_array($decoded) || $decoded === []) {
            return;
        }

        foreach ($decoded as $key => $value) {
            $data->output .= " --form '$key=$value'";
        }
    }

    private function handleBinaryData(RequestData &$data): void
    {
        $formData = $data->body?->getContent() ?? '';

        $data->output .= " --data-binary '@$formData'";
    }
}
