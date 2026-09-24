<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Realitaa\PhpVite\Controllers\ProductController;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

$productController = new ProductController();

switch ($path) {
    case '/':
    case '/index.php':
        $productController->home();
        break;

    case '/products':
        if ($method === 'POST') {
            $productController->store();
        } else {
            $productController->index();
        }
        break;

    case '/products/export':
        $productController->export();
        break;

    case '/products/create':
        if ($method === 'POST') {
            $productController->store();
        } else {
            $productController->create();
        }
        break;

    case '/products/edit':
        if ($method === 'POST') {
            $productController->update();
        } else {
            $productController->edit();
        }
        break;

    case '/products/delete':
        if ($method === 'POST') {
            $productController->destroy();
        } else {
            redirect('/products');
        }
        break;

    default:
        http_response_code(404);
        view('404', [
            'title' => '404 - Halaman Tidak Ditemukan',
        ], null);
        break;
}
