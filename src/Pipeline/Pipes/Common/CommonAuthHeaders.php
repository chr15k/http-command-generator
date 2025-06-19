<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Common;

use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\Auth\ApiKeyData;
use Chr15k\HttpCommand\DataTransfer\Auth\BasicAuthData;
use Chr15k\HttpCommand\DataTransfer\Auth\BearerTokenData;
use Chr15k\HttpCommand\DataTransfer\Auth\DigestAuthData;
use Chr15k\HttpCommand\DataTransfer\Auth\JWTData;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Closure;

/**
 * @internal
 */
final readonly class CommonAuthHeaders implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $output = match (true) {
            $data->auth instanceof BasicAuthData => $this->getBasicAuth($data),
            $data->auth instanceof BearerTokenData => $this->getBearerToken($data),
            $data->auth instanceof DigestAuthData => $this->getDigestAuth($data),
            $data->auth instanceof ApiKeyData => $this->getApiKeyAuth($data),
            $data->auth instanceof JWTData => $this->getJWTAuth($data),
            default => ''
        };

        $data = $data->copyWithOutput($data->output.$output);

        return $next($data);
    }

    private function getBasicAuth(RequestData $data): string
    {
        /** @var BasicAuthData $auth */
        $auth = $data->auth;

        if ($auth->username === '' || $auth->username === '0') {
            return '';
        }

        $encoded = AuthGenerator::basicAuth()
            ->username($auth->username)
            ->password($auth->password ?? '')
            ->toHeader();

        return sprintf(" --header 'Authorization: %s'", $encoded);
    }

    private function getBearerToken(RequestData $data): string
    {
        /** @var BearerTokenData $auth */
        $auth = $data->auth;

        if ($auth->token === '' || $auth->token === '0') {
            return '';
        }

        return sprintf(" --header 'Authorization: Bearer %s'", $auth->token);
    }

    private function getDigestAuth(RequestData $data): string
    {
        /** @var DigestAuthData $auth */
        $auth = $data->auth;

        if ($auth->username === '' || $auth->realm === '') {
            return '';
        }

        $header = AuthGenerator::digestAuth()
            ->username($auth->username)
            ->password($auth->password ?? '')
            ->algorithm($auth->algorithm)
            ->realm($auth->realm)
            ->method($auth->method)
            ->uri($auth->uri)
            ->nonce($auth->nonce)
            ->nonceCount($auth->nc)
            ->clientNonce($auth->cnonce)
            ->qop($auth->qop)
            ->opaque($auth->opaque)
            ->toHeader();

        return sprintf(" --header 'Authorization: %s'", $header);
    }

    private function getApiKeyAuth(RequestData $data): string
    {
        /** @var ApiKeyData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0' || $auth->inQuery) {
            return '';
        }

        $separator = $auth->value !== '' && $auth->value !== '0' ? ':' : ';';

        return sprintf(" --header '%s%s %s'", $auth->key, $separator, $auth->value);
    }

    private function getJWTAuth(RequestData $data): string
    {
        /** @var JWTData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0' || $auth->inQuery) {
            return '';
        }

        $token = AuthGenerator::jwt()
            ->key($auth->key, $auth->secretBase64Encoded)
            ->algorithm($auth->algorithm)
            ->headers($auth->headers)
            ->claims($auth->payload)
            ->toString();

        return str_replace(
            '  ', ' ', sprintf(" --header 'Authorization: %s %s'", $auth->headerPrefix, $token)
        );
    }
}
