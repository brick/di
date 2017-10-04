<?php

declare(strict_types=1);

namespace Brick\Di;

/**
 * Exception thrown when a parameter/property could not be resolved.
 */
class UnresolvedValueException extends \RuntimeException
{
    /**
     * @param \ReflectionParameter $parameter
     *
     * @return UnresolvedValueException
     */
    public static function unresolvedParameter(\ReflectionParameter $parameter) : UnresolvedValueException
    {
        $message = 'The parameter "%s" from function "%s" could not be resolved';
        $message = sprintf($message, self::getParameterName($parameter), self::getFunctionName($parameter));

        return new self($message);
    }

    /**
     * @param \ReflectionProperty $property
     *
     * @return UnresolvedValueException
     */
    public static function unresolvedProperty(\ReflectionProperty $property) : UnresolvedValueException
    {
        $message = 'The property %s::$%s could not be resolved';
        $message = sprintf($message, $property->getDeclaringClass()->getName(), $property->getName());

        return new self($message);
    }

    /**
     * Returns the type (if any) + name of a function parameter.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return string
     */
    private static function getParameterName(\ReflectionParameter $parameter) : string
    {
        return self::getTypeHint($parameter) . '$' . $parameter->getName();
    }

    /**
     * Helper class for getParameterName().
     *
     * @param \ReflectionParameter $parameter
     *
     * @return string
     */
    private static function getTypeHint(\ReflectionParameter $parameter) : string
    {
        if ($parameter->isArray()) {
            return 'array ';
        }

        $class = $parameter->getClass();
        if ($class) {
            return $class->getName() . ' ';
        }

        return '';
    }

    /**
     * Returns the type (if any) + name of a function.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return string
     */
    private static function getFunctionName(\ReflectionParameter $parameter) : string
    {
        $function = $parameter->getDeclaringFunction();

        return self::getClassName($function) . $function->getName();
    }

    /**
     * Helper class for getFunctionName().
     *
     * @param \ReflectionFunctionAbstract $function
     *
     * @return string
     */
    private static function getClassName(\ReflectionFunctionAbstract $function) : string
    {
        if ($function instanceof \ReflectionMethod) {
            return $function->getDeclaringClass()->getName() . '::';
        }

        return '';
    }
}
