<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Tests\Mocks;

class ClassWithDependency
{
    protected BasicClass $basicClass;

    public function __construct(BasicClass $basicClass)
    {
        $this->basicClass = $basicClass;
    }
}
