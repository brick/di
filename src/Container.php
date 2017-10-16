<?php

declare(strict_types=1);

namespace Brick\Di;

use Brick\Di\Definition\AliasDefinition;
use Brick\Di\Definition\BindingDefinition;
use Brick\Di\InjectionPolicy\NullPolicy;
use Brick\Di\ValueResolver\ContainerValueResolver;
use Brick\Reflection\ReflectionTools;

/**
 * Resolves object dependencies.
 */
class Container
{
    /**
     * @var \Brick\Di\InjectionPolicy
     */
    private $injectionPolicy;

    /**
     * @var \Brick\Di\Injector
     */
    private $injector;

    /**
     * @var \Brick\Di\ValueResolver\ContainerValueResolver
     */
    private $valueResolver;

    /**
     * @var \Brick\Reflection\ReflectionTools
     */
    private $reflectionTools;

    /**
     * @var array
     */
    private $items = [];

    /**
     * Class constructor.
     *
     * @param InjectionPolicy|null $policy
     */
    public function __construct(InjectionPolicy $policy = null)
    {
        if ($policy === null) {
            $policy = new NullPolicy();
        }

        $this->injectionPolicy = $policy;
        $this->valueResolver = new ContainerValueResolver($this);
        $this->injector = new Injector($this->valueResolver, $policy);
        $this->reflectionTools = new ReflectionTools();

        $this->set(self::class, $this);
        $this->set(Injector::class, $this->injector);
    }

    /**
     * @return Injector
     */
    public function getInjector() : Injector
    {
        return $this->injector;
    }

    /**
     * @return ContainerValueResolver
     */
    public function getValueResolver() : ContainerValueResolver
    {
        return $this->valueResolver;
    }

    /**
     * @return InjectionPolicy
     */
    public function getInjectionPolicy() : InjectionPolicy
    {
        return $this->injectionPolicy;
    }

    /**
     * Returns whether the container has the given key.
     *
     * @param string $key The key, class or interface name.
     *
     * @return bool
     */
    public function has(string $key) : bool
    {
        if (isset($this->items[$key])) {
            return true;
        }

        if (class_exists($key)) {
            $class = new \ReflectionClass($key);
            $classes = $this->reflectionTools->getClassHierarchy($class);

            foreach ($classes as $class) {
                if ($this->injectionPolicy->isClassInjected($class)) {
                    $this->bind($key); // @todo allow to configure scope (singleton) with annotations

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the value for the given key.
     *
     * @param string $key The key, class or interface name.
     *
     * @return mixed
     *
     * @throws DependencyInjectionException If the key is not registered.
     */
    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw DependencyInjectionException::keyNotRegistered($key);
        }

        $value = $this->items[$key];

        if ($value instanceof Definition) {
            return $value->get($this);
        }

        return $value;
    }

    /**
     * Sets a single value.
     *
     * The value will be returned as is when requested with get().
     *
     * @param string $key   The key, class or interface name.
     * @param mixed  $value The value to set.
     *
     * @return Container
     */
    public function set(string $key, $value) : Container
    {
        $this->items[$key] = $value;

        return $this;
    }

    /**
     * Sets multiple values.
     *
     * This is equivalent to calling set() for each key-value pair.
     *
     * @param array $values An associative array of key-value pairs.
     *
     * @return Container
     */
    public function add(array $values) : Container
    {
        $this->items = $values + $this->items;

        return $this;
    }

    /**
     * Binds a key to a class name to be instantiated, or to a closure to be invoked.
     *
     * By default, the key is bound to itself, so these two lines of code are equivalent;
     *
     *     $container->bind('Class\Name');
     *     $container->bind('Class\Name', 'Class\Name');
     *
     * It can be used to bind an interface to a class to be instantiated:
     *
     *     $container->bind('Interface\Name', 'Class\Name');
     *
     * The key can also be bound to a closure to return any value:
     *
     *     $container->bind('Class\Or\Interface\Name', function() {
     *         return new Class\Name();
     *     });
     *
     * If the key is an interface name, the target must be the name of a class implementing this interface,
     * or a closure returning an instance of such class.
     *
     * If the key is a class name, the target must be the name of the class or one of its subclasses,
     * or a closure returning an instance of this class.
     *
     * Any parameters required by the class constructor or the closure will be automatically resolved when possible
     * using type-hinted classes or interfaces. Additional parameters can be passed as an associative array using
     * the with() method:
     *
     *     $container->bind('Interface\Name', 'Class\Name')->with([
     *         'username' => 'admin',
     *         'password' => new Ref('config.password')
     *     ]);
     *
     * Fixed parameters can be provided as is, and references to container keys can be provided by wrapping the key in
     * a `Ref` object. See `BindingDefinition::with()` for more information.
     *
     * Note: not use bind() to attach an existing object instance. Use set() instead.
     *
     * @param string               $key    The key, class or interface name.
     * @param \Closure|string|null $target The class name or closure to bind. Optional if the key is the class name.
     *
     * @return BindingDefinition
     */
    public function bind(string $key, $target = null) : BindingDefinition
    {
        return $this->items[$key] = new BindingDefinition($target ?? $key);
    }

    /**
     * Creates an alias from one key to another.
     *
     * This method can be used for use cases as simple as:
     *
     *     $container->alias('my.alias', 'my.service');
     *
     * This is particularly useful when you have already registered a class by its name,
     * but now want to make it resolvable through an interface name it implements as well:
     *
     *     $container->bind('Class\Name');
     *     $container->alias('Interface\Name', 'Class\Name');
     *
     * An alias always queries the current value by default, unless you change its scope,
     * which may be used for advanced use cases, such as creating singletons out of a prototype:
     *
     *     $container->bind('Class\Name')->in(Scope::prototype());
     *     $container->alias('my.shared.instance', 'Class\Name')->in(Scope::singleton());
     *
     * @param string $key       The key, class or interface name.
     * @param string $targetKey The target key.
     *
     * @return \Brick\Di\Definition\AliasDefinition
     */
    public function alias(string $key, string $targetKey) : AliasDefinition
    {
        return $this->items[$key] = new AliasDefinition($targetKey);
    }
}
