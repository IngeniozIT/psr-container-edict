<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests\Classes;

use IngeniozIt\Edict\Inject;

class ClassWithAttributesAndDefaultValues implements ClassWithSolvableDependenciesInterface
{
    public function __construct(
        #[Inject('injectedEntry')] public string $defaultValue = 'a dependency',
    ) {
    }
}
