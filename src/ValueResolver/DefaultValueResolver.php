<?php

declare(strict_types=1);

namespace Brick\DI\ValueResolver;

use Brick\DI\ValueResolver;
use Brick\DI\UnresolvedValueException;

/**
 * Returns the default value of the parameter/property, if available.
 * This is more useful as a fallback, chained from inside another resolver, rather than standalone.
 */
class DefaultValueResolver implements ValueResolver
{
    public function getParameterValue(\ReflectionParameter $parameter) : mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw UnresolvedValueException::unresolvedParameter($parameter);
    }

    public function getPropertyValue(\ReflectionProperty $property) : mixed
    {
        $name = $property->getName();
        $class = $property->getDeclaringClass();
        $values = $class->getDefaultProperties(); // caution: this will return NULL for a property that has no default value declared!

        if (isset($values[$name])) {
            return $values[$name];
        }

        throw UnresolvedValueException::unresolvedProperty($property);
    }
}
