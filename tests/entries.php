<?php

use IngeniozIT\Edict\Tests\Classes\ClassWithoutDependencies;

use function IngeniozIT\Edict\{
    value,
    alias,
    dynamic,
    lazyload,
};

return [
    'entry1' => value(42),
    'entry2' => alias(ClassWithoutDependencies::class),
    'entry3' => dynamic(fn() => new ClassWithoutDependencies()),
    'entry4' => lazyload(fn() => new ClassWithoutDependencies()),
    'entry5' => fn() => 42,
];
