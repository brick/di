<?php

declare(strict_types=1);

namespace Brick\Di\ValueResolver;

use Brick\Di\ValueResolver;
use Brick\Di\Container;
use Brick\Reflection\ReflectionTools;

/**
 * This class is internal to the dependency injection Container.
 */
class ContainerValueResolver implements ValueResolver
{
    /**
     * @var \Brick\Di\Container
     */
    private $container;

    /**
     * @var \Brick\Di\InjectionPolicy
     */
    private $injectionPolicy;

    /**
     * @var \Brick\Di\ValueResolver\DefaultValueResolver
     */
    private $defaultValueResolver;

    /**
     * @var \Brick\Reflection\ReflectionTools
     */
    private $reflectionTools;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container            = $container;
        $this->injectionPolicy      = $container->getInjectionPolicy();
        $this->defaultValueResolver = new DefaultValueResolver();
        $this->reflectionTools      = new ReflectionTools();
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterValue(\ReflectionParameter $parameter)
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

    /**
     * {@inheritdoc}
     */
    public function getPropertyValue(\ReflectionProperty $property)
    {
        // Check if an injection key is available for this property.
        $key = $this->injectionPolicy->getPropertyKey($property);
        if ($key !== null) {
            return $this->container->get($key);
        }

        // Try to resolve the property by type.
        $className = $this->reflectionTools->getPropertyClass($property);
        if ($className !== null) {
            if ($this->container->has($className)) {
                return $this->container->get($className);
            }
        }

        return $this->defaultValueResolver->getPropertyValue($property);
    }

    /**
     * @return \ReflectionNamedType[]
     */
    private function getReflectionNamedTypes(?\ReflectionType $type) : array
    {
        if ($type instanceof \ReflectionNamedType) {
            return [$type];
        }

        if ($type instanceof \ReflectionUnionType) {
            return $type->getTypes();
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function getClassNames(?\ReflectionType $type) : array
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
