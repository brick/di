<?php

namespace Brick\Di;

/**
 * Value object used in bindings to defer resolving of container keys.
 */
class Resolve
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
