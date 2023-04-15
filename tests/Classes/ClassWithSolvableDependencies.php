<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests\Classes;

class ClassWithSolvableDependencies
{
    public function __construct(
        public ClassWithoutDependencies $dependency,
    ) {
    }
}
