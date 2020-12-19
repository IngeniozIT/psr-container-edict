<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

use IngeniozIT\Container\Edict;

class StaticEntry implements EdictEntryInterface
{
    protected Edict $container;

    protected string $entryId;

    /** @var callable */
    protected $callback;

    public function __construct(Edict $container, string $entryId, callable $callback)
    {
        $this->container = $container;
        $this->entryId = $entryId;
        $this->callback = $callback;
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        $this->container->set(
            $this->entryId,
            (new CallableEntry($this->container, $this->callback))->resolve()
        );
        return $this->container->get($this->entryId);
    }
}
