<?php

declare(strict_types=1);

namespace IngeniozIT\Edict;

use Psr\Container\ContainerInterface;
use Throwable;

class Container implements ContainerInterface
{
    /** @var callable[] */
    protected array $entries = [];

    protected bool $autowiring = true;

    public function __construct()
    {
        $this->set(self::class, value($this));
        $this->set(ContainerInterface::class, alias(self::class));
    }

    /**
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function get(string $entry): mixed
    {
        if (!$this->has($entry)) {
            throw $this->autowiring && class_exists($entry) ?
                new ContainerException("Class $entry cannot be autowired") :
                new NotFoundException("Entry $entry does not exist");
        }

        return $this->entries[$entry]($this);
    }

    public function has(string $entry): bool
    {
        return isset($this->entries[$entry]) || ($this->autowiring && class_exists($entry) && $this->autowire($entry));
    }

    public function set(string $entry, callable $value): void
    {
        $this->entries[$entry] = $value;
    }

    /**
     * @param iterable<string, callable> $entries
     */
    public function setMany(iterable $entries): void
    {
        foreach ($entries as $entryId => $entryValue) {
            $this->set($entryId, $entryValue);
        }
    }

    /**
     * @param class-string $className
     */
    private function autowire(string $className): bool
    {
        try {
            $this->set($className, objectValue($className));
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function disableAutowiring(): void
    {
        $this->autowiring = false;
    }

    public function enableAutowiring(): void
    {
        $this->autowiring = true;
    }

    /**
     * @throws ContainerException when file does not exist
     */
    public function setFromFile(string $filename): void
    {
        if (!file_exists($filename)) {
            throw new ContainerException("File $filename does not exist");
        }

        $this->setMany(require $filename);
    }
}
