<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Repositories;

use PDO;
use Realitaa\PhpVite\Models\Supplier;

class SupplierRepository
{
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? require dirname(__DIR__, 2) . '/database/pdo.php';
    }

    /**
     * Retrieve all suppliers ordered by name.
     *
     * @return array<Supplier>
     */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, name, email, phone, address FROM suppliers ORDER BY name ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => Supplier::fromArray($row), $rows);
    }

    /**
     * Find supplier by ID.
     */
    public function findById(int $id): ?Supplier
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, phone, address FROM suppliers WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Supplier::fromArray($row) : null;
    }
}
