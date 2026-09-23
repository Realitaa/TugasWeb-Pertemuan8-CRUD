<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$root = dirname(__DIR__);

// Load environment variables
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();

$config = require $root . '/config/database.php';
$default = $config['default'] ?? 'mysql';
$connConfig = $config['connections'][$default] ?? [];

echo "\n\033[1;36m  INFO\033[0m  Running migrations on database [\033[1m{$default}\033[0m: " . ($connConfig['database'] ?? '') . "].\n\n";

try {
    /** @var PDO $pdo */
    $pdo = require $root . '/database/pdo.php';
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    $migrationsDir = $root . '/database/migrations';
    if (!is_dir($migrationsDir)) {
        throw new RuntimeException("Folder migrasi tidak ditemukan di database/migrations/");
    }

    // Ambil seluruh file migrasi SQL
    $migrationFiles = glob($migrationsDir . '/*.sql') ?: [];
    sort($migrationFiles);

    if (empty($migrationFiles)) {
        echo "  \033[1;33mTidak ada file migrasi yang ditemukan di database/migrations/.\033[0m\n\n";
        exit(0);
    }

    foreach ($migrationFiles as $file) {
        $relativePath = 'database/migrations/' . basename($file);
        $sql = file_get_contents($file);

        $startTime = hrtime(true);

        $pdo->exec($sql);

        $durationMs = (int) round((hrtime(true) - $startTime) / 1e6);

        // Format ala Laravel: [DONE] berwarna hijau di sebelah kiri nama file
        $badge = "\033[1;32m[DONE]\033[0m";
        $durationStr = sprintf("%dms", max(1, $durationMs));
        $dotsCount = max(2, 60 - strlen($relativePath) - strlen($durationStr));
        $dots = str_repeat('.', $dotsCount);

        echo "  {$badge} {$relativePath} {$dots} {$durationStr}\n";
    }

    echo "\n";
    return 0;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "\n  \033[1;31m[FAILED]\033[0m Gagal menjalankan migrasi!\n";
    echo "  Pesan Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
