<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

use Psr\Container\ContainerInterface;

class AliasEntry implements EdictEntryInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var string */
    protected $destId;

    /**
     * Constructor.
     * @param ContainerInterface $container
     * @param string $destId Destination of the alias.
     */
    public function __construct(ContainerInterface $container, string $destId)
    {
        $this->container = $container;
        $this->destId = $destId;
    }

    /**
     * Resolves the entry.
     * @return mixed
     */
    public function resolve()
    {
        return $this->container->get($this->destId);
    }
}
