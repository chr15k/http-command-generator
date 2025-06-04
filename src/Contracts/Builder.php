<?php

declare(strict_types=1);

namespace Chr15k\HttpCliGenerator\Contracts;

interface Builder
{
    public function generate(): string;
}
