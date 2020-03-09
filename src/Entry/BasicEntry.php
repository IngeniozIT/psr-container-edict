<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

class BasicEntry implements EdictEntryInterface
{
    /** @var mixed */
    protected $value;

    /**
     * Constructor.
     * @param mixed $value Value to be stored.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Resolves the entry.
     * @return mixed
     */
    public function resolve()
    {
        return $this->value;
    }
}
