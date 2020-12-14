<?php

namespace Brick\DI;

/**
 * Value object used in bindings to reference a container key.
 */
class Ref
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getKey() : string
    {
        return $this->key;
    }
}
