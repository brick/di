<?php

declare(strict_types=1);

namespace Brick\DI\Scope;

use Brick\DI\Scope;
use Brick\DI\Definition;
use Brick\DI\Container;

/**
 * The definition will be resolved every time it is requested.
 */
class Prototype implements Scope
{
    public function get(Definition $definition, Container $container) : mixed
    {
        return $definition->resolve($container);
    }
}
