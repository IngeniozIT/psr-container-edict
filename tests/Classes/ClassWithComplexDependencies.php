<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests\Classes;

use IngeniozIt\Edict\Inject;

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
