<?php

declare(strict_types=1);

namespace Brick\Di;

use Attribute;

#[Attribute]
class Inject
{
    /**
     * @var string|array<string, string>|null
     */
    private string|array|null $value;

    /**
     * @param string|array<string, string>|null $value
     */
    public function __construct(string|array|null $value = null)
    {
        $this->value = $value;
    }

    /**
     * Returns the value for the given key.
     *
     * Returns null if either:
     *
     * - no value was provided
     * - a single string value was provided
     * - an array was provided, but the given key does not exist
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getValue(string $name) : string|null
    {
        if ($this->value === null || is_string($this->value)) {
            return null;
        }

        return $this->value[$name] ?? null;
    }

    /**
     * Returns the single value passed to the attribute, if any.
     *
     * @return string|null
     */
    public function getSingleValue() : string|null
    {
        if (is_string($this->value)) {
            return $this->value;
        }

        return null;
    }
}
