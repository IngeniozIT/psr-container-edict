<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIt\Edict\{
    Container,
};
use IngeniozIt\Edict\Tests\Classes\{ClassThatCannotBeAutowired,
    ClassWithAttributesAndDefaultValues,
    ClassWithAttributeDependencies,
    ClassWithComplexDependencies,
    ClassWithDefaultValuesDependencies,
    ClassWithInterfaceDependency,
    ClassWithoutDependencies,
    ClassWithSolvableDependencies,
    ClassWithSolvableDependenciesInterface};
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @SuppressWarnings("PHPMD.StaticAccess")
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class ContainerAutowiringTest extends TestCase
{
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
        $container->set(ClassWithSolvableDependenciesInterface::class, Container::alias(ClassWithSolvableDependencies::class));

        $entry = $container->get(ClassWithComplexDependencies::class);

        self::assertInstanceOf(ClassWithComplexDependencies::class, $entry);
    }

    public function testAutowiresClassesWithDefaultValues(): void
    {
        $container = new Container();

        $entry = $container->get(ClassWithDefaultValuesDependencies::class);

        self::assertInstanceOf(ClassWithDefaultValuesDependencies::class, $entry);
    }

    public function testAttributeTakesPrecedenceOverDefaultValue(): void
    {
        $container = new Container();
        $container->set('injectedEntry', Container::value('injectedValue'));

        /** @var ClassWithAttributesAndDefaultValues $entry */
        $entry = $container->get(ClassWithAttributesAndDefaultValues::class);

        self::assertEquals('injectedValue', $entry->defaultValue);
    }

    public function testFailsWhenAutowiringANonExistantClass(): void
    {
        $container = new Container();

        $this->expectException(NotFoundExceptionInterface::class);
        $container->get('\\Class\\That\\Does\\Not\\Exist');
    }

    public function testFailsWhenAutowiringANonAutowirableClass(): void
    {
        $container = new Container();

        $this->expectException(ContainerExceptionInterface::class);
        $container->get(ClassThatCannotBeAutowired::class);
    }

    public function testFailsWhenAutowiringAClassWithMissingAttributes(): void
    {
        $container = new Container();

        $this->expectException(ContainerExceptionInterface::class);
        // ClassWithAttributeDependencies needs 'injectedEntry' to be set
        $container->get(ClassWithAttributeDependencies::class);
    }

    public function testFailsWhenAutowiringIsDisabled(): void
    {
        $container = new Container();
        $container->disableAutowiring();

        $this->expectException(ContainerExceptionInterface::class);
        $container->get(ClassWithoutDependencies::class);
    }
}
