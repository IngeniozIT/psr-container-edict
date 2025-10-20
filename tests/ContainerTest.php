<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIt\Edict\{
    Container,
    ContainerException,
    NotFoundException
};
use Psr\Container\ContainerInterface;
use IngeniozIt\Edict\Tests\Classes\ClassWithoutDependencies;

class ContainerTest extends TestCase
{
    public function testIsAPsrContainer(): void
    {
        $container = new Container();

        self::assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testCanDisableAutowiring(): void
    {
        $container = new Container();

        $container->disableAutowiring();

        $this->expectException(NotFoundException::class);
        $container->get(ClassWithoutDependencies::class);
    }

    public function testCanEnableAutowiring(): void
    {
        $container = new Container();
        $container->disableAutowiring();

        $container->enableAutowiring();

        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get(ClassWithoutDependencies::class));
    }

    public function testLinksItselfToTheContainerClass(): void
    {
        $container = new Container();

        $edict = $container->get(Container::class);

        self::assertSame($container, $edict);
    }

    public function testLinksItselfToThePsrContainerInterface(): void
    {
        $container = new Container();

        $edict = $container->get(ContainerInterface::class);

        self::assertSame($container, $edict);
    }

    public function testReturnsFalseWhenAnEntryDoesNotExist(): void
    {
        $container = new Container();

        self::assertFalse($container->has('non existing entry'));
    }

    public function testFailsWhenAnEntryIsNotFound(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);
        $container->get('non existing entry');
    }

    public function testCanSetMultipleEntriesAtOnce(): void
    {
        $container = new Container();

        $container->setMany([
            'entry1' => Container::value(42),
            'entry2' => Container::alias(ClassWithoutDependencies::class),
            'entry3' => Container::dynamic(fn() => new ClassWithoutDependencies()),
            'entry4' => Container::lazyload(fn() => new ClassWithoutDependencies()),
            'entry5' => fn() => 42,
        ]);

        self::assertEquals(42, $container->get('entry1'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry2'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry3'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry4'));
        self::assertEquals(42, $container->get('entry5'));
    }

    public function testCanSetEntriesFromAFile(): void
    {
        $container = new Container();

        $container->setFromFile(__DIR__ . '/Files/entries.php');

        self::assertEquals(42, $container->get('entry1'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry2'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry3'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry4'));
        self::assertEquals(42, $container->get('entry5'));
    }

    public function testFailsWhenTheFileDoesNotExist(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        $container->setFromFile(__DIR__ . '/does-not-exist.php');
    }
}
