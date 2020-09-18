<?php

declare(strict_types=1);

namespace Brick\Di\Definition;

use Brick\Di\Definition;
use Brick\Di\Scope;
use Brick\Di\Container;

/**
 * Resolves a key by pointing to another.
 */
class AliasDefinition extends Definition
{
    private string $targetKey;

    public function __construct(string $targetKey)
    {
        $this->targetKey = $targetKey;
    }

    public function resolve(Container $container) : mixed
    {
        return $container->get($this->targetKey);
    }

    protected function getDefaultScope() : Scope
    {
        return new Scope\Prototype();
    }
}
