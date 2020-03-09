<?php

declare(strict_types=1);

namespace IngeniozIT\Container;

use Psr\Container\ContainerInterface;
use IngeniozIT\Container\{
    NotFoundException,
    ContainerException
};
use IngeniozIT\Container\Entry\{
    EdictEntryInterface,
    BasicEntry,
    CallableEntry,
    ClassEntry,
    AliasEntry,
    StaticEntry,
};

class Edict implements ContainerInterface
{
    /** @var EdictEntryInterface[] */
    protected array $entries = [];

    public function __construct()
    {
        $this->set(ContainerInterface::class, $this);
    }

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

        return $this->entries[$id]->resolve();
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
        return $this->hasEntry($id) || $this->autowire($id);
    }

    public function hasEntry(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    protected function autowire(string $className): bool
    {
        if (!class_exists($className)) {
            return false;
        }

        $this->entries[$className] = new ClassEntry($this, $className);

        return true;
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

    /**
     * Sets an entry to a specific value.
     *
     * @param string $id Identifier of the entry.
     * @param mixed $value Value the entry should hold.
     */
    public function set(string $id, $value): void
    {
        $this->entries[$id] = new BasicEntry($value);
    }

    /**
     * Binds multiple entries to specific callbacks.
     *
     * @param iterable<callable> $entries entryId => callback
     */
    public function bindMultiple(iterable $entries): void
    {
        foreach ($entries as $id => $callback) {
            $this->bind($id, $callback);
        }
    }

    /**
     * Binds a callback to an entry.
     *
     * @param string $id Identifier of the entry.
     * @param callable $callback Callback to execute everytime this entry is
     * asked.
     */
    public function bind(string $id, callable $callback): void
    {
        $this->entries[$id] = new CallableEntry($this, $callback);
    }

    /**
     * Binds multiple callbacks to be executed once to several entries.
     *
     * @param iterable<callable> $entries entryId => callback
     */
    public function bindMultipleStatic(iterable $entries): void
    {
        foreach ($entries as $id => $callback) {
            $this->bindStatic($id, $callback);
        }
    }

    /**
     * Binds a callback to be executed once to an entry.
     * Every subsequent call to get will return the same result.
     *
     * @param string $id Identifier of the entry.
     * @param callable $callback Callback to execute everytime this entry is
     * asked.
     */
    public function bindStatic(string $id, callable $callback): void
    {
        $this->entries[$id] = new StaticEntry($this, $id, $callback);
    }

    /**
     * Binds multiple entries to specific interfaces.
     *
     * @param iterable<string> $entries interfaceName => entryId
     */
    public function aliases(iterable $entries): void
    {
        foreach ($entries as $sourceId => $destId) {
            $this->alias($sourceId, $destId);
        }
    }

    /**
     * Binds an entry to another entry.
     *
     * @param string $sourceId
     * @param string $destId
     */
    public function alias(string $sourceId, string $destId): void
    {
        $this->entries[$sourceId] = new AliasEntry($this, $destId);
    }
}
