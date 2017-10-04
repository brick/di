<?php

declare(strict_types=1);

namespace Brick\Di\ValueResolver;

use Brick\Di\ValueResolver;

/**
 * Resolves parameters/properties whose names match the ones in an associative array.
 */
class ArrayValueResolver implements ValueResolver
{
    /**
     * @var ValueResolver
     */
    private $fallback;

    /**
     * @var array
     */
    private $values = [];

    /**
     * Class constructor.
     *
     * @param ValueResolver $fallback The ValueResolver to fall back to if the value isn't resolved.
     */
    public function __construct(ValueResolver $fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     * @param array $values
     *
     * @return void
     */
    public function setValues(array $values) : void
    {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterValue(\ReflectionParameter $parameter)
    {
        $name = $parameter->getName();

        if (isset($this->values[$name])) {
            return $this->values[$name];
        }

        return $this->fallback->getParameterValue($parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyValue(\ReflectionProperty $property)
    {
        $name = $property->getName();

        if (isset($this->values[$name])) {
            return $this->values[$name];
        }

        return $this->fallback->getPropertyValue($property);
    }
}
