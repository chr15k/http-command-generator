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
        match (true) {
            $data->body instanceof JsonBodyData => $this->handleJsonBody($data),
            $data->body instanceof FormUrlEncodedData,
            $data->body instanceof MultipartFormData => $this->handleFormData($data),
            $data->body instanceof BinaryData => $this->handleBinaryData($data),
            default => null
        };

        return $next($data);
    }

    private function handleJsonBody(RequestData &$data): void
    {
        $jsonBody = $data->body?->getContent() ?? '';

        $data->output .= " --body-data '$jsonBody'";
    }

    /**
     * Format is the same for both url encoded and multipart form data.
     */
    private function handleFormData(RequestData &$data): void
    {
        $formData = $data->body?->getContent() ?? '';

        $decoded = json_decode($formData, true);

        if (! is_array($decoded) || $decoded === []) {
            return;
        }

        $query = Url::buildQuery($decoded, $data->encodeQuery);

        $data->output .= " --body-data '$query'";
    }

    private function handleBinaryData(RequestData &$data): void
    {
        $formData = $data->body?->getContent() ?? '';

        $data->output .= " --body-file='$formData'";
    }
}
