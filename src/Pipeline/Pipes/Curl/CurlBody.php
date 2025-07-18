<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Curl;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\Body\BinaryData;
use Chr15k\HttpCommand\DataTransfer\Body\FormUrlEncodedData;
use Chr15k\HttpCommand\DataTransfer\Body\JsonBodyData;
use Chr15k\HttpCommand\DataTransfer\Body\MultipartFormData;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Chr15k\HttpCommand\Utils\Type;
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

        $output = trim(sprintf('%s %s', $data->output, $body));

        $data = $data->copyWithOutput($output);

        return $next($data);
    }

    private function getJsonBody(RequestData $data): string
    {
        $jsonBody = $data->body?->getContent() ?? '';

        return sprintf("--data '%s'", $jsonBody);
    }

    private function getFormUrlEncodedBody(RequestData $data): string
    {
        $return = [];
        foreach ($data->body?->data ?? [] as $key => $values) {
            if (Type::isStringable($key)) {
                foreach (Type::normalizeToStringableArray($values) as $value) {
                    $return[] = sprintf(
                        "%s--data-urlencode '%s=%s'",
                        $data->separator(),
                        rawurlencode((string) $key),
                        rawurlencode((string) $value)
                    );
                }
            }
        }

        return trim(implode('', $return));
    }

    private function getMultiPartFormData(RequestData $data): string
    {
        $return = [];
        foreach ($data->body?->data ?? [] as $key => $values) {
            if (Type::isStringable($key)) {
                foreach (Type::normalizeToStringableArray($values) as $value) {
                    $return[] = sprintf(
                        "%s--form '%s=%s'",
                        $data->separator(),
                        (string) $key,
                        (string) $value
                    );
                }
            }
        }

        return trim(implode('', $return));
    }

    private function getBinaryData(RequestData $data): string
    {
        $formData = $data->body?->getContent() ?? '';

        return "--data-binary '@$formData'";
    }
}
