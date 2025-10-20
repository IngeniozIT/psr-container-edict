<?php

declare(strict_types=1);

namespace IngeniozIt\Edict\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIt\Edict\Container;

class ContainerEntriesTest extends TestCase
{
    public function testResolvesPlainValueEntries(): void
    {
        $entryId = 'entry id';
        $expectedValue = 'entry value';
        $container = new Container();

        $container->set($entryId, Container::value($expectedValue));

        self::assertTrue($container->has($entryId));
        self::assertEquals($expectedValue, $container->get($entryId));
    }

    public function testResolvesAliasEntries(): void
    {
        $entryId = 'entry id';
        $expectedValue = 'entry value';
        $aliasId = 'alias id';

        $container = new Container();

        $container->set($entryId, Container::value($expectedValue));
        $container->set($aliasId, Container::alias($entryId));

        self::assertTrue($container->has($aliasId));
        self::assertEquals($expectedValue, $container->get($aliasId));
    }

    public function testResolvesDynamicEntries(): void
    {
        $entryId = 'entry id';
        $expectedValue = 42;
        $entryValue = fn() => $expectedValue;
        $container = new Container();

        $container->set($entryId, Container::dynamic($entryValue));

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

        $container->set($entryId, Container::dynamic($entryValue));

        self::assertEquals($firstExpectedValue, $container->get($entryId));
        self::assertEquals($secondExpectedValue, $container->get($entryId));
    }

    public function testResolvesLazyLoadedEntries(): void
    {
        $entryId = 'entry id';
        $expectedValue = 42;
        $entryValue = fn() => $expectedValue;
        $container = new Container();

        $container->set($entryId, Container::lazyload($entryValue));

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

        $container->set($entryId, Container::lazyload($entryValue));

        self::assertEquals($expectedValue, $container->get($entryId));
        self::assertEquals($expectedValue, $container->get($entryId));
    }
}
