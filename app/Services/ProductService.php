<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Services;

use InvalidArgumentException;
use Realitaa\PhpVite\Models\Category;
use Realitaa\PhpVite\Models\Product;
use Realitaa\PhpVite\Models\Supplier;
use Realitaa\PhpVite\Repositories\CategoryRepository;
use Realitaa\PhpVite\Repositories\ProductRepository;
use Realitaa\PhpVite\Repositories\SupplierRepository;
use RuntimeException;
use Throwable;

class ProductService
{
    public function __construct(
        protected ProductRepository $productRepo = new ProductRepository(),
        protected CategoryRepository $categoryRepo = new CategoryRepository(),
        protected SupplierRepository $supplierRepo = new SupplierRepository(),
    ) {}

    /**
     * Get paginated products for catalog or admin listing.
     *
     * @return array{items: array<Product>, total: int, currentPage: int, perPage: int, totalPages: int}
     */
    public function getPaginatedProducts(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        return $this->productRepo->paginate($page, $perPage, $search);
    }

    /**
     * Find product by ID.
     */
    public function getProductById(int $id): ?Product
    {
        return $this->productRepo->findById($id);
    }

    /**
     * Get categories and suppliers for dropdown options in create/edit forms.
     *
     * @return array{categories: array<Category>, suppliers: array<Supplier>}
     */
    public function getFormData(): array
    {
        return [
            'categories' => $this->categoryRepo->all(),
            'suppliers'  => $this->supplierRepo->all(),
        ];
    }

    /**
     * Validate product input and create new product.
     *
     * @param array<string, mixed> $input
     * @return array{success: bool, product: ?Product, errors: array<string, string>}
     */
    public function createProduct(array $input): array
    {
        $errors = $this->validateProductData($input);

        if (!empty($errors)) {
            return [
                'success' => false,
                'product' => null,
                'errors'  => $errors,
            ];
        }

        $product = Product::fromArray($input);
        $newId = $this->productRepo->create($product);
        $product->id = $newId;

        return [
            'success' => true,
            'product' => $product,
            'errors'  => [],
        ];
    }

    /**
     * Validate product input and update existing product.
     *
     * @param int $id
     * @param array<string, mixed> $input
     * @return array{success: bool, product: ?Product, errors: array<string, string>}
     */
    public function updateProduct(int $id, array $input): array
    {
        $existing = $this->productRepo->findById($id);
        if ($existing === null) {
            return [
                'success' => false,
                'product' => null,
                'errors'  => ['general' => 'Produk tidak ditemukan.'],
            ];
        }

        $errors = $this->validateProductData($input, $id);

        if (!empty($errors)) {
            return [
                'success' => false,
                'product' => null,
                'errors'  => $errors,
            ];
        }

        $input['id'] = $id;
        $product = Product::fromArray($input);
        $this->productRepo->update($product);

        return [
            'success' => true,
            'product' => $this->productRepo->findById($id),
            'errors'  => [],
        ];
    }

    /**
     * Delete product by ID within a database transaction and write log to storage/logs/database.log.
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepo->findById($id);
        if ($product === null) {
            return false;
        }

        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        $logFile = $logDir . '/database.log';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $this->productRepo->beginTransaction();

        try {
            $deleted = $this->productRepo->delete($id);
            if (!$deleted) {
                throw new RuntimeException("Gagal menghapus data produk dengan ID {$id} dari database.");
            }

            // Tulis entri log transaksi ke storage/logs/database.log
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = sprintf(
                "[%s] [TRANSACTION_COMMITTED] Action: DELETE_PRODUCT | ID: %d | SKU: %s | Name: \"%s\" | Price: %s | Stock: %d | Category: \"%s\" | Supplier: \"%s\"" . PHP_EOL,
                $timestamp,
                $product->id,
                $product->sku,
                $product->name,
                $product->getFormattedPrice(),
                $product->stock,
                $product->categoryName ?? '-',
                $product->supplierName ?? '-'
            );

            $writeResult = file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
            if ($writeResult === false) {
                throw new RuntimeException("Gagal menulis log transaksi ke file [{$logFile}].");
            }

            $this->productRepo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->productRepo->inTransaction()) {
                $this->productRepo->rollBack();
            }

            // Catat kegagalan / rollback ke storage/logs/database.log
            $timestamp = date('Y-m-d H:i:s');
            $errorLog = sprintf(
                "[%s] [TRANSACTION_ROLLBACK] Action: DELETE_PRODUCT | ID: %d | Error: %s" . PHP_EOL,
                $timestamp,
                $id,
                $e->getMessage()
            );
            @file_put_contents($logFile, $errorLog, FILE_APPEND | LOCK_EX);

            return false;
        }
    }

    /**
     * Validate raw input data.
     *
     * @param array<string, mixed> $data
     * @param int|null $excludeId For SKU uniqueness check
     * @return array<string, string>
     */
    protected function validateProductData(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            $errors['name'] = 'Nama produk wajib diisi.';
        } elseif (mb_strlen($name) < 3) {
            $errors['name'] = 'Nama produk minimal 3 karakter.';
        }

        $sku = strtoupper(trim((string) ($data['sku'] ?? '')));
        if ($sku === '') {
            $errors['sku'] = 'SKU produk wajib diisi.';
        } elseif (!preg_match('/^[A-Z0-9\-_]+$/i', $sku)) {
            $errors['sku'] = 'SKU hanya boleh berisi huruf, angka, tanda strip (-) dan garis bawah (_).';
        } elseif (!$this->productRepo->isSkuUnique($sku, $excludeId)) {
            $errors['sku'] = "SKU '{$sku}' sudah digunakan oleh produk lain.";
        }

        if (!isset($data['price']) || $data['price'] === '') {
            $errors['price'] = 'Harga produk wajib diisi.';
        } elseif (!is_numeric($data['price']) || (float) $data['price'] < 0) {
            $errors['price'] = 'Harga produk harus berupa angka positif.';
        }

        if (!isset($data['stock']) || $data['stock'] === '') {
            $errors['stock'] = 'Stok produk wajib diisi.';
        } elseif (filter_var($data['stock'], FILTER_VALIDATE_INT) === false || (int) $data['stock'] < 0) {
            $errors['stock'] = 'Stok produk harus berupa bilangan bulat positif atau 0.';
        }

        return $errors;
    }
}
