<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIT\Edict\{
    Container,
    ContainerException,
    NotFoundException,
};
use IngeniozIT\Edict\Tests\Classes\{
    ClassThatCannotBeAutowired,
    ClassWithAttributeDependencies,
    ClassWithComplexDependencies,
    ClassWithoutDependencies,
    ClassWithSolvableDependencies,
};

use function IngeniozIT\Edict\value;

class AutowireTest extends TestCase
{
    public function testCanManuallyInstantiateNonAutowirableClass(): void
    {
        $container = new Container();
        $container->set(
            ClassThatCannotBeAutowired::class,
            fn() => new ClassThatCannotBeAutowired(42)
        );

        $entry = $container->get(ClassThatCannotBeAutowired::class);

        self::assertInstanceOf(ClassThatCannotBeAutowired::class, $entry);
    }

    public function testAutowiresClassWithoutDependencies(): void
    {
        $container = new Container();

        $entry = $container->get(ClassWithoutDependencies::class);

        self::assertInstanceOf(ClassWithoutDependencies::class, $entry);
    }

    public function testAutowiresClassWithExplicitDependency(): void
    {
        $container = new Container();

        $entry = $container->get(ClassWithSolvableDependencies::class);

        self::assertInstanceOf(ClassWithSolvableDependencies::class, $entry);
    }

    public function testAutowiresClassWithAttributeDependency(): void
    {
        $container = new Container();
        // ClassWithAttributeDependencies needs 'injectedEntry' to be set
        $container->set('injectedEntry', value('injectedValue'));

        $entry = $container->get(ClassWithAttributeDependencies::class);

        self::assertInstanceOf(ClassWithAttributeDependencies::class, $entry);
    }

    public function testAutowiresClassWithComplexDependencies(): void
    {
        $container = new Container();
        // ClassWithComplexDependencies needs 'injectedEntry' and
        // 'anotherInjectedEntry' to be set
        $container->set('injectedEntry', value('injectedValue'));
        $container->set('anotherInjectedEntry', value(42));

        $entry = $container->get(ClassWithComplexDependencies::class);

        self::assertInstanceOf(ClassWithComplexDependencies::class, $entry);
    }

    public function testThrowsExceptionWhenAutowiringInexistantClass(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);
        $container->get('\\Class\\That\\Does\\Not\\Exist');
    }

    public function testThrowsExceptionWhenAutowiringNonAutowirableClass(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        $container->get(ClassThatCannotBeAutowired::class);
    }

    public function testThrowsExceptionWhenAutowiringClassWithMissingAttribute(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        // ClassWithAttributeDependencies needs 'injectedEntry' to be set
        $container->get(ClassWithAttributeDependencies::class);
    }
}
