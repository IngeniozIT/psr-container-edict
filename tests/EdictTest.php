<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIT\Container\Edict;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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

    /**
     * Asserts that an entry of a container is valid.
     * @param ContainerInterface $container
     * @param string $entryId
     * @param mixed $entryValue
     */
    protected function assertValidEntry(ContainerInterface $container, string $entryId, $entryValue): void
    {
        $this->assertTrue($container->has($entryId));
        $this->assertSame($entryValue, $container->get($entryId));
    }
}
