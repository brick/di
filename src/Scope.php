<?php

declare(strict_types=1);

namespace Brick\Di;

/**
 * Defines the re-usability of a resolved definition value.
 */
interface Scope
{
    /**
     * Resolves if needed, and returns a value for the given definition.
     *
     * @param Definition $definition
     * @param Container  $container
     *
     * @return mixed
     */
    public function get(Definition $definition, Container $container);
}
