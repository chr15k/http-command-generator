<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Common;

use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\ApiKeyData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\JWTData;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

final readonly class CommonAuthQuery implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        match (true) {
            $data->auth instanceof ApiKeyData => $this->handleApiKeyAuth($data),
            $data->auth instanceof JWTData => $this->handleJWTAuth($data),
            default => null
        };

        return $next($data);
    }

    private function handleApiKeyAuth(RequestData &$data): void
    {
        /** @var ApiKeyData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0' || $auth->inQuery === false) {
            return;
        }

        $separator = parse_url($data->url, PHP_URL_QUERY) ? '&' : '?';
        $url = sprintf('%s%s%s=%s', $data->url, $separator, $auth->key, $auth->value);
        $data->output = str_replace($data->url, $url, $data->output);
    }

    private function handleJWTAuth(RequestData &$data): void
    {
        /** @var JWTData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0' || $auth->inQuery === false) {
            return;
        }

        $token = AuthGenerator::jwt()
            ->key($auth->key, $auth->secretBase64Encoded)
            ->algorithm($auth->algorithm)
            ->headers($auth->headers)
            ->claims($auth->payload)
            ->toString();

        $separator = parse_url($data->url, PHP_URL_QUERY) ? '&' : '?';
        $url = sprintf('%s%s%s=%s', $data->url, $separator, $auth->queryKey, $token);
        $data->output = str_replace($data->url, $url, $data->output);
    }
}
