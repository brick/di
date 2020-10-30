<?php

declare(strict_types=1);

namespace Brick\DI;

use ReflectionFunctionAbstract;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Exception thrown when a parameter/property could not be resolved.
 */
class UnresolvedValueException extends \RuntimeException
{
    public static function unresolvedParameter(ReflectionParameter $parameter) : UnresolvedValueException
    {
        $message = 'The parameter "%s" from function "%s" could not be resolved';
        $message = sprintf($message, self::getParameterName($parameter), self::getFunctionName($parameter));

        return new self($message);
    }

    public static function unresolvedProperty(ReflectionProperty $property) : UnresolvedValueException
    {
        $message = 'The property %s::$%s could not be resolved.';
        $message = sprintf($message, $property->getDeclaringClass()->getName(), $property->getName());

        return new self($message);
    }

    /**
     * Returns the type (if any) + name of a function parameter.
     */
    private static function getParameterName(ReflectionParameter $parameter) : string
    {
        $parameterType = '';

        if (null !== $type = $parameter->getType()) {
            $parameterType = self::getReflectionTypeName($type) . ' ';
        }

        return $parameterType . '$' . $parameter->getName();
    }

    private static function getReflectionTypeName(\ReflectionType $reflectionType)
    {
        if ($reflectionType instanceof \ReflectionNamedType) {
            return $reflectionType->getName();
        }

        if ($reflectionType instanceof \ReflectionUnionType) {
            return implode('|', array_map(function(\ReflectionNamedType $type) {
                return $type->getName();
            }, $reflectionType->getTypes()));
        }

        return '';
    }

    /**
     * Returns the type (if any) + name of a function.
     */
    private static function getFunctionName(ReflectionParameter $parameter) : string
    {
        $function = $parameter->getDeclaringFunction();

        return self::getClassName($function) . $function->getName();
    }

    /**
     * Helper class for getFunctionName().
     */
    private static function getClassName(ReflectionFunctionAbstract $function) : string
    {
        if ($function instanceof \ReflectionMethod) {
            return $function->getDeclaringClass()->getName() . '::';
        }

        return '';
    }
}
