<?php

declare(strict_types=1);

namespace Brick\DI\InjectionPolicy;

use Brick\DI\Inject;
use Brick\DI\InjectionPolicy;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Controls injection with attributes.
 */
class AttributePolicy implements InjectionPolicy
{
    public function isClassInjected(ReflectionClass $class) : bool
    {
        return (bool) $class->getAttributes(Inject::class);
    }

    public function isMethodInjected(ReflectionMethod $method) : bool
    {
        return (bool) $method->getAttributes(Inject::class);
    }

    public function isPropertyInjected(ReflectionProperty $property) : bool
    {
        return (bool) $property->getAttributes(Inject::class);
    }

    public function getParameterKey(ReflectionParameter $parameter) : string|null
    {
        $function = $parameter->getDeclaringFunction();

        foreach ($function->getAttributes(Inject::class) as $attribute) {
            /** @var Inject $inject */
            $inject = $attribute->newInstance();

            return $inject->getValue($parameter->getName());
        }

        return null;
    }

    public function getPropertyKey(ReflectionProperty $property) : string|null
    {
        foreach ($property->getAttributes(Inject::class) as $attribute) {
            /** @var Inject $inject */
            $inject = $attribute->newInstance();

            return $inject->getSingleValue();
        }

        return null;
    }
}
