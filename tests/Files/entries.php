<?php

use IngeniozIt\Edict\Container;
use IngeniozIt\Edict\Tests\Classes\ClassWithoutDependencies;

return [
    'entry1' => Container::value(42),
    'entry2' => Container::alias(ClassWithoutDependencies::class),
    'entry3' => Container::dynamic(fn() => new ClassWithoutDependencies()),
    'entry4' => Container::lazyload(fn() => new ClassWithoutDependencies()),
    'entry5' => fn() => 42,
];
