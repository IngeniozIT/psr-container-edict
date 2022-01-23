<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use IngeniozIT\Edict\{
    Container,
    ContainerException,
    NotFoundException,
};
use IngeniozIT\Edict\Tests\Classes\{
    ClassThatCannotBeAutowired,
    ClassWithAttributeDependencies,
    ClassWithComplexDependencies,
    ClassWithSolvableDependencies,
    ClassWithoutDependencies,
};

use function IngeniozIT\Edict\{
    value,
    alias,
    dynamic,
    lazyload,
    entry,
};

class ContainerTest extends TestCase
{
    public function testIsAPsrContainer(): void
    {
        $container = new Container();

        self::assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testResolvesPlainValueEntries(): void
    {
        $entryId = 'entry id';
        $expectedValue = 'entry value';
        $container = new Container();

        $container->set($entryId, value($expectedValue));

        self::assertTrue($container->has($entryId));
        self::assertEquals($expectedValue, $container->get($entryId));
    }

    public function testResolvesAliasEntries(): void
    {
        $entryId = 'entry id';
        $expectedValue = 'entry value';
        $aliasId = 'alias id';

        $container = new Container();

        $container->set($entryId, value($expectedValue));
        $container->set($aliasId, alias($entryId));

        self::assertTrue($container->has($aliasId));
        self::assertEquals($expectedValue, $container->get($aliasId));
    }

    public function testResolvesDynamicEntries(): void
    {
        $entryId = 'entry id';
        $expectedValue = 42;
        $entryValue = fn() => $expectedValue;
        $container = new Container();

        $container->set($entryId, dynamic($entryValue));

        self::assertTrue($container->has($entryId));
        self::assertEquals($expectedValue, $container->get($entryId));
    }

    public function testDynamicEntriesAreResolvedMultipleTimes(): void
    {
        $entryId = 'entry id';
        $firstExpectedValue = 0;
        $secondExpectedValue = 1;
        $entryValue = function () {
            static $returnValue = 0;
            return $returnValue++;
        };
        $container = new Container();

        $container->set($entryId, dynamic($entryValue));

        self::assertEquals($firstExpectedValue, $container->get($entryId));
        self::assertEquals($secondExpectedValue, $container->get($entryId));
    }

    public function testResolvesLazyLoadedEntries(): void
    {
        $entryId = 'entry id';
        $expectedValue = 42;
        $entryValue = fn() => $expectedValue;
        $container = new Container();

        $container->set($entryId, lazyload($entryValue));

        self::assertTrue($container->has($entryId));
        self::assertEquals($expectedValue, $container->get($entryId));
    }

    public function testLazyLoadedEntriesAreResolvedOnce(): void
    {
        $entryId = 'entry id';
        $expectedValue = 0;
        $entryValue = function () {
            static $returnValue = 0;
            return $returnValue++;
        };
        $container = new Container();

        $container->set($entryId, lazyload($entryValue));

        self::assertEquals($expectedValue, $container->get($entryId));
        self::assertEquals($expectedValue, $container->get($entryId));
    }

    public function testCanManuallyInstantiateNonAutowirableClass(): void
    {
        $container = new Container();
        $container->set(
            ClassThatCannotBeAutowired::class,
            entry(fn() => new ClassThatCannotBeAutowired(42))
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

    public function testReturnsFalseWhenEntryDoesNotExist(): void
    {
        $container = new Container();

        self::assertFalse($container->has('non existing entry'));
    }

    public function testThrowsExceptionWhenEntryIsNotFound(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);
        $container->get('non existing entry');
    }

    public function testCanSetMultipleEntriesAtOnce(): void
    {
        $container = new Container();

        $container->setMany([
            'entry1' => value(42),
            'entry2' => alias(ClassWithoutDependencies::class),
            'entry3' => dynamic(fn() => new ClassWithoutDependencies()),
            'entry4' => lazyload(fn() => new ClassWithoutDependencies()),
            'entry5' => entry(fn() => 42),
        ]);

        self::assertEquals(42, $container->get('entry1'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry2'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry3'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry4'));
        self::assertEquals(42, $container->get('entry5'));
    }

    public function testCanDisableAutowiring(): void
    {
        $container = new Container();

        $container->disableAutowiring();

        $this->expectException(NotFoundException::class);
        $container->get(ClassWithoutDependencies::class);
    }

    public function testCanReenableAutowiring(): void
    {
        $container = new Container();
        $container->disableAutowiring();

        $container->enableAutowiring();

        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get(ClassWithoutDependencies::class));
    }

    public function testLinksItselfToContainerClass(): void
    {
        $container = new Container();

        $edict = $container->get(Container::class);

        self::assertSame($container, $edict);
    }

    public function testLinksItselfToPsr11ContainerInterface(): void
    {
        $container = new Container();

        $edict = $container->get(ContainerInterface::class);

        self::assertSame($container, $edict);
    }
}
