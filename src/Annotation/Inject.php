<?php

declare(strict_types=1);

namespace Brick\Di\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 */
class Inject
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Returns the value for the given key, or null if the given key is not provided.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getValue(string $name) : ?string
    {
        return $this->values[$name] ?? null;
    }

    /**
     * Returns the single value passed to the annotation, if any.
     *
     * @return string|null
     */
    public function getSingleValue() : ?string
    {
        return $this->values['value'] ?? null;
    }
}
