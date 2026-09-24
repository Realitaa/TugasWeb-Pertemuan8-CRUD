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
     * Display catalog home page with products from database.
     */
    public function home(): void
    {
        $products = $this->productService->getAllProducts(null, 'ASC');

        $productsData = array_map(function ($p) {
            return [
                'id'                 => $p->id,
                'title'              => $p->name,
                'name'               => $p->name,
                'sku'                => $p->sku,
                'price'              => $p->price,
                'stock'              => $p->stock,
                'description'        => $p->description ?? '',
                'discountPercentage' => $p->discountPercentage ?? 0,
                'rating'             => $p->rating,
                'thumbnail'          => $p->thumbnail,
                'category'           => $p->categoryName ?? 'Umum',
                'supplier'           => $p->supplierName ?? '-',
            ];
        }, $products);

        view('home', [
            'title'        => 'RealCommerce - Katalog Produk Belanja Online Responsif',
            'activeNav'    => 'home',
            'productsData' => $productsData,
        ]);
    }

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
     * Export products to CSV file.
     */
    public function export(): void
    {
        $search = isset($_GET['q']) ? trim((string) $_GET['q']) : null;
        $csvContent = $this->productService->exportProductsCsv($search);

        $filename = 'laporan-produk-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $csvContent;
        exit(0);
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
        $deleted = $this->productService->deleteProduct($id);

        if ($deleted) {
            flash('success', "Produk \"{$productName}\" berhasil dihapus!");
        } else {
            flash('error', "Gagal menghapus produk \"{$productName}\". Transaksi dibatalkan.");
        }

        redirect('/products');
    }
}
