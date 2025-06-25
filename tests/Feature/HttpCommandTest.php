<?php

declare(strict_types=1);

use Chr15k\HttpCommand\HttpCommand;

it('generates the command', function (...$scenarios): void {
    $command = buildCommand($scenarios);

    foreach ($scenarios['expected'] as $type => $expectedCommand) {
        expect($command->to($type))->toBe($expectedCommand);
    }
})->with('scenarios');

it('generates a command with stringable object', function (): void {
    $object = new class
    {
        public function __toString(): string
        {
            return 'Chris';
        }
    };

    $output = HttpCommand::get('http://localhost')
        ->form(['name' => $object])
        ->toCurl();

    $name = (string) $object;

    expect($output)->toContain('GET', 'http://localhost', "--data-urlencode 'name=$name'");
});

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
