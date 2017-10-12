<?php

declare(strict_types=1);

namespace Brick\Di;

/**
 * Decides which classes, methods & properties should be injected.
 */
interface InjectionPolicy
{
    /**
     * Should the given class be injected if it has not been registered with the container?
     *
     * If a class is declared as injected, its subclasses are considered injected as well.
     *
     * @param \ReflectionClass $class
     *
     * @return bool
     */
    public function isClassInjected(\ReflectionClass $class) : bool;

    /**
     * Should the given method be injected after instantiating the class?
     *
     * @param \ReflectionMethod $method
     *
     * @return bool
     */
    public function isMethodInjected(\ReflectionMethod $method) : bool;

    /**
     * Should the given property be injected after instantiating the class?
     *
     * @param \ReflectionProperty $property
     *
     * @return bool
     */
    public function isPropertyInjected(\ReflectionProperty $property) : bool;

    /**
     * Returns the container key to use to resolve the given parameter, if any.
     *
     * If no key is returned, the parameter will be resolved by type.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return string|array|null The key, or null.
     */
    public function getParameterKey(\ReflectionParameter $parameter);

    /**
     * Returns the container key to use to resolve the given property, if any.
     *
     * If no key is returned, the property will be resolved by type.
     *
     * @param \ReflectionProperty $property
     *
     * @return string|array|null The key, or null.
     */
    public function getPropertyKey(\ReflectionProperty $property);
}
