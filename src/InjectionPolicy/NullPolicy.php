<?php

declare(strict_types=1);

namespace Brick\DI\InjectionPolicy;

use Brick\DI\InjectionPolicy;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Prevents injection of any class, method or property.
 */
class NullPolicy implements InjectionPolicy
{
    public function isClassInjected(ReflectionClass $class) : bool
    {
        return false;
    }

    public function isMethodInjected(ReflectionMethod $method) : bool
    {
        return false;
    }

    public function isPropertyInjected(ReflectionProperty $property) : bool
    {
        return false;
    }

    public function getParameterKey(ReflectionParameter $parameter) : string|null
    {
        return null;
    }

    public function getPropertyKey(ReflectionProperty $property) : string|null
    {
        return null;
    }
}
