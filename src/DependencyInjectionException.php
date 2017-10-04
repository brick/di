<?php

declare(strict_types=1);

namespace Brick\Di;

/**
 * Exception thrown by the dependency injection classes.
 */
class DependencyInjectionException extends \RuntimeException
{
    /**
     * @param string $key
     *
     * @return DependencyInjectionException
     */
    public static function keyNotRegistered(string $key) : DependencyInjectionException
    {
        return new self('Key not registered: ' . $key);
    }
}
