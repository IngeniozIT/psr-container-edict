<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests\Classes;

class ClassThatCannotBeAutowired
{
    public function __construct(
        public int $undecidableValue
    ) {
    }
}
