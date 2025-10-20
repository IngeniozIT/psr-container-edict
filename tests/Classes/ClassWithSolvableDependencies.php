<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests\Classes;

class ClassWithSolvableDependencies implements ClassWithSolvableDependenciesInterface
{
    public function __construct(
        public ClassWithoutDependencies $dependency,
    ) {
    }
}
