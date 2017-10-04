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
     * @param \ReflectionParameter $parameter
     *
     * @return string|array|null
     */
    public function getParameterKey(\ReflectionParameter $parameter);

    /**
     * @param \ReflectionProperty $property
     *
     * @return string|array|null
     */
    public function getPropertyKey(\ReflectionProperty $property);
}
