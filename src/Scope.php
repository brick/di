<?php

declare(strict_types=1);

namespace Brick\DI;

/**
 * Defines the re-usability of a resolved definition value.
 */
interface Scope
{
    /**
     * Resolves if needed, and returns a value for the given definition.
     */
    public function get(Definition $definition, Container $container) : mixed;
}
