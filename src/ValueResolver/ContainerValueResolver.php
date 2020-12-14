<?php

declare(strict_types=1);

namespace Brick\DI\ValueResolver;

use Brick\DI\InjectionPolicy;
use Brick\DI\ValueResolver;
use Brick\DI\Container;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

/**
 * This class is internal to the dependency injection Container.
 */
class ContainerValueResolver implements ValueResolver
{
    private Container $container;

    private InjectionPolicy $injectionPolicy;

    private DefaultValueResolver $defaultValueResolver;

    public function __construct(Container $container)
    {
        $this->container            = $container;
        $this->injectionPolicy      = $container->getInjectionPolicy();
        $this->defaultValueResolver = new DefaultValueResolver();
    }

    public function getParameterValue(ReflectionParameter $parameter) : mixed
    {
        // Check if an injection key is available for this parameter.
        $key = $this->injectionPolicy->getParameterKey($parameter);

        if ($key !== null) {
            return $this->container->get($key);
        }

        // Try to resolve the parameter by type.
        $type = $parameter->getType();

        foreach ($this->getClassNames($type) as $className) {
            if ($this->container->has($className)) {
                return $this->container->get($className);
            }
        }

        return $this->defaultValueResolver->getParameterValue($parameter);
    }

    public function getPropertyValue(ReflectionProperty $property) : mixed
    {
        // Check if an injection key is available for this property.
        $key = $this->injectionPolicy->getPropertyKey($property);

        if ($key !== null) {
            return $this->container->get($key);
        }

        // Try to resolve the property by type.
        $type = $property->getType();

        foreach ($this->getClassNames($type) as $className) {
            if ($this->container->has($className)) {
                return $this->container->get($className);
            }
        }

        return $this->defaultValueResolver->getPropertyValue($property);
    }

    /**
     * @return ReflectionNamedType[]
     */
    private function getReflectionNamedTypes(ReflectionType|null $type) : array
    {
        if ($type instanceof ReflectionNamedType) {
            return [$type];
        }

        if ($type instanceof ReflectionUnionType) {
            return $type->getTypes();
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function getClassNames(ReflectionType|null $type) : array
    {
        $namedTypes = $this->getReflectionNamedTypes($type);

        $classNames = [];

        foreach ($namedTypes as $namedType) {
            if (! $namedType->isBuiltin()) {
                $classNames[] = $namedType->getName();
            }
        }

        return $classNames;
    }
}
