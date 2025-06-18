<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline\Pipes\Common;

use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\HttpCommand\Collections\HttpParameterCollection;
use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\Auth\ApiKeyData;
use Chr15k\HttpCommand\DataTransfer\Auth\JWTData;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Chr15k\HttpCommand\Utils\Url;
use Closure;

/**
 * @internal
 */
final readonly class CommonUrl implements Pipe
{
    public function __invoke(RequestData $data, Closure $next): RequestData
    {
        $params = $this->mergeAllQueryParameters($data);

        $url = Url::mergeQuery($data->url, $params, $data->encode);

        $data->output .= " '{$url}'";

        return $next($data);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function mergeAllQueryParameters(RequestData $data): array
    {
        $auth = match (true) {
            $data->auth instanceof ApiKeyData => $this->getApiKeyAuthParameters($data),
            $data->auth instanceof JWTData => $this->getJWTAuthParameters($data),
            default => []
        };

        $queries = new HttpParameterCollection;
        $queries = $queries->merge(params: $data->queries);

        return $queries->merge(params: $auth)->all();
    }

    /**
     * @return array<string, string>
     */
    private function getApiKeyAuthParameters(RequestData $data): array
    {
        /** @var ApiKeyData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0' || $auth->inQuery === false) {
            return [];
        }

        return [$auth->key => $auth->value];
    }

    /**
     * @return array<string, string>
     */
    private function getJWTAuthParameters(RequestData $data): array
    {
        /** @var JWTData $auth */
        $auth = $data->auth;

        if ($auth->key === '' || $auth->key === '0' || $auth->inQuery === false) {
            return [];
        }

        $token = AuthGenerator::jwt()
            ->key($auth->key, $auth->secretBase64Encoded)
            ->algorithm($auth->algorithm)
            ->headers($auth->headers)
            ->claims($auth->payload)
            ->toString();

        return [$auth->queryKey => $token];
    }
}
