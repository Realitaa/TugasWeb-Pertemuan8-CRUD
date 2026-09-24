<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Controllers;

use Realitaa\PhpVite\Services\DatabaseResetService;

class DatabaseResetController
{
    public function __construct(
        protected DatabaseResetService $resetService = new DatabaseResetService(),
    ) {}

    /**
     * Handle POST request to reset database with fresh migrations and seeding.
     */
    public function handle(): void
    {
        // Parameter key dari form POST, JSON payload, atau fallback query
        $inputKey = $_POST['key'] ?? null;

        if ($inputKey === null) {
            $rawInput = file_get_contents('php://input');
            if ($rawInput) {
                $data = json_decode($rawInput, true);
                if (is_array($data) && isset($data['key'])) {
                    $inputKey = (string) $data['key'];
                }
            }
        }

        if ($inputKey === null && isset($_GET['key'])) {
            $inputKey = (string) $_GET['key'];
        }

        // Jika kunci tidak valid, kembalikan 403 Forbidden tanpa informasi lebih lanjut
        if (!$this->resetService->isValidKey($inputKey)) {
            http_response_code(403);
            exit;
        }

        // Jalankan migrasi database fresh dengan seed
        $this->resetService->resetAndSeed();

        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Database berhasil di-reset dan di-seed ulang.',
        ]);
        exit;
    }
}
