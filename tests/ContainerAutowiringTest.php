<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIt\Edict\{
    Container,
    ContainerException,
    NotFoundException,
};
use IngeniozIt\Edict\Tests\Classes\{ClassThatCannotBeAutowired,
    ClassWithAttributeDependencies,
    ClassWithComplexDependencies,
    ClassWithInterfaceDependency,
    ClassWithoutDependencies,
    ClassWithSolvableDependencies,
    ClassWithSolvableDependenciesInterface
};

class ContainerAutowiringTest extends TestCase
{
    public function testCanManuallyInstantiateAClass(): void
    {
        $container = new Container();
        $container->set(
            ClassThatCannotBeAutowired::class,
            fn() => new ClassThatCannotBeAutowired(42)
        );

        $entry = $container->get(ClassThatCannotBeAutowired::class);

        self::assertInstanceOf(ClassThatCannotBeAutowired::class, $entry);
    }

    public function testAutowiresClassesWithoutDependencies(): void
    {
        $container = new Container();

        $entry = $container->get(ClassWithoutDependencies::class);

        self::assertInstanceOf(ClassWithoutDependencies::class, $entry);
    }

    public function testAutowiresClassesWithExplicitDependencies(): void
    {
        $container = new Container();

        $entry = $container->get(ClassWithSolvableDependencies::class);

        self::assertInstanceOf(ClassWithSolvableDependencies::class, $entry);
    }

    public function testAutowiresClassesWithAttributeDependencies(): void
    {
        $container = new Container();
        // ClassWithAttributeDependencies needs 'injectedEntry' to be set
        $container->set('injectedEntry', Container::value('injectedValue'));

        $entry = $container->get(ClassWithAttributeDependencies::class);

        self::assertInstanceOf(ClassWithAttributeDependencies::class, $entry);
    }

    public function testAutowiresClassesWithInterfaceDependencies(): void
    {
        $container = new Container();
        $container->set(ClassWithSolvableDependenciesInterface::class, Container::alias(ClassWithSolvableDependencies::class));

        $entry = $container->get(ClassWithInterfaceDependency::class);

        self::assertInstanceOf(ClassWithInterfaceDependency::class, $entry);
    }

    public function testAutowiresClassesWithComplexDependencies(): void
    {
        $container = new Container();
        // ClassWithComplexDependencies needs 'injectedEntry' and
        // 'anotherInjectedEntry' to be set
        $container->set('injectedEntry', Container::value('injectedValue'));
        $container->set('anotherInjectedEntry', Container::value(42));

        $entry = $container->get(ClassWithComplexDependencies::class);

        self::assertInstanceOf(ClassWithComplexDependencies::class, $entry);
    }

    public function testFailsWhenAutowiringANonExistantClass(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);
        $container->get('\\Class\\That\\Does\\Not\\Exist');
    }

    public function testFailsWhenAutowiringANonAutowirableClass(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        $container->get(ClassThatCannotBeAutowired::class);
    }

    public function testFailsWhenAutowiringAClassWithMissingAttributes(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        // ClassWithAttributeDependencies needs 'injectedEntry' to be set
        $container->get(ClassWithAttributeDependencies::class);
    }
}
