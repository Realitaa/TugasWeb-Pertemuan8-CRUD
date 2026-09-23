<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Controllers;

use Realitaa\PhpVite\Services\ProductService;

class ProductController
{
    public function __construct(
        protected ProductService $productService = new ProductService(),
    ) {}

    /**
     * Display listing of products.
     */
    public function index(): void
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $search = isset($_GET['q']) ? trim((string) $_GET['q']) : null;
        $perPage = 10;

        $result = $this->productService->getPaginatedProducts($page, $perPage, $search);

        view('products/index', [
            'title'       => 'Manajemen Produk - RealCommerce',
            'activeNav'   => 'products',
            'products'    => $result['items'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'perPage'     => $result['perPage'],
            'totalPages'  => $result['totalPages'],
            'search'      => $search ?? '',
        ]);
    }

    /**
     * Show form for creating a new product.
     */
    public function create(): void
    {
        $formData = $this->productService->getFormData();

        view('products/create', [
            'title'      => 'Tambah Produk Baru - RealCommerce',
            'activeNav'  => 'products',
            'categories' => $formData['categories'],
            'suppliers'  => $formData['suppliers'],
            'errors'     => [],
            'old'        => [],
        ]);
    }

    /**
     * Store newly created product.
     */
    public function store(): void
    {
        $result = $this->productService->createProduct($_POST);

        if ($result['success']) {
            flash('success', "Produk \"{$result['product']->name}\" berhasil ditambahkan!");
            redirect('/products');
        }

        flash('error', 'Gagal menambahkan produk. Silakan periksa kembali formulir.');
        $formData = $this->productService->getFormData();

        view('products/create', [
            'title'      => 'Tambah Produk Baru - RealCommerce',
            'activeNav'  => 'products',
            'categories' => $formData['categories'],
            'suppliers'  => $formData['suppliers'],
            'errors'     => $result['errors'],
            'old'        => $_POST,
        ]);
    }

    /**
     * Show form for editing product.
     */
    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $product = $this->productService->getProductById($id);

        if ($product === null) {
            flash('error', 'Produk tidak ditemukan.');
            redirect('/products');
        }

        $formData = $this->productService->getFormData();

        view('products/edit', [
            'title'      => "Edit Produk: {$product->name} - RealCommerce",
            'activeNav'  => 'products',
            'product'    => $product,
            'categories' => $formData['categories'],
            'suppliers'  => $formData['suppliers'],
            'errors'     => [],
        ]);
    }

    /**
     * Update specified product.
     */
    public function update(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $result = $this->productService->updateProduct($id, $_POST);

        if ($result['success']) {
            flash('success', "Produk \"{$result['product']->name}\" berhasil diperbarui!");
            redirect('/products');
        }

        flash('error', 'Gagal memperbarui produk. Silakan periksa kesalahan pada formulir.');
        $product = $this->productService->getProductById($id);
        $formData = $this->productService->getFormData();

        view('products/edit', [
            'title'      => 'Edit Produk - RealCommerce',
            'activeNav'  => 'products',
            'product'    => $product,
            'categories' => $formData['categories'],
            'suppliers'  => $formData['suppliers'],
            'errors'     => $result['errors'],
            'old'        => $_POST,
        ]);
    }

    /**
     * Delete specified product.
     */
    public function destroy(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $product = $this->productService->getProductById($id);

        if ($product === null) {
            flash('error', 'Produk yang ingin dihapus tidak ditemukan.');
            redirect('/products');
        }

        $productName = $product->name;
        $this->productService->deleteProduct($id);

        flash('success', "Produk \"{$productName}\" berhasil dihapus!");
        redirect('/products');
    }
}
