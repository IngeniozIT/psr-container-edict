<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIT\Container\Edict;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use IngeniozIT\Container\Tests\Mocks\ExtendsEdict;
use IngeniozIT\Container\Tests\Mocks\BasicClass;
use IngeniozIT\Container\Tests\Mocks\SimplyWiredClass;

/**
 * @coversDefaultClass \IngeniozIT\Container\Edict
 */
class EdictTest extends TestCase
{
    public function testIsAContainer(): void
    {
        $container = new Edict();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testThrowsExceptionWhenNoEntryWasFound(): void
    {
        $container = new Edict();

        $this->expectException(NotFoundExceptionInterface::class);
        $container->get('foo');
    }

    /**
     * @param mixed $entryValue
     * @dataProvider providerBasicEntries
     */
    public function testSupportsBasicEntries($entryValue): void
    {
        $container = new Edict();

        $container->set('foo', $entryValue);

        $this->assertValidEntry($container, 'foo', $entryValue);
    }

    /**
     * @return array<array>
     */
    public function providerBasicEntries(): array
    {
        return [
            'true' => [true],
            'false' => [false],
            'int' => [42],
            'float' => [84.84],
            'string' => ["We’ll we’ll we’ll…if it isn’t autocorrect."],
            'array' => [[3 => 'i am', 1 => 'an array']],
            'object' => [(object)['foo' => 'bar']],
            'null' => [null],
            'callable' => [[$this, 'basicCallable']],
        ];
    }

    public static function basicCallable(): bool
    {
        return true;
    }

    public function testSetMultiple(): void
    {
        $container = new Edict();

        $container->setMultiple([
            'foo' => 'bar',
            'foo2' => 'bar2'
        ]);

        $this->assertValidEntry($container, 'foo', 'bar');
        $this->assertValidEntry($container, 'foo2', 'bar2');
    }

    public function testCanBindCallable(): void
    {
        $container = new Edict();

        $container->bind('foo', function (ContainerInterface $c) {
            static $i = 0;
            ++$i;
            return $i;
        });

        $this->assertValidEntry($container, 'foo', 1);
        $this->assertValidEntry($container, 'foo', 2);
    }

    public function testCanBindMultipleCallables(): void
    {
        $container = new Edict();

        $container->bindMultiple([
            'foo' => function (ContainerInterface $c) {
                static $i = 0;
                ++$i;
                return $i;
            },
            'bar' => function (ContainerInterface $c) {
                static $i = 0;
                ++$i;
                return $i;
            },
        ]);

        $this->assertValidEntry($container, 'foo', 1);
        $this->assertValidEntry($container, 'bar', 1);
        $this->assertValidEntry($container, 'foo', 2);
        $this->assertValidEntry($container, 'bar', 2);
    }

    /**
     * Checks if an entry of a container is valid.
     * @param ContainerInterface $container
     * @param string $entryId
     * @param mixed $entryValue
     */
    protected function assertValidEntry(ContainerInterface $container, string $entryId, $entryValue): void
    {
        $this->assertTrue($container->has($entryId));
        $this->assertSame($entryValue, $container->get($entryId));
    }

    public function testCanAutowireBasicClass(): void
    {
        $container = new Edict();

        $this->assertEntryInstantiatesClass($container, BasicClass::class);
    }

    public function testCanAutowireSimplyWiredClass(): void
    {
        $container = new Edict();

        $this->assertEntryInstantiatesClass($container, SimplyWiredClass::class);
    }

    /**
     * Checks if an entry of a container is valid.
     * @param ContainerInterface $container
     * @param string $className
     */
    protected function assertEntryInstantiatesClass(ContainerInterface $container, string $className): void
    {
        $this->assertEntryIsInstanceOf($container, $className, $className);
    }

    /**
     * Checks if an entry of a container is valid.
     * @param ContainerInterface $container
     * @param string $entryId
     * @param string $className
     */
    protected function assertEntryIsInstanceOf(ContainerInterface $container, string $entryId, string $className): void
    {
        $this->assertTrue($container->has($entryId));
        $this->assertInstanceOf($className, $container->get($entryId));
    }

    /**
     * By default, Edict binds itself to the PSR ContainerInterface class.
     */
    public function testIsInitializedWithContainerInterfaceClass(): void
    {
        $container = new Edict();

        $containerInterface = $container->get(ContainerInterface::class);

        $this->assertSame($container, $containerInterface);
    }

    /**
     * You can change which container will be passed to the callbacks by
     * overriding the ContainerInterface class.
     */
    public function testContainerInterfaceClassCanBeChanged(): void
    {
        $container = new Edict();
        $newContainer = new ExtendsEdict();

        $container->set(ContainerInterface::class, $newContainer);

        $containerInterface = $container->get(ContainerInterface::class);

        $this->assertSame($newContainer, $containerInterface);
    }
}
