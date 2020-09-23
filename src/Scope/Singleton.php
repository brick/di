<?php

declare(strict_types=1);

namespace Brick\DI\Scope;

use Brick\DI\Scope;
use Brick\DI\Definition;
use Brick\DI\Container;

/**
 * The definition will be resolved once, then the same result will be returned every time it is requested.
 */
class Singleton implements Scope
{
    private bool $resolved = false;

    private mixed $result;

    /**
     * {@inheritdoc}
     */
    public function get(Definition $definition, Container $container) : mixed
    {
        if (! $this->resolved) {
            $this->result   = $definition->resolve($container);
            $this->resolved = true;
        }

        return $this->result;
    }
}
