<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests\Classes;

class ClassWithDefaultValuesDependencies implements ClassWithSolvableDependenciesInterface
{
    public function __construct(
        public string $defaultValue = 'a dependency',
    ) {
    }
}
