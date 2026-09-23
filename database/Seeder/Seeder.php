<?php

declare(strict_types=1);

namespace Database\Seeder;

use Faker\Factory as FakerFactory;
use PDO;
use RuntimeException;

class Seeder
{
    protected PDO $pdo;
    protected string $root;

    public function __construct(?PDO $pdo = null)
    {
        $this->root = dirname(__DIR__, 2);
        $this->pdo = $pdo ?? require $this->root . '/database/pdo.php';
    }

    /**
     * Run all seeders.
     */
    public function run(): void
    {
        echo "\n\033[1;36m  INFO\033[0m  Seeding database...\n\n";

        $startTime = hrtime(true);

        $supplierIds = $this->seedSuppliers(10);
        $categoryMap = $this->seedCategoriesAndProducts($supplierIds);

        $durationMs = (int) round((hrtime(true) - $startTime) / 1e6);

        echo "\n  \033[1;32m[DONE]\033[0m Database seeded successfully in {$durationMs}ms.\n\n";
    }

    /**
     * Seed suppliers table using Faker.
     *
     * @return array<int> List of inserted supplier IDs
     */
    protected function seedSuppliers(int $count = 10): array
    {
        $faker = FakerFactory::create('id_ID');

        $stmt = $this->pdo->prepare(
            "INSERT INTO suppliers (name, email, phone, address) VALUES (:name, :email, :phone, :address)"
        );

        $supplierIds = [];
        for ($i = 0; $i < $count; $i++) {
            $stmt->execute([
                'name'    => $faker->company(),
                'email'   => $faker->unique()->companyEmail(),
                'phone'   => $faker->phoneNumber(),
                'address' => $faker->address(),
            ]);
            $supplierIds[] = (int) $this->pdo->lastInsertId();
        }

        $badge = "\033[1;32m[DONE]\033[0m";
        echo "  {$badge} Seeded " . count($supplierIds) . " suppliers.\n";

        return $supplierIds;
    }

    /**
     * Seed categories and products from data/products.json.
     *
     * @param array<int> $supplierIds
     * @return array<string, int>
     */
    protected function seedCategoriesAndProducts(array $supplierIds): array
    {
        $jsonPath = $this->root . '/data/products.json';
        if (!file_exists($jsonPath)) {
            throw new RuntimeException("File [data/products.json] tidak ditemukan!");
        }

        $rawJson = file_get_contents($jsonPath);
        $products = json_decode($rawJson, true);

        if (!is_array($products)) {
            throw new RuntimeException("Format [data/products.json] tidak valid!");
        }

        // Cache category IDs: category_name => category_id
        $categoryMap = [];
        $catStmtFind = $this->pdo->prepare("SELECT id FROM categories WHERE name = :name LIMIT 1");
        $catStmtInsert = $this->pdo->prepare("INSERT INTO categories (name) VALUES (:name)");

        $prodStmt = $this->pdo->prepare(
            "INSERT INTO products (category_id, supplier_id, name, sku, price, stock) 
             VALUES (:category_id, :supplier_id, :name, :sku, :price, :stock)"
        );

        $productCount = 0;
        foreach ($products as $p) {
            $catName = trim((string) ($p['category'] ?? 'Uncategorized'));
            if ($catName === '') {
                $catName = 'Uncategorized';
            }

            // Normalisasi huruf kapital di awal kata (beauty -> Beauty)
            $catDisplayName = ucwords(str_replace('-', ' ', $catName));

            if (!isset($categoryMap[$catDisplayName])) {
                $catStmtFind->execute(['name' => $catDisplayName]);
                $existingId = $catStmtFind->fetchColumn();

                if ($existingId !== false) {
                    $categoryMap[$catDisplayName] = (int) $existingId;
                } else {
                    $catStmtInsert->execute(['name' => $catDisplayName]);
                    $categoryMap[$catDisplayName] = (int) $this->pdo->lastInsertId();
                }
            }

            $categoryId = $categoryMap[$catDisplayName];
            // Assign supplier secara random dari list supplier yang sudah dibuat
            $supplierId = !empty($supplierIds) ? $supplierIds[array_rand($supplierIds)] : null;

            $sku = (string) ($p['sku'] ?? ('SKU-' . strtoupper(bin2hex(random_bytes(4)))));
            $name = (string) ($p['title'] ?? 'Unnamed Product');
            $price = (float) ($p['price'] ?? 0);
            $stock = (int) ($p['stock'] ?? rand(10, 100));

            $prodStmt->execute([
                'category_id' => $categoryId,
                'supplier_id' => $supplierId,
                'name'        => $name,
                'sku'         => $sku,
                'price'       => $price,
                'stock'       => $stock,
            ]);

            $productCount++;
        }

        $badge = "\033[1;32m[DONE]\033[0m";
        echo "  {$badge} Seeded " . count($categoryMap) . " categories.\n";
        echo "  {$badge} Seeded {$productCount} products.\n";

        return $categoryMap;
    }
}
