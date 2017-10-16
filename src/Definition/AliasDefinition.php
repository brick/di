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
    /**
     * @var string
     */
    private $targetKey;

    /**
     * @param string $targetKey
     */
    public function __construct(string $targetKey)
    {
        $this->targetKey = $targetKey;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Container $container)
    {
        return $container->get($this->targetKey);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultScope() : Scope
    {
        return Scope::prototype();
    }
}
