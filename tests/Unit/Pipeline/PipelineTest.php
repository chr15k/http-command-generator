<?php

declare(strict_types=1);

use Chr15k\HttpCommand\DataTransfer\RequestData;
use Chr15k\HttpCommand\Exceptions\InvalidPipeException;
use Chr15k\HttpCommand\Pipeline\Pipeline;
use Tests\Unit\Pipeline\FakePipes\FakePipeOne;
use Tests\Unit\Pipeline\FakePipes\FakePipeTwo;

test('pipeline can be created', function (): void {
    $pipeline = new Pipeline;
    expect($pipeline)->toBeInstanceOf(Pipeline::class);
});

test('pipeline can be sent with data', function (): void {
    $data = new RequestData;
    $pipeline = Pipeline::send($data);
    expect($pipeline)->toBeInstanceOf(Pipeline::class);
});

test('pipeline can be processed through pipes', function (): void {
    $data = new RequestData;
    $pipeline = Pipeline::send($data)
        ->through([FakePipeOne::class])
        ->thenReturn();

    expect($pipeline->output)->toBe('Fake 1!');
});

test('pipeline can be processed through multiple pipes', function (): void {
    $data = new RequestData;
    $pipeline = Pipeline::send($data)
        ->through([FakePipeOne::class, FakePipeTwo::class])
        ->thenReturn();

    expect($pipeline->output)->toBe('Fake 1!Fake 2!');
});

test('pipeline can be processed through pipes with destination', function (): void {
    $data = new RequestData;
    $pipeline = Pipeline::send($data)
        ->through([FakePipeOne::class])
        ->then(function (RequestData $data): RequestData {
            $data = $data->copyWithOutput($data->output.' Destination!');

            return $data;
        });

    expect($pipeline->output)->toBe('Fake 1! Destination!');
});

test('pipeline can be processed with closure pipes', function (): void {
    $data = new RequestData;

    $data = $data->copyWithOutput('Closure Pipe!');

    $pipeline = Pipeline::send($data)
        ->through([
            fn (RequestData $data, Closure $next) => $next($data),
        ])
        ->thenReturn();

    expect($pipeline->output)->toBe('Closure Pipe!');
});

test('pipeline throws exception for invalid pipe', function (): void {
    $data = new RequestData;
    Pipeline::send($data)
        ->through(['InvalidPipe'])
        ->thenReturn();
})->throws(InvalidPipeException::class);

test('pipeline throws InvalidPipeException for exception thrown via destination closure', function (): void {
    $data = new RequestData;
    Pipeline::send($data)
        ->through([FakePipeOne::class])
        ->then(fn () => throw new Exception('Invalid destination closure'));
})->throws(InvalidPipeException::class);
