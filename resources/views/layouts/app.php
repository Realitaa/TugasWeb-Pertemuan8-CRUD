<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($title ?? 'RealCommerce - Sistem Inventaris & Katalog') ?></title>
    <meta name="description" content="Sistem Manajemen Produk dan Katalog Online RealCommerce" />
    <meta name="robots" content="index, follow" />

    <!-- Google Fonts Preconnect & Async Load -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet" />

    <?php
    try {
        echo vite()->tags('resources/js/app.js');
    } catch (\RuntimeException $e) {
        if (str_contains($e->getMessage(), 'not found in manifest')) {
            echo vite()->tags('src/main.js');
        } else {
            throw $e;
        }
    }
    ?>
  </head>
  <body class="bg-canvas text-primary min-h-screen flex flex-col transition-colors duration-200">
    <div id="app"></div>

    <?php $flash = get_flash(); ?>
    <?php if ($flash): ?>
      <div id="flash-message" 
           data-type="<?= e($flash['type']) ?>" 
           data-message="<?= e($flash['message']) ?>" 
           class="hidden"></div>
    <?php endif; ?>

    <!-- Global Header -->
    <header class="sticky top-0 z-40 flex flex-wrap sm:justify-start sm:flex-nowrap w-full py-3 bg-surface/95 backdrop-blur-md border-b border-border transition-colors duration-300">
      <nav class="max-w-340 w-full mx-auto px-4 flex items-center justify-between gap-4">
        <!-- Brand Logo -->
        <a class="flex items-center gap-2 text-xl font-bold text-primary focus:outline-hidden hover:opacity-85 transition-opacity" href="/" aria-label="Brand">
          <span class="size-8 rounded-lg bg-brand text-white flex items-center justify-center shadow-xs">
            <iconify-icon icon="lucide:shopping-bag" class="size-4.5"></iconify-icon>
          </span>
          <span>RealCommerce</span>
        </a>

        <!-- Right Side Nav Actions -->
        <div class="flex items-center gap-2 sm:gap-3">
          <?php if (($activeNav ?? '') === 'products'): ?>
            <a href="/" class="py-1.5 px-3 inline-flex items-center gap-x-1.5 text-sm font-medium rounded-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors">
              <iconify-icon icon="lucide:store" class="size-4 text-muted"></iconify-icon>
              <span>Katalog</span>
            </a>
          <?php else: ?>
            <a href="/products" class="py-1.5 px-3.5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-brand text-white hover:bg-brand-hover shadow-xs focus:outline-hidden transition-colors">
              <iconify-icon icon="lucide:layout-dashboard" class="size-4"></iconify-icon>
              <span>Manajemen</span>
            </a>
          <?php endif; ?>

          <!-- Theme Switcher -->
          <button id="theme-toggle-btn" type="button" class="size-9 flex justify-center items-center rounded-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors" aria-label="Toggle theme">
            <span id="theme-toggle-icon" class="size-4 flex items-center justify-center">
              <iconify-icon icon="lucide:sun" class="size-4"></iconify-icon>
            </span>
          </button>

          <!-- User Profile -->
          <div class="hidden sm:flex items-center gap-x-2.5 ps-2 border-s border-border">
            <span class="text-sm font-semibold text-primary" init-name>Reza</span>
            <img class="inline-block size-8 rounded-full ring-2 ring-ring object-cover" src="https://github.com/Realitaa.png" alt="Avatar">
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1 max-w-340 w-full mx-auto px-4 py-8">
      <?= $slot ?>
    </main>

    <!-- Global Footer -->
    <footer class="mt-auto border-t border-border bg-surface py-6 transition-colors duration-300">
      <div class="max-w-340 mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-muted text-center sm:text-left">
        <p class="inline-flex flex-wrap items-center justify-center sm:justify-start gap-1.5">
          <iconify-icon icon="lucide:copyright" class="size-3.5 shrink-0"></iconify-icon>
          <span>2026 RealCommerce. Dibuat oleh</span>
          <span class="font-semibold text-primary" init-fullname>Reza Mulia Putra</span>
        </p>
        <p class="text-xs sm:text-sm">Sistem Inventaris Produk & Katalog Belanja Online</p>
      </div>
    </footer>
  </body>
</html>
