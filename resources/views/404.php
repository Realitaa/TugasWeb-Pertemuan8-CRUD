<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 - Halaman Tidak Ditemukan</title>
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
  <body class="bg-canvas text-primary min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-md">
      <h1 class="text-6xl font-bold text-brand mb-4">404</h1>
      <h2 class="text-2xl font-bold mb-2">Halaman Tidak Ditemukan</h2>
      <p class="text-muted mb-6">Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.</p>
      <a href="/" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-brand text-white font-medium hover:bg-brand-hover transition-colors">
        Kembali ke Beranda
      </a>
    </div>
  </body>
</html>
