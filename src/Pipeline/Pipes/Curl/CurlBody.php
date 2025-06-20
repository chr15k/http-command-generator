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
        $body = match (true) {
            $data->body instanceof JsonBodyData => $this->getJsonBody($data),
            $data->body instanceof FormUrlEncodedData => $this->getFormUrlEncodedBody($data),
            $data->body instanceof MultipartFormData => $this->getMultiPartFormData($data),
            $data->body instanceof BinaryData => $this->getBinaryData($data),
            default => null
        };

        if ($body === null) {
            return $next($data);
        }

        $output = trim(sprintf('%s%s%s', $data->output, $data->separator(), $body));

        $data = $data->copyWithOutput($output);

        return $next($data);
    }

    private function getJsonBody(RequestData $data): string
    {
        $jsonBody = $data->body?->getContent() ?? '';

        return "--data '$jsonBody'";
    }

    private function getFormUrlEncodedBody(RequestData $data): string
    {
        $formData = $data->body?->getContent() ?? '';

        $decoded = json_decode($formData, true);

        if (! is_array($decoded) || $decoded === []) {
            return '';
        }

        $return = [];
        foreach ($decoded as $key => $value) {
            $return[] = " --data-urlencode '$key=$value'";
        }

        return trim(implode('', $return));
    }

    private function getMultiPartFormData(RequestData $data): string
    {
        $formData = $data->body?->getContent() ?? '';

        $decoded = json_decode($formData, true);

        if (! is_array($decoded) || $decoded === []) {
            return '';
        }

        $return = [];
        foreach ($decoded as $key => $value) {
            $return[] = " --form '$key=$value'";
        }

        return trim(implode('', $return));
    }

    private function getBinaryData(RequestData $data): string
    {
        $formData = $data->body?->getContent() ?? '';

        return "--data-binary '@$formData'";
    }
}
