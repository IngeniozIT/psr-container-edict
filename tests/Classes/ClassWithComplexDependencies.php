<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests\Classes;

use IngeniozIT\Edict\Inject;

class ClassWithComplexDependencies
{
    public function __construct(
        public ClassWithoutDependencies $dependency1,
        public ClassWithSolvableDependencies $dependency2,
        public ClassWithAttributeDependencies $dependency3,
        #[Inject('injectedEntry')] public string $injectedParam,
        #[Inject('anotherInjectedEntry')] public int $anotherInjectedParam,
    ) {
    }
}
