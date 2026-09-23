<?php

declare(strict_types=1);

if (!function_exists('view')) {
    /**
     * Render a view within a layout.
     *
     * @param string $view Relative view file path inside resources/views (without .php)
     * @param array<string, mixed> $data Variables to pass to the view and layout
     * @param string|null $layout Layout file path or null for raw view
     */
    function view(string $view, array $data = [], ?string $layout = 'layouts/app'): void
    {
        extract($data, EXTR_SKIP);

        $root = dirname(__DIR__, 2);
        $viewFile = $root . '/resources/views/' . ltrim($view, '/') . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file [{$viewFile}] not found.");
        }

        if ($layout === null) {
            require $viewFile;
            return;
        }

        $layoutFile = $root . '/resources/views/' . ltrim($layout, '/') . '.php';
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Layout file [{$layoutFile}] not found.");
        }

        // Tangkap output view ke dalam $slot
        ob_start();
        require $viewFile;
        $slot = ob_get_clean();

        // Render layout utama bersama $slot
        require $layoutFile;
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Indonesian Rupiah currency string.
     */
    function format_rupiah(float|int|string $amount): string
    {
        $num = (float) $amount;
        return 'Rp ' . number_format($num, 0, ',', '.');
    }
}

if (!function_exists('flash')) {
    /**
     * Set a flash message in the session.
     */
    function flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('get_flash')) {
    /**
     * Get and clear the flash message from session.
     *
     * @return array{type: string, message: string}|null
     */
    function get_flash(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }

        return null;
    }
}

if (!function_exists('redirect')) {
    /**
     * Send HTTP redirect header and terminate.
     */
    function redirect(string $url): void
    {
        header("Location: {$url}");
        exit(0);
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     */
    function e(mixed $value, bool $doubleEncode = true): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}

