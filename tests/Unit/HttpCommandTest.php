<?php

declare(strict_types=1);

use Chr15k\HttpCommand\Exceptions\InvalidHttpMethodException;
use Chr15k\HttpCommand\HttpCommand;

it('throws an exception for invalid HTTP methods', function (): void {
    HttpCommand::invalid('https://example.com');
})->throws(InvalidHttpMethodException::class);

it('throws an exception for non-string URL 1', function (): void {
    HttpCommand::get(123);
})->throws(TypeError::class, 'Argument 1 passed to get must be of type string, integer given');

it('throws an exception for non-string URL 2', function (): void {
    HttpCommand::get([123]);
})->throws(TypeError::class, 'Argument 1 passed to get must be of type string, array given');

it('throws an exception for non-string URL 3', function (): void {
    HttpCommand::get(new stdClass);
})->throws(TypeError::class, 'Argument 1 passed to get must be of type string, object given');

it('creates a command with an empty URL', function (): void {
    $output = HttpCommand::get()->toCurl();
    expect($output)->toContain('GET');
});

it('creates a GET command with a valid URL', function (): void {
    $output = HttpCommand::get('https://example.com')->toCurl();
    expect($output)->toContain('GET', 'https://example.com');
});

it('creates a POST command with a valid URL', function (): void {
    $output = HttpCommand::post('https://example.com')->toCurl();
    expect($output)->toContain('POST', 'https://example.com');
});

it('creates a PUT command with a valid URL', function (): void {
    $output = HttpCommand::put('https://example.com')->toCurl();
    expect($output)->toContain('PUT', 'https://example.com');
});

it('creates a PATCH command with a valid URL', function (): void {
    $output = HttpCommand::patch('https://example.com')->toCurl();
    expect($output)->toContain('PATCH', 'https://example.com');
});

it('creates a DELETE command with a valid URL', function (): void {
    $output = HttpCommand::delete('https://example.com')->toCurl();
    expect($output)->toContain('DELETE', 'https://example.com');
});

it('creates a HEAD command with a valid URL', function (): void {
    $command = HttpCommand::head('https://example.com');
    expect($command->toCurl())->toContain('--head', 'https://example.com');
    expect($command->toWget())->toContain('--method HEAD', 'https://example.com');
});

it('creates an OPTIONS command with a valid URL', function (): void {
    $output = HttpCommand::options('https://example.com')->toCurl();
    expect($output)->toContain('OPTIONS', 'https://example.com');
});
