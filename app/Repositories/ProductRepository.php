<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Repositories;

use PDO;
use Realitaa\PhpVite\Models\Product;

class ProductRepository
{
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? require dirname(__DIR__, 2) . '/database/pdo.php';
    }

    /**
     * Get paginated products with 2 JOINs (categories & suppliers) and optional search.
     *
     * @return array{items: array<Product>, total: int, currentPage: int, perPage: int, totalPages: int}
     */
    public function paginate(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $whereClause = '';
        $params = [];

        $trimmedSearch = trim((string) $search);
        if ($trimmedSearch !== '') {
            $whereClause = 'WHERE (p.name LIKE :search_name OR p.sku LIKE :search_sku OR c.name LIKE :search_cat OR s.name LIKE :search_sup)';
            $searchWildcard = '%' . $trimmedSearch . '%';
            $params[':search_name'] = $searchWildcard;
            $params[':search_sku'] = $searchWildcard;
            $params[':search_cat'] = $searchWildcard;
            $params[':search_sup'] = $searchWildcard;
        }

        // Count total matching rows
        $countSql = "
            SELECT COUNT(*) 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            {$whereClause}
        ";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;

        // Fetch paginated items with 2 JOINs
        $sql = "
            SELECT 
                p.*,
                c.name AS category_name,
                s.name AS supplier_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            {$whereClause}
            ORDER BY p.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $items = array_map(fn(array $row) => Product::fromArray($row), $rows);

        return [
            'items'       => $items,
            'total'       => $total,
            'currentPage' => $page,
            'perPage'     => $perPage,
            'totalPages'  => $totalPages,
        ];
    }

    /**
     * Retrieve all products with category and supplier relations (optional search filter).
     *
     * @return array<Product>
     */
    public function allWithRelations(?string $search = null): array
    {
        $whereClause = '';
        $params = [];

        $trimmedSearch = trim((string) $search);
        if ($trimmedSearch !== '') {
            $whereClause = 'WHERE (p.name LIKE :search_name OR p.sku LIKE :search_sku OR c.name LIKE :search_cat OR s.name LIKE :search_sup)';
            $searchWildcard = '%' . $trimmedSearch . '%';
            $params[':search_name'] = $searchWildcard;
            $params[':search_sku'] = $searchWildcard;
            $params[':search_cat'] = $searchWildcard;
            $params[':search_sup'] = $searchWildcard;
        }

        $sql = "
            SELECT 
                p.*,
                c.name AS category_name,
                s.name AS supplier_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            {$whereClause}
            ORDER BY p.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => Product::fromArray($row), $rows);
    }

    /**
     * Find single product by ID with category and supplier relations.
     */
    public function findById(int $id): ?Product
    {
        $sql = "
            SELECT 
                p.*,
                c.name AS category_name,
                s.name AS supplier_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            WHERE p.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Product::fromArray($row) : null;
    }

    /**
     * Check if a given SKU is unique.
     */
    public function isSkuUnique(string $sku, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM products WHERE sku = :sku";
        $params = [':sku' => $sku];

        if ($excludeId !== null) {
            $sql .= " AND id != :excludeId";
            $params[':excludeId'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return ((int) $stmt->fetchColumn()) === 0;
    }

    /**
     * Insert a new product into the database.
     */
    public function create(Product $product): int
    {
        $sql = "
            INSERT INTO products (category_id, supplier_id, name, sku, price, stock)
            VALUES (:category_id, :supplier_id, :name, :sku, :price, :stock)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':category_id' => $product->categoryId,
            ':supplier_id' => $product->supplierId,
            ':name'        => $product->name,
            ':sku'         => $product->sku,
            ':price'       => $product->price,
            ':stock'       => $product->stock,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update an existing product in the database.
     */
    public function update(Product $product): bool
    {
        if ($product->id === null) {
            return false;
        }

        $sql = "
            UPDATE products
            SET 
                category_id = :category_id,
                supplier_id = :supplier_id,
                name = :name,
                sku = :sku,
                price = :price,
                stock = :stock
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'          => $product->id,
            ':category_id' => $product->categoryId,
            ':supplier_id' => $product->supplierId,
            ':name'        => $product->name,
            ':sku'         => $product->sku,
            ':price'       => $product->price,
            ':stock'       => $product->stock,
        ]);
    }

    /**
     * Delete a product by ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit the current database transaction.
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback the current database transaction.
     */
    public function rollBack(): bool
    {
        if ($this->pdo->inTransaction()) {
            return $this->pdo->rollBack();
        }
        return false;
    }

    /**
     * Check if currently in an active transaction.
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}
