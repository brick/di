<?php

declare(strict_types=1);

namespace Brick\Di;

use Brick\Reflection\ReflectionTools;

/**
 * Instantiates classes, injects dependencies in objects and invokes functions by autowiring.
 */
class Injector
{
    /**
     * @var \Brick\Di\InjectionPolicy
     */
    private $policy;

    /**
     * @var \Brick\Di\ValueResolver
     */
    private $resolver;

    /**
     * @var \Brick\Reflection\ReflectionTools
     */
    private $reflectionTools;

    /**
     * @param ValueResolver   $resolver
     * @param InjectionPolicy $policy
     */
    public function __construct(ValueResolver $resolver, InjectionPolicy $policy)
    {
        $this->policy = $policy;
        $this->resolver = $resolver;
        $this->reflectionTools = new ReflectionTools();
    }

    /**
     * Invokes a function after resolving its parameters.
     *
     * If the given parameters match the function parameter names, the given values are used.
     * Otherwise, the function parameters are resolved using the ValueResolver.
     *
     * @param callable $function   The function to invoke.
     * @param array    $parameters An associative array of values for the function parameters.
     *
     * @return mixed The result of the function call.
     *
     * @throws UnresolvedValueException If a function parameter could not be resolved.
     */
    public function invoke(callable $function, array $parameters = [])
    {
        $reflection = $this->reflectionTools->getReflectionFunction($function);
        $parameters = $this->getFunctionParameters($reflection, $parameters);

        return call_user_func_array($function, $parameters);
    }

    /**
     * Instantiates a class by resolving its constructor parameters, and injects dependencies in the resulting object.
     *
     * If the given parameters match the constructor parameter names, the given values are used.
     * Otherwise, the constructor parameters are resolved using the ValueResolver.
     *
     * @param string $class      The name of the class to instantiate.
     * @param array  $parameters An associative array of values for the constructor parameters.
     *
     * @return object The instantiated object.
     *
     * @throws UnresolvedValueException If a function parameter could not be resolved.
     */
    public function instantiate(string $class, array $parameters = [])
    {
        $class = new \ReflectionClass($class);
        $instance = $class->newInstanceWithoutConstructor();

        $this->inject($instance);

        $constructor = $class->getConstructor();

        if ($constructor) {
            $parameters = $this->getFunctionParameters($constructor, $parameters);
            $constructor->setAccessible(true);
            $constructor->invokeArgs($instance, $parameters);
        }

        return $instance;
    }

    /**
     * Injects dependencies in an object.
     *
     * Properties are injected first, then methods.
     *
     * @param object $object The object to inject dependencies in.
     *
     * @return void
     */
    public function inject($object) : void
    {
        $reflection = new \ReflectionObject($object);

        $this->injectProperties($reflection, $object);
        $this->injectMethods($reflection, $object);
    }

    /**
     * @param \ReflectionClass $class
     * @param object           $object
     *
     * @return void
     */
    private function injectMethods(\ReflectionClass $class, $object) : void
    {
        foreach ($this->reflectionTools->getClassMethods($class) as $method) {
            if ($this->policy->isMethodInjected($method)) {
                $parameters = $this->getFunctionParameters($method);
                $method->setAccessible(true);
                $method->invokeArgs($object, $parameters);
            }
        }
    }

    /**
     * @param \ReflectionClass $class
     * @param object           $object
     *
     * @return void
     */
    private function injectProperties(\ReflectionClass $class, $object) : void
    {
        foreach ($this->reflectionTools->getClassProperties($class) as $property) {
            if ($this->policy->isPropertyInjected($property)) {
                $value = $this->resolver->getPropertyValue($property);
                $property->setAccessible(true);
                $property->setValue($object, $value);
            }
        }
    }

    /**
     * Returns an associative array of parameters to call a given function.
     *
     * The parameters are indexed by name, and returned in the same order as they are defined.
     *
     * @param \ReflectionFunctionAbstract $function   The reflection of the function.
     * @param array                       $parameters An optional array of parameters indexed by name.
     *
     * @return array The parameters to call the function with.
     *
     * @throws UnresolvedValueException If a function parameter could not be resolved.
     */
    private function getFunctionParameters(\ReflectionFunctionAbstract $function, array $parameters = []) : array
    {
        $result = [];

        foreach ($function->getParameters() as $parameter) {
            $name = $parameter->getName();
            $value = $parameters[$name] ?? $this->resolver->getParameterValue($parameter);

            if ($parameter->isVariadic()) {
                $result = array_merge($result, $value);
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }
}
