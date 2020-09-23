<?php

namespace Brick\Di\Tests;

use Brick\Di\Ref;
use Brick\Di\Scope;
use Brick\Di\InjectionPolicy\NullPolicy;
use Brick\Di\InjectionPolicy\AttributePolicy;
use Brick\Di\Inject;
use Brick\Di\Container;

use PHPUnit\Framework\TestCase;

/**
 * Unit test for the dependency injection container.
 */
class ContainerTest extends TestCase
{
    /**
     * @dataProvider containerProvider
     */
    public function testContainer(Container $container) : void
    {
        $container->add([
            'db.host' => 'localhost',
            'db.user' => 'root',
            'db.pass' => 'passw0rd'
        ]);

        $container->bind(SomeService::class)->with([
            'timeout' => 3600,
            'config' => [
                'HOSTNAME' => new Ref('db.host'),
                'CREDENTIALS' => [
                    'USERNAME' => new Ref('db.user'),
                    'PASSWORD' => new Ref('db.pass')
                ]
            ]
        ]);

        $container->bind(AnotherService::class);

        $service = $container->get(AnotherService::class);

        $this->assertInstanceOf(AnotherService::class, $service);
        $this->assertInstanceOf(DatabaseConnection::class, $service->connection);
        $this->assertInstanceOf(SomeService::class, $service->service);

        $this->assertSame('localhost', $service->connection->hostname);
        $this->assertSame('root', $service->connection->username);
        $this->assertSame('passw0rd', $service->connection->password);

        $this->assertSame(3600, $service->service->timeout);

        $this->assertSame([
            'HOSTNAME' => 'localhost',
            'CREDENTIALS' => [
                'USERNAME' => 'root',
                'PASSWORD' => 'passw0rd'
            ]
        ], $service->service->config);
    }

    public function containerProvider() : array
    {
        $containerWithoutAttributes = new Container(new NullPolicy());

        $containerWithoutAttributes->bind(DatabaseConnection::class)->with([
            'hostname' => new Ref('db.host'),
            'username' => new Ref('db.user'),
            'password' => new Ref('db.pass')
        ]);

        $containerWithAttributes = new Container();

        return [
            [$containerWithoutAttributes],
            [$containerWithAttributes]
        ];
    }

    /**
     * @dataProvider providerScope
     */
    public function testScope(Scope $dbScope, Scope $aliasScope, bool $dbSame, bool $aliasSame) : void
    {
        $container = new Container(new NullPolicy());

        $container->set('foo', 'bar');
        $container->bind(DatabaseConnection::class)->in($dbScope)->with([
            'hostname' => new Ref('foo'),
            'username' => new Ref('foo'),
            'password' => new Ref('foo')
        ]);

        $container->alias('database.connection.shared', DatabaseConnection::class)->in($aliasScope);

        $this->assertResult($container, DatabaseConnection::class, $dbSame);
        $this->assertResult($container, 'database.connection.shared', $aliasSame);
    }

    /**
     * @return array
     */
    public function providerScope() : array
    {
        return [
            [new Scope\Singleton(), new Scope\Singleton(), true, true],
            [new Scope\Singleton(), new Scope\Prototype(), true, true],
            [new Scope\Prototype(), new Scope\Singleton(), false, true],
            [new Scope\Prototype(), new Scope\Prototype(), false, false],
        ];
    }

    private function assertResult(Container $container, string $key, bool $same) : void
    {
        $a = $container->get($key);
        $b = $container->get($key);

        $this->assertInstanceOf(DatabaseConnection::class, $a);
        $this->assertInstanceOf(DatabaseConnection::class, $b);

        $same ? $this->assertSame($a, $b) : $this->assertNotSame($a, $b);
    }
}

#[Inject]
class DatabaseConnection
{
    public string $hostname;
    public string $username;
    public string $password;

    #[Inject(['hostname' => 'db.host', 'username' => 'db.user', 'password' => 'db.pass'])]
    public function __construct(string $hostname, string $username, string $password)
    {
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
    }
}

class SomeService
{
    public DatabaseConnection $connection;
    public int $timeout;
    public array $config;

    public function __construct(DatabaseConnection $connection, int $timeout, array $config)
    {
        $this->connection = $connection;
        $this->timeout = $timeout;
        $this->config = $config;
    }
}

class AnotherService
{
    public DatabaseConnection $connection;
    public SomeService $service;

    public function __construct(DatabaseConnection $connection, SomeService $service)
    {
        $this->connection = $connection;
        $this->service = $service;
    }
}
