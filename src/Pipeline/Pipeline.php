<?php

declare(strict_types=1);

namespace Chr15k\HttpCommand\Pipeline;

use Chr15k\HttpCommand\Contracts\Pipe;
use Chr15k\HttpCommand\DataTransfer\RequestData;
use Chr15k\HttpCommand\Exceptions\InvalidPipeException;
use Closure;
use Throwable;

/**
 * @internal
 */
final class Pipeline
{
    private RequestData $passable;

    /**
     * @var array<int, Pipe|Closure|string>
     */
    private array $pipes = [];

    public static function send(RequestData $passable): self
    {
        $pipeline = new self;

        $pipeline->passable = $passable;

        return $pipeline;
    }

    /**
     * @param  array<int, Pipe|Closure|string>  $pipes
     */
    public function through(array $pipes): self
    {
        $this->pipes = $pipes;

        return $this;
    }

    public function then(Closure $destination): RequestData
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    public function thenReturn(): RequestData
    {
        return $this->then(fn (RequestData $passable): RequestData => $passable);
    }

    private function prepareDestination(Closure $destination): Closure
    {
        return function (RequestData $passable) use ($destination) {
            try {
                return $destination($passable);
            } catch (Throwable $e) {
                return $this->handleException($e);
            }
        };
    }

    private function carry(): Closure
    {
        return fn ($stack, $pipe): Closure => function (RequestData $passable) use ($stack, $pipe) {
            try {
                if ($pipe instanceof Closure) {
                    return $pipe($passable, $stack);
                }

                if (is_string($pipe) && class_exists($pipe) && is_subclass_of($pipe, Pipe::class)) {
                    $instance = new $pipe;

                    return $instance($passable, $stack);
                }
                throw new InvalidPipeException;
            } catch (Throwable $e) {
                return $this->handleException($e);
            }
        };
    }

    private function handleException(Throwable $e): never
    {
        throw new InvalidPipeException(
            message: 'An error occurred while processing the pipeline: '.$e->getMessage(),
            previous: $e
        );
    }
}
