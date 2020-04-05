<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Tests\Mocks;

class NotAutowirableClass
{
    /** @var mixed */
    protected $id;

    /**
     * Constructor.
     * @param mixed $id Parameter with no specific type.
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
}
