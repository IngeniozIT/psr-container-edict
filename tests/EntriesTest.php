<?php

declare(strict_types=1);

namespace IngeniozIT\Edict\Tests;

use PHPUnit\Framework\TestCase;
use IngeniozIT\Edict\Container;

use function IngeniozIT\Edict\{alias, dynamic, lazyload, value,};

class EntriesTest extends TestCase
{
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
}
