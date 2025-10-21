<?php

declare(strict_types=1);

namespace IngeniozIt\Edict;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    use EntryTrait;

    /** @var callable[] */
    protected array $entries = [];

    protected bool $autowiring = true;

    public function __construct()
    {
        $this->set(static::class, self::value($this));
        $this->set(ContainerInterface::class, self::alias(static::class));
    }

    /**
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function get(string $entry): mixed
    {
        if (!$this->has($entry)) {
            throw new NotFoundException(
                class_exists($entry) ?
                "Class $entry exists, but autowiring is disabled" :
                "Entry $entry cannot be autowired"
            );
        }

        return $this->entries[$entry]($this);
    }

    public function has(string $entry): bool
    {
        if (isset($this->entries[$entry])) {
            return true;
        }

        if (!$this->autowiring) {
            return false;
        }

        try {
            $this->set($entry, self::autowire($entry));
        } catch (ContainerExceptionInterface) {
            return false;
        }

        return true;
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

        /** @var iterable<string, callable> $entries */
        $entries = include $filename;
        $this->setMany($entries);
    }
}
