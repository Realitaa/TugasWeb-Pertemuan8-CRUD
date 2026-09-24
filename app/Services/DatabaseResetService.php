<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Services;

use Database\Seeder\Seeder;
use PDO;

class DatabaseResetService
{
    public function __construct(
        protected ?PDO $pdo = null
    ) {
        $this->pdo = $pdo ?? require dirname(__DIR__, 2) . '/database/pdo.php';
    }

    /**
     * Verify if provided key matches DB_RESET_KEY configured in .env.
     */
    public function isValidKey(?string $key): bool
    {
        $configuredKey = $_ENV['DB_RESET_KEY'] ?? getenv('DB_RESET_KEY') ?? null;
        if (!is_string($configuredKey) || trim($configuredKey) === '') {
            return false;
        }

        if (!is_string($key) || trim($key) === '') {
            return false;
        }

        return hash_equals(trim($configuredKey), trim($key));
    }

    /**
     * Execute fresh database migration and seeder.
     */
    public function resetAndSeed(): bool
    {
        $root = dirname(__DIR__, 2);

        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

        $stmt = $this->pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $this->pdo->exec("DROP TABLE IF EXISTS `{$table}`");
        }

        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Run migrations
        $migrationsDir = $root . '/database/migrations';
        $migrationFiles = glob($migrationsDir . '/*.sql') ?: [];
        sort($migrationFiles);

        foreach ($migrationFiles as $file) {
            $sql = file_get_contents($file);
            $this->pdo->exec($sql);
        }

        // Run seeder silently without polluting HTTP output
        ob_start();
        try {
            $seeder = new Seeder($this->pdo);
            $seeder->run();
        } finally {
            ob_end_clean();
        }

        return true;
    }
}
