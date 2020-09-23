<?php

declare(strict_types=1);

namespace Brick\Di\InjectionPolicy;

use Brick\Di\Inject;
use Brick\Di\InjectionPolicy;

/**
 * Controls injection with attributes.
 */
class AttributePolicy implements InjectionPolicy
{
    /**
     * {@inheritdoc}
     */
    public function isClassInjected(\ReflectionClass $class) : bool
    {
        return (bool) $class->getAttributes(Inject::class);
    }

    /**
     * {@inheritdoc}
     */
    public function isMethodInjected(\ReflectionMethod $method) : bool
    {
        return (bool) $method->getAttributes(Inject::class);
    }

    /**
     * {@inheritdoc}
     */
    public function isPropertyInjected(\ReflectionProperty $property) : bool
    {
        return (bool) $property->getAttributes(Inject::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterKey(\ReflectionParameter $parameter) : string|null
    {
        $function = $parameter->getDeclaringFunction();

        foreach ($function->getAttributes(Inject::class) as $attribute) {
            /** @var Inject $inject */
            $inject = $attribute->newInstance();

            return $inject->getValue($parameter->getName());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyKey(\ReflectionProperty $property) : string|null
    {
        foreach ($property->getAttributes(Inject::class) as $attribute) {
            /** @var Inject $inject */
            $inject = $attribute->newInstance();

            return $inject->getSingleValue();
        }

        return null;
    }
}
