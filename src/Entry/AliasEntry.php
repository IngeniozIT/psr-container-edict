<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

use Psr\Container\ContainerInterface;

class AliasEntry implements EdictEntryInterface
{
    protected ContainerInterface $container;

    protected string $aliasDestinationId;

    public function __construct(ContainerInterface $container, string $aliasDestinationId)
    {
        $this->container = $container;
        $this->aliasDestinationId = $aliasDestinationId;
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        return $this->container->get($this->aliasDestinationId);
    }
}
