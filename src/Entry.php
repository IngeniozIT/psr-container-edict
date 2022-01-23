<?php

namespace IngeniozIT\Edict;

use Closure;
use Psr\Container\ContainerInterface;

class Entry
{
    public function __construct(
        protected readonly Closure $callback
    ) {
    }

    public function resolve(ContainerInterface $container): mixed
    {
        return ($this->callback)($container);
    }
}
