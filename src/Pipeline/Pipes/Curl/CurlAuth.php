<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Curl;

use Closure;
use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\JWTData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\ApiKeyData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BasicAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\DigestAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BearerTokenData;

final readonly class CurlAuth implements Pipe
{
    public function __invoke(RequestData $data, Closure $next)
    {
        match (true) {
            $data->auth instanceof BasicAuthData => $this->handleBasicAuth($data),
            $data->auth instanceof BearerTokenData => $this->handleBearerToken($data),
            $data->auth instanceof DigestAuthData => $this->handleDigestAuth($data),
            $data->auth instanceof ApiKeyData => $this->handleApiKeyAuth($data),
            $data->auth instanceof JWTData => $this->handleJWTAuth($data),
            default => null
        };

        return $next($data);
    }

    private function handleBasicAuth(RequestData &$data): void
    {
        /** @var BasicAuthData $auth */
        $auth = $data->auth;

        if ($auth->username === '' || $auth->username === '0') {
            return;
        }

        $data->output .= sprintf(' -u "%s:%s"', $auth->username, $auth->password ?? '');
    }

    private function handleBearerToken(RequestData &$data): void
    {
        /** @var BearerTokenAuth $auth */
        $auth = $data->auth;

        if ($auth->token === '' || $auth->token === '0') {
            return;
        }

        $data->output .= sprintf(' -H "Authorization: Bearer %s"', $auth->token);
    }

    private function handleDigestAuth(RequestData &$data): void
    {
        /** @var DigestAuthData $auth */
        $auth = $data->auth;

        if ($auth->username === '' || $auth->username === '0') {
            return;
        }

        $data->output .= sprintf(' --digest -u "%s:%s"', $auth->username, $auth->password ?? '');
    }

    private function handleApiKeyAuth(RequestData &$data): void
    {
        /** @var ApiKeyData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0') {
            return;
        }

        if ($auth->inQuery) {
            $separator = parse_url($data->url, PHP_URL_QUERY) ? '&' : '?';
            $url = sprintf('%s%s%s=%s', $data->url, $separator, $auth->key, $auth->value ?? '');
            $data->output = str_replace($data->url, $url, $data->output);
        } else {
            $separator = $auth->value !== '' && $auth->value !== '0' ? ':' : ';';
            $data->output .= sprintf(' --header "%s%s %s"', $auth->key, $separator, $auth->value ?? '');
        }
    }

    private function handleJWTAuth(RequestData &$data): void
    {
        /** @var JWTData $auth */
        $auth = $data->auth;

        if ($auth->token === '') {
            return;
        }

        if ($auth->inQuery) {
            $separator = parse_url($data->url, PHP_URL_QUERY) ? '&' : '?';
            $url = sprintf('%s%s%s=%s', $data->url, $separator, $auth->queryKey, $auth->token);
            $data->output = str_replace($data->url, $url, $data->output);
        } else {
            $data->output .= sprintf(' -H "Authorization: %s %s"', $auth->headerPrefix, $auth->token);
        }
    }
}
