<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Tests\Mocks;

class ClassWithMultipleDependencies
{
    protected BasicClass $basicClass;
    protected ClassWithDependency $classWithDependency;
    protected ClassWithDependency $classWithDependency2;

    public function __construct(
        BasicClass $basicClass,
        ClassWithDependency $classWithDependency,
        ClassWithDependency $classWithDependency2
    ) {
        $this->basicClass = $basicClass;
        $this->classWithDependency = $classWithDependency;
        $this->classWithDependency2 = $classWithDependency2;
    }
}
