<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Realitaa\PhpVite\Services\DatabaseResetService;

class DatabaseResetServiceTest extends TestCase
{
    protected ?string $originalKey = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalKey = $_ENV['DB_RESET_KEY'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->originalKey !== null) {
            $_ENV['DB_RESET_KEY'] = $this->originalKey;
        } else {
            unset($_ENV['DB_RESET_KEY']);
        }
        parent::tearDown();
    }

    public function testIsValidKeyWithMatchingKey(): void
    {
        $testKey = '01a0d275-4f97-76a0-96ee-29ff60a76314';
        $_ENV['DB_RESET_KEY'] = $testKey;

        $service = new DatabaseResetService();

        $this->assertTrue($service->isValidKey($testKey));
    }

    public function testIsValidKeyWithMismatchedKey(): void
    {
        $_ENV['DB_RESET_KEY'] = 'correct-secret-key';

        $service = new DatabaseResetService();

        $this->assertFalse($service->isValidKey('wrong-key'));
        $this->assertFalse($service->isValidKey(''));
        $this->assertFalse($service->isValidKey(null));
    }

    public function testIsValidKeyWhenEnvKeyIsNotSet(): void
    {
        unset($_ENV['DB_RESET_KEY']);

        $service = new DatabaseResetService();

        $this->assertFalse($service->isValidKey('any-key'));
    }
}
