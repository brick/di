<?php

namespace Brick\Di\Tests;

use Brick\Di\InjectionPolicy\NullPolicy;
use Brick\Di\Injector;
use Brick\Di\ValueResolver\DefaultValueResolver;

use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testCanInjectPrivateMethod()
    {
        $injector = new Injector(new DefaultValueResolver(), new NullPolicy());

        $object = $injector->instantiate(PrivateConstructor::class, [
            'foo' => 'Foo',
            'bar' => 'Bar'
        ]);

        $this->assertSame('Foo', $object->foo);
        $this->assertSame('Bar', $object->bar);
    }
}

class PrivateConstructor
{
    public $foo;
    public $bar;

    private function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
