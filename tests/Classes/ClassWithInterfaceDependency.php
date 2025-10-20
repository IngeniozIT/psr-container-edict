<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests\Classes;

class ClassWithInterfaceDependency
{
    public function __construct(
        public ClassWithSolvableDependenciesInterface $dependency,
    ) {
    }
}
