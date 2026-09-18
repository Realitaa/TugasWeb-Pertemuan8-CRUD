<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$root = dirname(__DIR__);

// Load environment variables
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();

$config = require $root . '/config/database.php';
$default = $config['default'] ?? 'pgsql';
$connConfig = $config['connections'][$default] ?? [];

echo "\033[1;36m[PDO Connection Tester]\033[0m Menghubungkan ke database [{$default}]...\n";

try {
    /** @var PDO $pdo */
    $pdo = require $root . '/database/pdo.php';

    // Retrieve attributes safely
    $driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $serverVersion = @$pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?: 'Unknown';
    $connectionStatus = @$pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) ?: 'Connected';
    $inTransaction = $pdo->inTransaction() ? 'true' : 'false';

    echo "\033[1;32m✓ Koneksi database berhasil!\033[0m\n\n";
    echo "=> PDO {#" . spl_object_id($pdo) . "\n";
    echo "     inTransaction: {$inTransaction},\n";
    echo "     attributes: {\n";
    echo "       DRIVER_NAME: \"{$driverName}\",\n";
    echo "       SERVER_VERSION: \"{$serverVersion}\",\n";
    echo "       CONNECTION_STATUS: \"{$connectionStatus}\",\n";
    if (isset($connConfig['database'])) {
        echo "       DATABASE: \"{$connConfig['database']}\",\n";
    }
    if (isset($connConfig['host'])) {
        echo "       HOST: \"{$connConfig['host']}:" . ($connConfig['port'] ?? '') . "\",\n";
    }
    if (isset($connConfig['username'])) {
        echo "       USERNAME: \"{$connConfig['username']}\",\n";
    }
    echo "     },\n";
    echo "   }\n";

    exit(0);
} catch (Throwable $e) {
    echo "\033[1;31m✗ Koneksi database gagal!\033[0m\n";
    echo "Pesan Error: " . $e->getMessage() . "\n\n";
    echo "\033[1;33mPetunjuk:\033[0m Pastikan konfigurasi database di file .env sudah sesuai:\n";
    echo "  DB_DRIVER=" . ($connConfig['driver'] ?? $default) . "\n";
    echo "  DB_HOST=" . ($connConfig['host'] ?? '127.0.0.1') . "\n";
    echo "  DB_PORT=" . ($connConfig['port'] ?? '5432') . "\n";
    echo "  DB_DATABASE=" . ($connConfig['database'] ?? 'inventaris') . "\n";
    echo "  DB_USERNAME=" . ($connConfig['username'] ?? 'postgres') . "\n";
    echo "  DB_PASSWORD=" . (isset($connConfig['password']) && $connConfig['password'] !== '' ? '******' : '(kosong)') . "\n";

    exit(1);
}
