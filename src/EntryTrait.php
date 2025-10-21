<?php

declare(strict_types=1);

namespace IngeniozIt\Edict;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;

trait EntryTrait
{
    /**
     * An entry that will be evaluated once
     */
    public static function value(mixed $value): callable
    {
        return fn(): mixed => $value;
    }

    /**
     * An entry that points to another entry
     */
    public static function alias(string $entry): callable
    {
        return fn(ContainerInterface $container): mixed => $container->get($entry);
    }

    /**
     * An entry that will be evaluated every time it is called
     */
    public static function dynamic(callable $callback): callable
    {
        return fn(ContainerInterface $container): mixed => $callback($container);
    }

    /**
     * An entry that will be evaluated the first time it is called
     */
    public static function lazyload(callable $callback): callable
    {
        return function (ContainerInterface $container) use ($callback): mixed {
            static $resolved = false;
            static $value = null;

            if (!$resolved) {
                /** @var mixed $value */
                $value = $callback($container);
                $resolved = true;
            }

            return $value;
        };
    }

    private static function autowire(string $className): callable
    {
        if (!class_exists($className)) {
            throw new NotFoundException("Class $className cannot be autowired : it does not exist.");
        }

        $parametersEntries = self::getConstructorEntries($className);

        return function (ContainerInterface $container) use ($className, $parametersEntries): mixed {
            return new $className(...array_map(
                fn(callable $parameterCallable): mixed => $parameterCallable($container),
                $parametersEntries
            ));
        };
    }

    /**
     * @param class-string $className
     * @return callable[]
     */
    private static function getConstructorEntries(string $className): array
    {
        $parameters = (new ReflectionClass($className))->getConstructor()?->getParameters() ?? [];

        return array_map(
            self::getEntryFromParameter(...),
            $parameters,
        );
    }

    private static function getEntryFromParameter(ReflectionParameter $parameter): callable
    {
        $entryName = self::getEntryNameFromParameter($parameter);

        return $parameter->isDefaultValueAvailable() ?
            fn(ContainerInterface $container): mixed => (
                $container->has($entryName) ?
                    $container->get($entryName) :
                    $parameter->getDefaultValue()
            ) :
            self::alias($entryName);
    }

    private static function getEntryNameFromParameter(ReflectionParameter $parameter): string
    {
        $injectAttribute = $parameter->getAttributes(Inject::class)[0] ?? null;

        return $injectAttribute !== null ?
            $injectAttribute->newInstance()->entryId :
            (string)$parameter->getType();
    }
}
