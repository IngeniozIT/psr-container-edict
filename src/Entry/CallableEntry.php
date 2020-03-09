<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

use Psr\Container\ContainerInterface;

class CallableEntry implements EdictEntryInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var callable */
    protected $callback;

    /**
     * Constructor.
     * @param callable $callback Callback to be stored.
     */
    public function __construct(ContainerInterface $container, callable $callback)
    {
        $this->container = $container;
        $this->callback = $callback;
    }

    /**
     * Resolves the entry.
     * @return mixed
     */
    public function resolve()
    {
        return ($this->callback)(
            $this->container->get(ContainerInterface::class)
        );
    }
}
