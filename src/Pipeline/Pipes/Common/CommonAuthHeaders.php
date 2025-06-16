<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline\Pipes\Common;

use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\HttpCliGenerator\Contracts\Pipe;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\ApiKeyData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BasicAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\BearerTokenData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\DigestAuthData;
use Chr15k\HttpCliGenerator\DataTransfer\Auth\JWTData;
use Chr15k\HttpCliGenerator\DataTransfer\RequestData;
use Closure;

final readonly class CommonAuthHeaders implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
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

        $encoded = AuthGenerator::basicAuth()
            ->username($auth->username)
            ->password($auth->password ?? '')
            ->toHeader();

        $data->output .= sprintf(" --header 'Authorization: %s'", $encoded);
    }

    private function handleBearerToken(RequestData &$data): void
    {
        /** @var BearerTokenData $auth */
        $auth = $data->auth;

        if ($auth->token === '' || $auth->token === '0') {
            return;
        }

        $data->output .= sprintf(" --header 'Authorization: Bearer %s'", $auth->token);
    }

    private function handleDigestAuth(RequestData &$data): void
    {
        /** @var DigestAuthData $auth */
        $auth = $data->auth;

        if ($auth->username === '' || $auth->realm === '') {
            return;
        }

        $header = AuthGenerator::digestAuth()
            ->username($auth->username)
            ->password($auth->password ?? '')
            ->algorithm($auth->algorithm)
            ->realm($auth->realm)
            ->method($auth->method)
            ->uri($auth->uri)
            ->nonce($auth->nonce)
            ->nc($auth->nc)
            ->cnonce($auth->cnonce)
            ->qop($auth->qop)
            ->opaque($auth->opaque)
            ->entityBody($auth->entityBody)
            ->toHeader();

        $data->output .= sprintf(" --header 'Authorization: %s'", $header);
    }

    private function handleApiKeyAuth(RequestData &$data): void
    {
        /** @var ApiKeyData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0' || $auth->inQuery) {
            return;
        }

        $separator = $auth->value !== '' && $auth->value !== '0' ? ':' : ';';
        $data->output .= sprintf(" --header '%s%s %s'", $auth->key, $separator, $auth->value);
    }

    private function handleJWTAuth(RequestData &$data): void
    {
        /** @var JWTData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0' || $auth->inQuery) {
            return;
        }

        $token = AuthGenerator::jwt()
            ->key($auth->key, $auth->secretBase64Encoded)
            ->algorithm($auth->algorithm)
            ->headers($auth->headers)
            ->claims($auth->payload)
            ->toString();

        $data->output .= str_replace(
            '  ', ' ', sprintf(" --header 'Authorization: %s %s'", $auth->headerPrefix, $token)
        );
    }
}
