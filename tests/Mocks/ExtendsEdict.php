<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Tests\Mocks;

use IngeniozIT\Container\Edict;

class ExtendsEdict extends Edict
{
    public bool $called = false;

    public function get($id)
    {
        $this->called = true;
        return true;
    }
}
