<?php

declare(strict_types=1);

namespace Brick\DI;

/**
 * Exception thrown by the dependency injection classes.
 */
class DIException extends \RuntimeException
{
    public static function keyNotRegistered(string $key) : DIException
    {
        if (class_exists($key)) {
            $message = 'The class "' . $key . '" is not marked as injected, and not registered with the container.';
        } else {
            $message = 'The key "' . $key . '" is not registered with the container.';
        }

        return new self($message);
    }
}
