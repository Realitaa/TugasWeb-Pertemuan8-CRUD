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
     * Delete product by ID.
     */
    public function deleteProduct(int $id): bool
    {
        return $this->productRepo->delete($id);
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
