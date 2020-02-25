<?php

declare(strict_types=1);

namespace IngeniozIT\Container;

use Psr\Container\ContainerInterface;
use IngeniozIT\Container\NotFoundException;
use IngeniozIT\Container\ContainerException;

class Edict implements ContainerInterface
{
    /** @var array<mixed> */
    protected array $entries = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for **this** identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Entry $id not found.");
        }

        return $this->entries[$id];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->entries[$id]) || array_key_exists($id, $this->entries);
    }

    /**
     * Sets an entry to a specific value.
     *
     * @param string $id Identifier of the entry to look for.
     * @param mixed $value Value the entry should hold.
     */
    public function set(string $id, $value): void
    {
        $this->entries[$id] = $value;
    }

    /**
     * Sets multiple entries to specific values.
     *
     * @param iterable<mixed> $entries
     */
    public function setMultiple(iterable $entries): void
    {
        foreach ($entries as $id => $value) {
            $this->set($id, $value);
        }
    }
}
