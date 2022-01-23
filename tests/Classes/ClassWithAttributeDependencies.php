<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests\Classes;

use IngeniozIT\Edict\Inject;

class ClassWithAttributeDependencies
{
    public function __construct(
        #[Inject('injectedEntry')] public string $injectedParam
    ) {
    }
}
