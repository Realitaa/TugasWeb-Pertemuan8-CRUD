<?php

declare(strict_types=1);

namespace Tests;

use Database\Database;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DatabaseSingletonTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        // Reset state singleton jika diubah pada saat mock
        Database::setInstance(null);
    }

    public function testDatabaseClassHasPrivateConstructorAndClone(): void
    {
        $ref = new ReflectionClass(Database::class);

        $constructor = $ref->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isPrivate(), 'Database constructor must be private for Singleton pattern');

        $cloneMethod = $ref->getMethod('__clone');
        $this->assertTrue($cloneMethod->isPrivate(), 'Database __clone must be private for Singleton pattern');
    }

    public function testDatabaseReturnsSameInstanceAcrossCalls(): void
    {
        // Gunakan sqlite in-memory PDO untuk menguji singleton secara independen dari konfigurasi MySQL
        $mockPdo = new PDO('sqlite::memory:');
        Database::setInstance($mockPdo);

        $instance1 = Database::getInstance();
        $instance2 = Database::getInstance();

        $this->assertSame($mockPdo, $instance1);
        $this->assertSame($instance1, $instance2, 'Database::getInstance() must return the same instance');
    }

    public function testPdoFileRequireReturnsSingletonInstance(): void
    {
        $mockPdo = new PDO('sqlite::memory:');
        Database::setInstance($mockPdo);

        $requiredPdo = require dirname(__DIR__) . '/database/pdo.php';

        $this->assertSame($mockPdo, $requiredPdo);
        $this->assertSame(Database::getInstance(), $requiredPdo);
    }
}
