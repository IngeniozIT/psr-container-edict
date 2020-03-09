<?php

declare(strict_types=1);

namespace IngeniozIT\Container\Entry;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use IngeniozIT\Container\NotFoundException;

class ClassEntry implements EdictEntryInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var string */
    protected $className;

    /** @var string[] */
    protected $params = [];

    public function __construct(ContainerInterface $container, string $className)
    {
        $this->container = $container;
        $this->className = $className;
        $this->params = self::getConstructorParams($className);
    }

    /**
     * Get a class constructor parameters
     * @param  string $className
     *
     * @return string[] $parameters
     */
    protected static function getConstructorParams(string $className): array
    {
        $constructorParams = [];

        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        return $constructor !== null ?
            array_map([self::class, 'getParameterType'], $constructor->getParameters()) :
            [];
    }

    /**
     * Get a ReflectionParameter's data as used by Edict.
     * @param  ReflectionParameter $param
     * @suppress PhanUndeclaredMethod ReflectionType::getName is not documented
     * yet.
     */
    protected static function getParameterType(ReflectionParameter $param): string
    {
        $type = $param->getType();

        if ($type === null) {
            throw new NotFoundException("Parameter {$param->getName()} has not type. Autowiring is impossible.");
        }

        return $type->getName();
    }

    /**
     * Resolves the entry.
     * @return mixed
     */
    public function resolve()
    {
        return new $this->className(...$this->resolveParameters());
    }

    /**
     * Resolves a set of parameters by their type.
     * @return mixed[]
     */
    protected function resolveParameters(): array
    {
        return array_map(fn($param) => $this->container->get($param), $this->params);
    }
}
