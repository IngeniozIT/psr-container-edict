<?php

declare(strict_types=1);

namespace IngeniozIT\Edict;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;

function value(mixed $value): callable
{
    return fn(): mixed => $value;
}

function alias(string $id): callable
{
    return fn(ContainerInterface $container): mixed => $container->get($id);
}

function dynamic(callable $callback): callable
{
    return fn(ContainerInterface $container): mixed => $callback($container);
}

function lazyload(callable $callback): callable
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

/**
 * @param class-string $className
 * @throws ContainerException
 * @throws ReflectionException
 */
function objectValue(string $className): callable
{
    $autowiredParameters = getAutowiredParameters($className);
    return function (ContainerInterface $container) use ($className, $autowiredParameters): mixed {
        try {
            $class = new ReflectionClass($className);
            return $class->newInstance(
                ...array_map(
                    fn(string $paramType): mixed => $container->get($paramType),
                    $autowiredParameters
                )
            );
        } catch (NotFoundException $e) {
            throw new ContainerException("Cannot autowire $className : {$e->getMessage()}");
        }
    };
}

/**
 * @param class-string $className
 * @return array<class-string|string>
 * @throws ContainerException
 * @throws ReflectionException
 */
function getAutowiredParameters(string $className): array
{
    return array_map(
        function (ReflectionParameter $param) use ($className): string {
            $injectAttribute = $param->getAttributes(Inject::class)[0] ?? null;
            if ($injectAttribute !== null) {
                return $injectAttribute->newInstance()->entryId;
            }
            $parameterClass = (string)$param->getType();
            return class_exists($parameterClass) || interface_exists($parameterClass) ?
                $parameterClass :
                throw new ContainerException("Cannot autowire parameter {$param->getName()} of $className");
        },
        (new ReflectionClass($className))->getConstructor()?->getParameters() ?? []
    );
}
