<?php

declare(strict_types=1);

namespace IngeniozIT\Container;

use Psr\Container\ContainerInterface;
use IngeniozIT\Container\NotFoundException;
use IngeniozIT\Container\ContainerException;

class Edict implements ContainerInterface
{
    const TYPE_STATIC = 1;
    const TYPE_DYNAMIC = 2;

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

        return $this->entries[$id]['type'] === self::TYPE_STATIC ?
            $this->entries[$id]['value'] :
            $this->entries[$id]['value']($this);
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
     * @param string $id Identifier of the entry.
     * @param mixed $value Value the entry should hold.
     */
    public function set(string $id, $value): void
    {
        $this->setEntry($id, $value, self::TYPE_STATIC);
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
     * Binds a callback to an entry.
     *
     * @param string $id Identifier of the entry.
     * @param callable $callback Callback to execute everytime this entry is
     * asked.
     */
    public function bind(string $id, callable $callback): void
    {
        $this->setEntry($id, $callback, self::TYPE_DYNAMIC);
    }

    /**
     * Binds multiple entries to specific callbacks.
     *
     * @param iterable<callable> $entries
     */
    public function bindMultiple(iterable $entries): void
    {
        foreach ($entries as $id => $callback) {
            $this->bind($id, $callback);
        }
    }

    /**
     * @param string $id Identifier of the entry.
     * @param mixed $value Value of the entry.
     * @param int $type Type of the entry. Can either be Edict::TYPE_STATIC or
     * Edict::TYPE_DYNAMIC.
     */
    protected function setEntry(string $id, $value, int $type): void
    {
        $this->entries[$id] = [
            'type' => $type,
            'value' => $value
        ];
    }
}
