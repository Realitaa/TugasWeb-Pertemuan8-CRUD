<?php

declare(strict_types=1);

namespace Database;

use Dotenv\Dotenv;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Singleton Pattern untuk Koneksi Database PDO.
 * 
 * Memastikan hanya terdapat satu instance koneksi PDO yang aktif di memori
 * selama siklus hidup request aplikasi.
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Private constructor untuk mencegah instansiasi langsung dari luar (Singleton).
     */
    private function __construct()
    {
    }

    /**
     * Private clone untuk mencegah penggandaan instance (Singleton).
     */
    private function __clone()
    {
    }

    /**
     * Dapatkan instance tunggal PDO (Singleton pattern).
     */
    public static function getInstance(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $root = dirname(__DIR__);

        // Pastikan autoloader vendor telah dimuat
        if (!class_exists(Dotenv::class) && file_exists($root . '/vendor/autoload.php')) {
            require_once $root . '/vendor/autoload.php';
        }

        // Muat variabel environment jika belum termuat
        if (class_exists(Dotenv::class)) {
            $dotenv = Dotenv::createImmutable($root);
            $dotenv->safeLoad();
        }

        $config = require $root . '/config/database.php';

        $default = $config['default'] ?? 'mysql';
        $connection = $config['connections'][$default] ?? null;

        if (!$connection) {
            throw new InvalidArgumentException("Konfigurasi database untuk koneksi [{$default}] tidak ditemukan.");
        }

        $driver = $connection['driver'] ?? $default;

        switch ($driver) {
            case 'mysql':
                // Cek unix socket terlebih dahulu untuk koneksi lokal berkinerja tinggi
                $socket = $connection['unix_socket'] ?? null;
                if (!empty($socket) && file_exists($socket)) {
                    $dsn = sprintf(
                        'mysql:unix_socket=%s;dbname=%s;charset=%s',
                        $socket,
                        $connection['database'],
                        $connection['charset'] ?? 'utf8mb4'
                    );
                } else {
                    $dsn = sprintf(
                        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                        $connection['host'],
                        $connection['port'],
                        $connection['database'],
                        $connection['charset'] ?? 'utf8mb4'
                    );
                }
                break;

            case 'pgsql':
                $dsn = sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s',
                    $connection['host'],
                    $connection['port'],
                    $connection['database']
                );
                break;

            case 'sqlite':
                $dsn = sprintf('sqlite:%s', $connection['database']);
                break;

            default:
                throw new InvalidArgumentException("Driver database [{$driver}] tidak didukung.");
        }

        $options = $connection['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$instance = new PDO(
                $dsn,
                $connection['username'] ?? null,
                $connection['password'] ?? null,
                $options
            );
        } catch (PDOException $e) {
            // Fallback: Jika koneksi MySQL TCP gagal (misal server hanya listen di socket), coba via unix socket
            if ($driver === 'mysql') {
                $commonSockets = ['/run/mysqld/mysqld.sock', '/var/run/mysqld/mysqld.sock', '/tmp/mysql.sock'];
                foreach ($commonSockets as $sock) {
                    if (file_exists($sock)) {
                        try {
                            $fallbackDsn = sprintf(
                                'mysql:unix_socket=%s;dbname=%s;charset=%s',
                                $sock,
                                $connection['database'],
                                $connection['charset'] ?? 'utf8mb4'
                            );
                            self::$instance = new PDO(
                                $fallbackDsn,
                                $connection['username'] ?? null,
                                $connection['password'] ?? null,
                                $options
                            );
                            return self::$instance;
                        } catch (PDOException) {
                            // Lanjut throw error asli jika fallback juga gagal
                        }
                    }
                }
            }

            throw new PDOException("Gagal terhubung ke database [{$driver}]: " . $e->getMessage(), (int) $e->getCode(), $e);
        }

        return self::$instance;
    }

    /**
     * Atur atau reset instance koneksi (berguna untuk testing atau mock).
     */
    public static function setInstance(?PDO $pdo): void
    {
        self::$instance = $pdo;
    }
}
