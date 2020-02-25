<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIT\Container\Container;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @coversDefaultClass \IngeniozIT\Container\Container
 */
class ContainerTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $container = new Container();

        $this->assertInstanceOf(Container::class, $container);
    }

    public function testThrowsExceptionWhenNoEntryWasFound(): void
    {
        $container = new Container();

        $this->expectException(NotFoundExceptionInterface::class);
        $container->get('foo');
    }

    /**
     * @param mixed $entryValue
     * @dataProvider providerBasicEntries
     */
    public function testBasicEntries($entryValue): void
    {
        $container = new Container();

        $container->set('foo', $entryValue);

        $this->assertTrue($container->has('foo'));
        $this->assertSame($entryValue, $container->get('foo'));
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
}
