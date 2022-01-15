<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests\Classes;

use IngeniozIT\Edict\Inject;

class ClassWithComplexDependencies
{
    public function __construct(
        public ClassWithoutDependencies $noDependencies,
        public ClassWithSolvableDependencies $solvableDependencies,
        public ClassWithAttributeDependencies $attributeDependencies,
        #[Inject('injectedEntry')] public string $injectedParam,
        #[Inject('anotherInjectedEntry')] public int $anotherInjectedParam,
    ) {
    }
}
