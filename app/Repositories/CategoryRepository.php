<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Repositories;

use PDO;
use Realitaa\PhpVite\Models\Category;

class CategoryRepository
{
    protected ?PDO $pdo = null;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function getPdo(): ?PDO
    {
        if ($this->pdo === null) {
            try {
                $this->pdo = require dirname(__DIR__, 2) . '/database/pdo.php';
            } catch (\Throwable) {
                return null;
            }
        }

        return $this->pdo;
    }

    /**
     * Retrieve all categories ordered by name.
     *
     * @return array<Category>
     */
    public function all(): array
    {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return [];
        }

        $stmt = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => Category::fromArray($row), $rows);
    }

    /**
     * Find category by ID.
     */
    public function findById(int $id): ?Category
    {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT id, name FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Category::fromArray($row) : null;
    }
}
