<?php

declare(strict_types=1);

namespace Brick\Di\ValueResolver;

use Brick\Di\InjectionPolicy;
use Brick\Di\ValueResolver;
use Brick\Di\Container;
use Brick\Reflection\ReflectionTools;

/**
 * This class is internal to the dependency injection Container.
 */
class ContainerValueResolver implements ValueResolver
{
    private Container $container;

    private InjectionPolicy $injectionPolicy;

    private DefaultValueResolver $defaultValueResolver;

    private ReflectionTools $reflectionTools;

    public function __construct(Container $container)
    {
        $this->container            = $container;
        $this->injectionPolicy      = $container->getInjectionPolicy();
        $this->defaultValueResolver = new DefaultValueResolver();
        $this->reflectionTools      = new ReflectionTools();
    }

    public function getParameterValue(\ReflectionParameter $parameter) : mixed
    {
        // Check if an injection key is available for this parameter.
        $key = $this->injectionPolicy->getParameterKey($parameter);
        if ($key !== null) {
            return $this->container->get($key);
        }

        // Try to resolve the parameter by type.
        $type = $parameter->getType();
        if ($type) {
            $className = $type->getName();
            if ($this->container->has($className)) {
                return $this->container->get($className);
            }
        }

        return $this->defaultValueResolver->getParameterValue($parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyValue(\ReflectionProperty $property) : mixed
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
}
