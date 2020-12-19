<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

class BasicEntry implements EdictEntryInterface
{
    /** @var mixed */
    protected $value;

    /**
     * @param mixed $value Value to be stored.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        return $this->value;
    }
}
