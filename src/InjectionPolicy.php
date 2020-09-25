<?php

declare(strict_types=1);

namespace Brick\DI;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Decides which classes, methods & properties should be injected.
 */
interface InjectionPolicy
{
    /**
     * Should the given class be injected if it has not been registered with the container?
     *
     * If a class is declared as injected, its subclasses are considered injected as well.
     */
    public function isClassInjected(ReflectionClass $class) : bool;

    /**
     * Should the given method be injected after instantiating the class?
     */
    public function isMethodInjected(ReflectionMethod $method) : bool;

    /**
     * Should the given property be injected after instantiating the class?
     */
    public function isPropertyInjected(ReflectionProperty $property) : bool;

    /**
     * Returns the container key to use to resolve the given parameter, if any.
     *
     * If no key is returned, the parameter will be resolved by type.
     */
    public function getParameterKey(ReflectionParameter $parameter) : string|null;

    /**
     * Returns the container key to use to resolve the given property, if any.
     *
     * If no key is returned, the property will be resolved by type.
     */
    public function getPropertyKey(ReflectionProperty $property) : string|null;
}
