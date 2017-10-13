<?php

declare(strict_types=1);

namespace Brick\Di\Definition;

use Brick\Di\Definition;
use Brick\Di\Resolve;
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
    private $parameters = [];

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
     * These will be used to resolve the parameters of the bound closure, or the constructor parameters of the bound
     * class. The array keys must match the closure/constructor parameter names.
     *
     * Parameters can include references to other container keys by wrapping keys in a Resolve object.
     * Resolve objects deeply nested in arrays will also be resolved:
     *
     *     $container->bind(MyService::class)->with([
     *       'username' => 'admin'
     *       'password' => new Resolve('myservice.password'),
     *       'options' => [
     *         'timeout' => new Resolve('myservice.timeout'),
     *       ]
     *     ]);
     *
     * Using `new Resolve()` is conceptually equivalent to calling `$container->get()`, but with Resolve,
     * the values are resolved just-in-time, when the object is requested.
     *
     * @param array $parameters
     *
     * @return BindingDefinition
     */
    public function with(array $parameters) : BindingDefinition
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Container $container)
    {
        $parameters = $this->getParameters($container, $this->parameters);

        if ($this->target instanceof \Closure) {
            return $container->getInjector()->invoke($this->target, $parameters);
        }

        return $container->getInjector()->instantiate($this->target, $parameters);
    }

    /**
     * @param Container $container
     * @param array     $parameters
     *
     * @return array
     */
    private function getParameters(Container $container, array $parameters) : array
    {
        $result = [];

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->getParameters($container, $value);
            } elseif ($value instanceof Resolve) {
                $result[$key] = $container->get($value->getKey());
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultScope() : Scope
    {
        return Scope::singleton();
    }
}
