<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Pipeline;

use Closure;
use InvalidArgumentException;
use Throwable;

final class Pipeline
{
    private $passable;

    private ?array $pipes = null;

    public static function send($passable): self
    {
        $pipeline = new self;

        $pipeline->passable = $passable;

        return $pipeline;
    }

    public function through(array $pipes): self
    {
        $this->pipes = $pipes;

        return $this;
    }

    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    public function thenReturn()
    {
        return $this->then(fn ($passable) => $passable);
    }

    private function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            try {
                return $destination($passable);
            } catch (Throwable $e) {
                return $this->handleException($e);
            }
        };
    }

    private function carry()
    {
        return fn ($stack, $pipe): Closure => function ($passable) use ($stack, $pipe) {
            try {
                if (is_callable($pipe)) {
                    return $pipe($passable, $stack);
                }
                if (is_object($pipe)) {
                    return $pipe($passable, $stack);
                }
                if (is_string($pipe) && class_exists($pipe)) {
                    $pipeInstance = new $pipe;

                    return $pipeInstance($passable, $stack);
                }
                throw new InvalidArgumentException('Invalid pipe type.');
            } catch (Throwable $e) {
                return $this->handleException($e);
            }
        };
    }

    private function handleException(Throwable $e): never
    {
        throw $e;
    }
}
