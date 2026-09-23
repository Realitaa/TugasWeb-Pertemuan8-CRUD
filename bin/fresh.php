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
$dbName = $connConfig['database'] ?? '';

echo "\n\033[1;36m  INFO\033[0m  Dropping all tables on database [\033[1m{$default}\033[0m: {$dbName}].\n\n";

try {
    /** @var PDO $pdo */
    $pdo = require $root . '/database/pdo.php';

    // Disable foreign key checks untuk drop aman
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Ambil semua tabel yang ada di database aktif
    $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($tables)) {
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
        }
        echo "  \033[1;32m[DONE]\033[0m Dropped " . count($tables) . " tables successfully.\n\n";
    } else {
        echo "  \033[1;33m[NOTE]\033[0m Tidak ada tabel untuk dihapus.\n\n";
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Lanjut jalankan migrasi
    require __DIR__ . '/migrate.php';
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO) {
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        } catch (Throwable) {
            // Ignore reset error
        }
    }

    echo "\n  \033[1;31m[FAILED]\033[0m Gagal melakukan migrate:fresh!\n";
    echo "  Pesan Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
