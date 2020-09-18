<?php

declare(strict_types=1);

namespace Brick\Di\Definition;

use Brick\Di\Definition;
use Brick\Di\Ref;
use Brick\Di\Scope;
use Brick\Di\Container;
use Closure;

/**
 * Resolves a class name.
 */
class BindingDefinition extends Definition
{
    /**
     * The class name to instantiate, or a closure to invoke.
     */
    private Closure|string $target;

    private array $parameters = [];

    /**
     * Class constructor.
     *
     * @param Closure|string $target The class name to instantiate, or a closure to invoke.
     */
    public function __construct(Closure|string $target)
    {
        $this->target = $target;
    }

    /**
     * Sets an associative array of parameters to resolve the binding.
     *
     * These will be used to resolve the parameters of the bound closure, or the constructor parameters of the bound
     * class. The array keys must match the closure/constructor parameter names.
     *
     * Parameters can include references to other container keys by wrapping keys in a `Ref` object.
     * `Ref` objects deeply nested in arrays will also be resolved:
     *
     *     $container->bind(MyService::class)->with([
     *       'username' => 'admin'
     *       'password' => new Ref('myservice.password'),
     *       'options' => [
     *         'timeout' => new Ref('myservice.timeout'),
     *       ]
     *     ]);
     *
     * Using `new Ref()` is conceptually equivalent to calling `$container->get()`, but with `Ref`,
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
    public function resolve(Container $container) : mixed
    {
        $parameters = $this->getParameters($container, $this->parameters);

        if ($this->target instanceof Closure) {
            return $container->getInjector()->invoke($this->target, $parameters);
        }

        return $container->getInjector()->instantiate($this->target, $parameters);
    }

    private function getParameters(Container $container, array $parameters) : array
    {
        $result = [];

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->getParameters($container, $value);
            } elseif ($value instanceof Ref) {
                $result[$key] = $container->get($value->getKey());
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    protected function getDefaultScope() : Scope
    {
        return new Scope\Singleton();
    }
}
