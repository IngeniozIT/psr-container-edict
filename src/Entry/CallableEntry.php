<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

use Psr\Container\ContainerInterface;

class CallableEntry implements EdictEntryInterface
{
    protected ContainerInterface $container;

    /** @var callable */
    protected $callback;

    public function __construct(ContainerInterface $container, callable $callback)
    {
        $this->container = $container;
        $this->callback = $callback;
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        return ($this->callback)($this->container->get(ContainerInterface::class));
    }
}
