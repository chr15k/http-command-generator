<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Wget;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\Body\BinaryData;
use Chr15k\HttpCommand\DataTransfer\Body\FormUrlEncodedData;
use Chr15k\HttpCommand\DataTransfer\Body\JsonBodyData;
use Chr15k\HttpCommand\DataTransfer\Body\MultipartFormData;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Chr15k\HttpCommand\Utils\Url;
use Closure;

/**
 * @internal
 */
final readonly class WgetBody implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $output = match (true) {
            $data->body instanceof JsonBodyData => $this->getJsonBody($data),
            $data->body instanceof FormUrlEncodedData,
            $data->body instanceof MultipartFormData => $this->getFormData($data),
            $data->body instanceof BinaryData => $this->getBinaryData($data),
            default => ''
        };

        $data = $data->copyWithOutput($data->output.$output);

        return $next($data);
    }

    private function getJsonBody(RequestData $data): string
    {
        $jsonBody = $data->body?->getContent() ?? '';

        return " --body-data '$jsonBody'";
    }

    private function getFormData(RequestData $data): string
    {
        $formData = $data->body?->getContent() ?? '';

        $decoded = json_decode($formData, true);

        if (! is_array($decoded) || $decoded === []) {
            return '';
        }

        $query = Url::buildQuery($decoded, $data->encodeQuery);

        return " --body-data '$query'";
    }

    private function getBinaryData(RequestData $data): string
    {
        $formData = $data->body?->getContent() ?? '';

        return " --body-file='$formData'";
    }
}
