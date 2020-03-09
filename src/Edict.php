<?php

declare(strict_types=1);

namespace IngeniozIT\Container;

use Psr\Container\ContainerInterface;
use IngeniozIT\Container\NotFoundException;
use IngeniozIT\Container\ContainerException;
use ReflectionClass;

class Edict implements ContainerInterface
{
    const TYPE_STATIC = 1;
    const TYPE_CALLABLE = 2;
    const TYPE_CLASS = 4;
    const TYPE_CONSTRUCTOR = 8;

    /** @var array<mixed> */
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

        return $this->resolveEntry($id);
    }

    /**
     * Resolves an entry.
     *
     * @return mixed
     */
    protected function resolveEntry(string $id)
    {
        switch ($this->entries[$id]['type']) {
            case self::TYPE_CALLABLE:
                return $this->resolveCallableEntry($id);
            case self::TYPE_CLASS:
                return $this->resolveClassEntry($id);
            case self::TYPE_CONSTRUCTOR:
                return $this->resolveConstructorEntry($id);
        }
        return $this->resolveStaticEntry($id);
    }

    /**
     * Resolves a static entry.
     *
     * @return mixed
     */
    protected function resolveStaticEntry(string $id)
    {
        return $this->entries[$id]['value'];
    }

    /**
     * Resolves an entry based on a callback.
     *
     * @return mixed
     */
    protected function resolveCallableEntry(string $id)
    {
        return $this->entries[$id]['callback']($this);
    }

    /**
     * Resolves an entry based on a class name.
     *
     * @return mixed
     * @suppress PhanTypeExpectedObjectOrClassName 'className' will always
     * contain a class name.
     */
    protected function resolveClassEntry(string $id)
    {
        return new $this->entries[$id]['className']();
    }

    /**
     * Resolves an entry based on a constructor.
     *
     * @return mixed
     * @suppress PhanTypeExpectedObjectOrClassName 'className' will always
     * contain a class name.
     */
    protected function resolveConstructorEntry(string $id)
    {
        return new $this->entries[$id]['className'](
            ...array_map(
                fn($p) => $this->get($p['type']),
                $this->entries[$id]['params']
            )
        );
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
        return isset($this->entries[$id]) || array_key_exists($id, $this->entries);
    }

    protected function autowire(string $className): bool
    {
        if (!class_exists($className)) {
            return false;
        }

        $constructorParams = self::getConstructorParams($className);

        if (empty($constructorParams)) {
            $this->addClassEntry($className);
        } else {
            $this->addConstructorEntry($className, $constructorParams);
        }

        return true;
    }

    /**
     * Get a class constructor parameters
     * @param  string $className
     *
     * @return array<array> $parameters
     */
    protected static function getConstructorParams(string $className): array
    {
        $constructorParams = [];

        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $parameter) {
                $constructorParams[] = [
                    'name' => $parameter->getName(),
                    'type' => (string)$parameter->getType(),
                ];
            }
        }

        return $constructorParams;
    }

    protected function addClassEntry(string $className): void
    {
        $this->entries[$className] = [
            'type' => self::TYPE_CLASS,
            'className' => $className,
        ];
    }

    /**
     * Adds an entry based on a class constructor.
     * @param string $className
     * @param array<array> $parameters
     */
    protected function addConstructorEntry(string $className, array $parameters): void
    {
        $this->entries[$className] = [
            'type' => self::TYPE_CONSTRUCTOR,
            'className' => $className,
            'params' => $parameters,
        ];
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
        $this->addStaticEntry($id, $value);
    }

    /**
     * @param string $id Identifier of the entry.
     * @param mixed $value Value of the entry.
     */
    protected function addStaticEntry(string $id, $value): void
    {
        $this->entries[$id] = [
            'type' => self::TYPE_STATIC,
            'value' => $value
        ];
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
     * Binds a callback to an entry.
     *
     * @param string $id Identifier of the entry.
     * @param callable $callback Callback to execute everytime this entry is
     * asked.
     */
    public function bind(string $id, callable $callback): void
    {
        $this->addCallableEntry($id, $callback);
    }

    protected function addCallableEntry(string $id, callable $callback): void
    {
        $this->entries[$id] = [
            'type' => self::TYPE_CALLABLE,
            'callback' => $callback,
        ];
    }
}
