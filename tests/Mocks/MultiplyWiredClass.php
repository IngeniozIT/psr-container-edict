<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Tests\Mocks;

class MultiplyWiredClass
{
    protected BasicClass $basicClass;
    protected SimplyWiredClass $simplyWiredClass;
    protected SimplyWiredClass $simplyWiredClass2;

    public function __construct(
        BasicClass $basicClass,
        SimplyWiredClass $simplyWiredClass,
        SimplyWiredClass $simplyWiredClass2
    ) {
        $this->basicClass = $basicClass;
        $this->simplyWiredClass = $simplyWiredClass;
        $this->simplyWiredClass2 = $simplyWiredClass2;
    }
}
