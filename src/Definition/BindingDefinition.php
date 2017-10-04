<?php

declare(strict_types=1);

namespace Brick\Di\Definition;

use Brick\Di\Definition;
use Brick\Di\Scope;
use Brick\Di\Container;

/**
 * Resolves a class name.
 */
class BindingDefinition extends Definition
{
    /**
     * The class name to instantiate, or a closure to invoke.
     *
     * @var \Closure|string
     */
    private $target;

    /**
     * @var array
     */
    private $withParameters = [];

    /**
     * @var array
     */
    private $usingParameters = [];

    /**
     * Class constructor.
     *
     * @param \Closure|string $target The class name to instantiate, or a closure to invoke.
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * @param \Closure|string $target
     *
     * @return BindingDefinition
     */
    public function to($target) : BindingDefinition
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Sets an associative array of parameters to resolve the binding.
     *
     * Unlike using(), values can be of any type and will be used as is.
     *
     * Note that if with() and using() keys conflict, using() takes precedence.
     *
     * @param array $parameters
     *
     * @return BindingDefinition
     */
    public function with(array $parameters) : BindingDefinition
    {
        $this->withParameters = $parameters;

        return $this;
    }

    /**
     * Sets an associative array of parameters mapping to container values to resolve the binding.
     *
     * Unlike with(), values are keys that will be resolved by the container at injection time.
     * Values can be either strings, or nested arrays of strings.
     *
     * Note that if with() and using() keys conflict, using() takes precedence.
     *
     * @param array $parameters
     *
     * @return BindingDefinition
     */
    public function using(array $parameters) : BindingDefinition
    {
        $this->usingParameters = $parameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Container $container)
    {
        $parameters = $container->get($this->usingParameters);
        $parameters = $parameters + $this->withParameters;

        if ($this->target instanceof \Closure) {
            return $container->getInjector()->invoke($this->target, $parameters);
        }

        return $container->getInjector()->instantiate($this->target, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultScope() : Scope
    {
        return Scope::singleton();
    }
}
