<?php

declare(strict_types=1);

namespace IngeniozIT\Edict;

use Closure;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;
use Psr\Container\ContainerInterface;

function value(mixed $value): Entry
{
    return new Entry(fn() => $value);
}

function alias(string $id): Entry
{
    return new Entry(
        fn(ContainerInterface $container) => $container->get($id)
    );
}

function dynamic(callable $callback): Entry
{
    return new Entry(
        fn(ContainerInterface $container) => $callback($container)
    );
}

function lazyload(callable $callback): Entry
{
    return new Entry(
        function (ContainerInterface $container) use ($callback) {
            static $resolved = false;
            static $value = null;

            if (!$resolved) {
                $value = $callback($container);
                $resolved = true;
            }

            return $value;
        }
    );
}

function entry(Closure $callback): Entry
{
    return new Entry($callback);
}

/**
 * @param class-string $className
 * @throws ContainerException
 * @throws ReflectionException
 */
function objectValue(string $className): Entry
{
    $autowiredParameters = getAutowiredParameters($className);
    return new Entry(
        function (ContainerInterface $container) use ($className, $autowiredParameters) {
            try {
                return new $className(
                    ...array_map(
                        fn(string $paramType) => $container->get($paramType),
                        $autowiredParameters
                    )
                );
            } catch (NotFoundException $e) {
                throw new ContainerException("Cannot autowire $className : {$e->getMessage()}");
            }
        }
    );
}

/**
 * @param class-string $className
 * @return string[]
 * @throws ContainerException
 * @throws ReflectionException
 */
function getAutowiredParameters(string $className): array
{
    return array_map(
        function (ReflectionParameter $param) use ($className) {
            $injectAttribute = $param->getAttributes(Inject::class)[0] ?? null;
            if ($injectAttribute !== null) {
                return (string)$injectAttribute->newInstance();
            }
            $parameterClass = (string)$param->getType();
            return class_exists($parameterClass) ?
                $parameterClass :
                throw new ContainerException("Cannot autowire parameter {$param->getName()} of $className");
        },
        (new ReflectionClass($className))->getConstructor()?->getParameters() ?? []
    );
}
