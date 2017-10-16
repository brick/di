<?php

namespace Brick\Di;

/**
 * Value object used in bindings to reference a container key.
 */
class Ref
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }
}
