<?php

declare(strict_types=1);

namespace Brick\Di;

/**
 * Defines the re-usability of a resolved definition value.
 */
abstract class Scope
{
    /**
     * @return Scope\Singleton
     */
    public static function singleton() : Scope\Singleton
    {
        return new Scope\Singleton();
    }

    /**
     * @return Scope\Prototype
     */
    public static function prototype() : Scope\Prototype
    {
        return new Scope\Prototype();
    }

    /**
     * Resolves if needed, and returns a value for the given definition.
     *
     * @param Definition $definition
     * @param Container  $container
     *
     * @return mixed
     */
    abstract public function get(Definition $definition, Container $container);
}
