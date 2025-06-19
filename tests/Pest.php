<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use Chr15k\HttpCommand\Builder\CommandBuilder;
use Chr15k\HttpCommand\HttpCommand;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function buildCommand(array $scenario): CommandBuilder
{
    $method = $scenario['method'];
    $url = $scenario['url'];

    $command = match ($method) {
        'GET' => HttpCommand::get($url),
        'POST' => HttpCommand::post($url),
        'PUT' => HttpCommand::put($url),
        'PATCH' => HttpCommand::patch($url),
        'DELETE' => HttpCommand::delete($url),
        default => HttpCommand::get($url)->method($method)
    };

    if (! empty($scenario['headers'])) {
        if (is_array($scenario['headers']) && isset($scenario['headers'][0]) && is_array($scenario['headers'][0])) {
            foreach ($scenario['headers'] as $header) {
                foreach ($header as $key => $value) {
                    $command->header($key, $value);
                }
            }
        } else {
            foreach ($scenario['headers'] as $key => $value) {
                $command->header($key, $value);
            }
        }
    }

    if (! empty($scenario['queries'])) {
        if (is_array($scenario['queries']) && isset($scenario['queries'][0]) && is_array($scenario['queries'][0])) {
            foreach ($scenario['queries'] as $query) {
                $command->query($query[0], $query[1]);
            }
        } else {
            foreach ($scenario['queries'] as $key => $value) {
                $command->query($key, $value);
            }
        }
    }

    if ($scenario['encodeQuery'] ?? false) {
        $command->encodeQuery();
    }

    if ($body = $scenario['body'] ?? null) {
        switch ($body['type']) {
            case 'json':
                if (isset($body['raw']) && $body['raw']) {
                    $command->json($body['data'], true);
                } else {
                    $command->json($body['data']);
                }
                break;
            case 'form':
                $command->form($body['data']);
                break;
            case 'multipart':
                $command->multipart($body['data']);
                break;
            case 'file':
                $command->file($body['path']);
                break;
        }
    }

    if ($auth = $scenario['auth'] ?? null) {
        $authBuilder = $command->auth();

        switch ($auth['type']) {
            case 'basic':
                $authBuilder->basic($auth['username'], $auth['password']);
                break;
            case 'bearer':
                $authBuilder->bearerToken($auth['token']);
                break;
            case 'digest':
                $authBuilder->digest(
                    $auth['username'] ?? '',
                    $auth['password'] ?? '',
                    $auth['algorithm'] ?? DigestAlgorithm::MD5,
                    $auth['realm'] ?? '',
                    $auth['method'] ?? 'GET',
                    $auth['uri'] ?? '',
                    $auth['nonce'] ?? '',
                    $auth['nc'] ?? '',
                    $auth['cnonce'] ?? '',
                    $auth['qop'] ?? '',
                    $auth['opaque'] ?? '',
                    $auth['entityBody'] ?? ''
                );
                break;
            case 'apiKey':
                $inQuery = $auth['inQuery'] ?? false;
                $authBuilder->apiKey($auth['key'], $auth['value'], $inQuery);
                break;
            case 'jwt':
                $authBuilder->jwt(
                    $auth['key'] ?? '',
                    $auth['payload'] ?? [],
                    $auth['headers'] ?? [],
                    $auth['algorithm'] ?? Algorithm::HS256,
                    $auth['secretBase64Encoded'] ?? false,
                    $auth['headerPrefix'] ?? 'Bearer',
                    $auth['inQuery'] ?? false,
                    $auth['queryKey'] ?? ''
                );
                break;
        }
    }

    return $command;
}
