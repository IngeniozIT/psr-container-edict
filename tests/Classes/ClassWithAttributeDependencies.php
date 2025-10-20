<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests\Classes;

use IngeniozIt\Edict\Inject;

class ClassWithAttributeDependencies
{
    public function __construct(
        #[Inject('injectedEntry')] public string $injectedParam,
    ) {
    }
}
