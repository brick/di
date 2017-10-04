<?php

declare(strict_types=1);

namespace Brick\Di\InjectionPolicy;

use Brick\Di\InjectionPolicy;

/**
 * Prevents injection of any class, method or property.
 */
class NullPolicy implements InjectionPolicy
{
    /**
     * {@inheritdoc}
     */
    public function isClassInjected(\ReflectionClass $class) : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isMethodInjected(\ReflectionMethod $method) : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropertyInjected(\ReflectionProperty $property) : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterKey(\ReflectionParameter $parameter)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyKey(\ReflectionProperty $property)
    {
        return null;
    }
}
