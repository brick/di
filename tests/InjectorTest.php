<?php

namespace Brick\DI\Tests;

use Brick\DI\InjectionPolicy\NullPolicy;
use Brick\DI\Injector;
use Brick\DI\ValueResolver\DefaultValueResolver;

use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testCanInjectPrivateMethod() : void
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
    public string $foo;
    public string $bar;

    private function __construct(string $foo, string $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
