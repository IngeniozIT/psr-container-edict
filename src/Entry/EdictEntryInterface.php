<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

interface EdictEntryInterface
{
    /**
     * @return mixed
     */
    public function resolve();
}
