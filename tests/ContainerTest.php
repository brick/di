<?php

namespace Brick\Di\Tests;

use Brick\Di\Resolve;
use Brick\Di\Scope;
use Doctrine\Common\Annotations\AnnotationReader;
use Brick\Di\InjectionPolicy\AnnotationPolicy;
use Brick\Di\Annotation\Inject;
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
    public function testContainer(Container $container)
    {
        $container->add([
            'db.host' => 'localhost',
            'db.user' => 'root',
            'db.pass' => 'passw0rd'
        ]);

        $container->bind(SomeService::class)->with([
            'timeout' => 3600,
            'config' => [
                'HOSTNAME' => new Resolve('db.host'),
                'CREDENTIALS' => [
                    'USERNAME' => new Resolve('db.user'),
                    'PASSWORD' => new Resolve('db.pass')
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

    /**
     * @return array
     */
    public function containerProvider()
    {
        $containerWithoutAnnotations = new Container();

        $containerWithoutAnnotations->bind(DatabaseConnection::class)->with([
            'hostname' => new Resolve('db.host'),
            'username' => new Resolve('db.user'),
            'password' => new Resolve('db.pass')
        ]);

        $reader = new AnnotationReader();
        $policy = new AnnotationPolicy($reader);

        $containerWithAnnotations = new Container($policy);

        return [
            [$containerWithoutAnnotations],
            [$containerWithAnnotations]
        ];
    }

    /**
     * @dataProvider providerScope
     */
    public function testScope(Scope $dbScope, Scope $aliasScope, $dbSame, $aliasSame)
    {
        $container = new Container();

        $container->set('foo', 'bar');
        $container->bind(DatabaseConnection::class)->in($dbScope)->with([
                'hostname' => new Resolve('foo'),
                'username' => new Resolve('foo'),
                'password' => new Resolve('foo')
            ]);

        $container->alias('database.connection.shared', DatabaseConnection::class)->in($aliasScope);

        $this->assertResult($container, DatabaseConnection::class, $dbSame);
        $this->assertResult($container, 'database.connection.shared', $aliasSame);
    }

    /**
     * @return array
     */
    public function providerScope()
    {
        return [
            [Scope::singleton(), Scope::singleton(), true, true],
            [Scope::singleton(), Scope::prototype(), true, true],
            [Scope::prototype(), Scope::singleton(), false, true],
            [Scope::prototype(), Scope::prototype(), false, false],
        ];
    }

    /**
     * @param Container $container
     * @param string    $key
     * @param bool      $same
     */
    private function assertResult(Container $container, $key, $same)
    {
        $a = $container->get($key);
        $b = $container->get($key);

        $this->assertInstanceOf(DatabaseConnection::class, $a);
        $this->assertInstanceOf(DatabaseConnection::class, $b);

        $same ? $this->assertSame($a, $b) : $this->assertNotSame($a, $b);
    }
}

/**
 * @Inject
 */
class DatabaseConnection
{
    public $hostname;
    public $username;
    public $password;

    /**
     * @Inject(hostname="db.host", username="db.user", password="db.pass")
     */
    public function __construct($hostname, $username, $password)
    {
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
    }
}

class SomeService
{
    public $connection;
    public $timeout;
    public $config;

    public function __construct(DatabaseConnection $connection, $timeout, array $config)
    {
        $this->connection = $connection;
        $this->timeout = $timeout;
        $this->config = $config;
    }
}

class AnotherService
{
    public $connection;
    public $service;

    public function __construct(DatabaseConnection $connection, SomeService $service)
    {
        $this->connection = $connection;
        $this->service = $service;
    }
}
