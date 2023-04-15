<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIT\Edict\{
    Container,
    ContainerException,
    NotFoundException
};
use Psr\Container\ContainerInterface;
use IngeniozIT\Edict\Tests\Classes\ClassWithoutDependencies;

use function IngeniozIT\Edict\{
    alias,
    dynamic,
    lazyload,
    value
};

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

        $container->setFromFile(__DIR__ . '/entries.php');

        self::assertEquals(42, $container->get('entry1'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry2'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry3'));
        self::assertInstanceOf(ClassWithoutDependencies::class, $container->get('entry4'));
        self::assertEquals(42, $container->get('entry5'));
    }

    public function testThrowsExceptionWhenFileDoesNotExist(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        $container->setFromFile(__DIR__ . '/does-not-exist.php');
    }
}
