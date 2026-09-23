<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Database\Seeder\Seeder;
use Dotenv\Dotenv;

$root = dirname(__DIR__);

// Load environment variables
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();

try {
    $seeder = new Seeder();
    $seeder->run();
    exit(0);
} catch (Throwable $e) {
    echo "\n  \033[1;31m[FAILED]\033[0m Gagal menjalankan seeder!\n";
    echo "  Pesan Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
