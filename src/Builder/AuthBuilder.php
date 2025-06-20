<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Builder;

use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use Chr15k\HttpCommand\Contracts\AuthDataTransfer;
use Chr15k\HttpCommand\Contracts\Builder;
use Chr15k\HttpCommand\DataTransfer\Auth\ApiKeyData;
use Chr15k\HttpCommand\DataTransfer\Auth\BasicAuthData;
use Chr15k\HttpCommand\DataTransfer\Auth\BearerTokenData;
use Chr15k\HttpCommand\DataTransfer\Auth\DigestAuthData;
use Chr15k\HttpCommand\DataTransfer\Auth\JWTData;
use Chr15k\HttpCommand\Enums\HttpMethod;

/**
 * @internal
 */
final readonly class AuthBuilder implements Builder
{
    public function __construct(private CommandBuilder $builder)
    {
        //
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    public function jwt(
        string $key = '',
        array $payload = [],
        array $headers = [],
        Algorithm $algorithm = Algorithm::HS256,
        bool $secretBase64Encoded = false,
        string $headerPrefix = 'Bearer',
        bool $inQuery = false,
        string $queryKey = 'token',
    ): CommandBuilder {
        return $this->setAuthOnBuilder(new JWTData(
            key: $key,
            payload: $payload,
            headers: $headers,
            algorithm: $algorithm,
            secretBase64Encoded: $secretBase64Encoded,
            headerPrefix: $headerPrefix,
            inQuery: $inQuery,
            queryKey: $queryKey,
        ));
    }

    public function apiKey(string $key, string $value, bool $inQuery = false): CommandBuilder
    {
        return $this->setAuthOnBuilder(new ApiKeyData(key: $key, value: $value, inQuery: $inQuery));
    }

    public function basic(string $username, string $password): CommandBuilder
    {
        return $this->setAuthOnBuilder(new BasicAuthData(username: $username, password: $password));
    }

    public function digest(
        string $username = '',
        string $password = '',
        DigestAlgorithm $algorithm = DigestAlgorithm::MD5,
        string $realm = '',
        HttpMethod $method = HttpMethod::GET,
        string $uri = '/',
        string $nonce = '',
        string $nc = '',
        string $cnonce = '',
        string $qop = '',
        string $opaque = '',
        string $entityBody = '',
    ): CommandBuilder {
        return $this->setAuthOnBuilder(new DigestAuthData(
            username: $username,
            password: $password,
            algorithm: $algorithm,
            realm: $realm,
            method: $method,
            uri: $uri,
            nonce: $nonce,
            nc: $nc,
            cnonce: $cnonce,
            qop: $qop,
            opaque: $opaque,
            entityBody: $entityBody
        ));
    }

    public function bearer(string $token): CommandBuilder
    {
        return $this->setAuthOnBuilder(new BearerTokenData(token: $token));
    }

    /**
     * @deprecated use bearer()
     *
     * @codeCoverageIgnoreStart
     */
    public function bearerToken(string $token): CommandBuilder
    {
        return $this->bearer(token: $token);
    } // @codeCoverageIgnoreEnd

    private function setAuthOnBuilder(AuthDataTransfer $auth): CommandBuilder
    {
        $this->builder->setAuth($auth);

        return $this->builder;
    }
}
