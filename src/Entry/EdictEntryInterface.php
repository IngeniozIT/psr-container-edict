<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

interface EdictEntryInterface
{
    /**
     * Resolves the entry.
     * @return mixed
     */
    public function resolve();
}
