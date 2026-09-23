<div class="space-y-6">
  <!-- Page Header & Action Bar -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-bold text-primary tracking-tight">Manajemen Produk</h1>
      <p class="text-sm text-muted mt-1">Daftar inventaris lengkap dengan kategori dan pemasok (supplier).</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="/products/create" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg bg-brand text-white hover:bg-brand-hover shadow-xs focus:outline-hidden transition-colors">
        <iconify-icon icon="lucide:plus" class="size-4.5"></iconify-icon>
        <span>Tambah Produk</span>
      </a>
    </div>
  </div>

  <!-- Search & Filter Card -->
  <div class="bg-surface border border-border rounded-xl p-4 shadow-2xs">
    <form method="GET" action="/products" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
      <div class="relative flex-1">
        <span class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-muted">
          <iconify-icon icon="lucide:search" class="size-4.5"></iconify-icon>
        </span>
        <input 
          type="text" 
          name="q" 
          value="<?= htmlspecialchars($search) ?>" 
          placeholder="Cari berdasarkan nama produk, SKU, kategori, atau supplier..." 
          class="py-2 ps-10 pe-4 block w-full rounded-lg border border-border bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
        >
      </div>

      <div class="flex items-center gap-2">
        <button type="submit" class="py-2 px-4 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-brand text-white hover:bg-brand-hover focus:outline-hidden transition-colors cursor-pointer">
          <iconify-icon icon="lucide:search" class="size-4"></iconify-icon>
          <span>Cari</span>
        </button>

        <?php if ($search !== ''): ?>
          <a href="/products" class="py-2 px-3 inline-flex items-center justify-center gap-x-1.5 text-sm font-medium rounded-lg border border-border text-muted hover:text-primary hover:bg-hover focus:outline-hidden transition-colors" title="Reset Pencarian">
            <iconify-icon icon="lucide:x" class="size-4"></iconify-icon>
            <span class="hidden sm:inline">Reset</span>
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Products Table Card -->
  <div class="bg-surface border border-border rounded-xl shadow-2xs overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-border">
        <thead class="bg-hover/50 text-muted text-xs uppercase font-semibold">
          <tr>
            <th scope="col" class="py-3.5 px-4 text-left w-16">No</th>
            <th scope="col" class="py-3.5 px-4 text-left">SKU</th>
            <th scope="col" class="py-3.5 px-4 text-left">Nama Produk</th>
            <th scope="col" class="py-3.5 px-4 text-left">Kategori</th>
            <th scope="col" class="py-3.5 px-4 text-left">Supplier</th>
            <th scope="col" class="py-3.5 px-4 text-right">Harga</th>
            <th scope="col" class="py-3.5 px-4 text-center">Stok</th>
            <th scope="col" class="py-3.5 px-4 text-center w-28">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border text-sm">
          <?php if (empty($products)): ?>
            <tr>
              <td colspan="8" class="py-12 text-center text-muted">
                <div class="flex flex-col items-center justify-center gap-2">
                  <iconify-icon icon="lucide:package-open" class="size-10 text-muted/60"></iconify-icon>
                  <p class="font-medium text-base text-primary">Tidak ada produk ditemukan</p>
                  <p class="text-xs text-muted max-w-sm">
                    <?= $search !== '' ? 'Coba ubah kata kunci pencarian Anda.' : 'Belum ada data produk di sistem inventaris.' ?>
                  </p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php 
              $startIndex = ($currentPage - 1) * $perPage;
              foreach ($products as $idx => $product): 
            ?>
              <tr class="hover:bg-hover/40 transition-colors">
                <!-- No -->
                <td class="py-3 px-4 text-muted text-xs font-mono">
                  <?= $startIndex + $idx + 1 ?>
                </td>

                <!-- SKU -->
                <td class="py-3 px-4 font-mono text-xs text-primary font-medium whitespace-nowrap">
                  <?= htmlspecialchars($product->sku) ?>
                </td>

                <!-- Nama Produk -->
                <td class="py-3 px-4 font-medium text-primary max-w-xs truncate" title="<?= htmlspecialchars($product->name) ?>">
                  <?= htmlspecialchars($product->name) ?>
                </td>

                <!-- Kategori (JOIN 1) -->
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-medium bg-brand/10 text-brand border border-brand/20">
                    <?= htmlspecialchars($product->categoryName ?? 'Tanpa Kategori') ?>
                  </span>
                </td>

                <!-- Supplier (JOIN 2) -->
                <td class="py-3 px-4 text-muted whitespace-nowrap" title="<?= htmlspecialchars($product->supplierName ?? '-') ?>">
                  <span class="inline-flex items-center gap-1.5">
                    <iconify-icon icon="lucide:truck" class="size-3.5 text-muted shrink-0"></iconify-icon>
                    <span class="text-xs sm:text-sm"><?= htmlspecialchars($product->supplierName ?? '-') ?></span>
                  </span>
                </td>

                <!-- Harga -->
                <td class="py-3 px-4 text-right font-medium text-primary whitespace-nowrap">
                  <?= $product->getFormattedPrice() ?>
                </td>

                <!-- Stok -->
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  <?php if ($product->stock <= 0): ?>
                    <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                      Habis
                    </span>
                  <?php elseif ($product->stock < 10): ?>
                    <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                      <?= $product->stock ?> (Sisa sedikit)
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center py-0.5 px-2 rounded-md text-xs font-medium text-primary">
                      <?= $product->stock ?>
                    </span>
                  <?php endif; ?>
                </td>

                <!-- Aksi (Ikon Edit & Hapus Saja) -->
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  <div class="inline-flex items-center justify-center gap-1.5">
                    <!-- Tombol Edit (Ikon) -->
                    <a 
                      href="/products/edit?id=<?= $product->id ?>" 
                      class="size-8 inline-flex items-center justify-center rounded-lg border border-border text-primary hover:text-brand hover:border-brand/40 hover:bg-hover focus:outline-hidden transition-colors"
                      title="Edit Produk <?= htmlspecialchars($product->name) ?>"
                      aria-label="Edit Produk"
                    >
                      <iconify-icon icon="lucide:square-pen" class="size-4"></iconify-icon>
                    </a>

                    <!-- Tombol Hapus (Ikon) -->
                    <button 
                      type="button" 
                      class="btn-delete size-8 inline-flex items-center justify-center rounded-lg border border-border text-primary hover:text-rose-600 hover:border-rose-500/40 hover:bg-rose-500/10 focus:outline-hidden transition-colors cursor-pointer"
                      data-id="<?= $product->id ?>"
                      data-name="<?= htmlspecialchars($product->name) ?>"
                      title="Hapus Produk <?= htmlspecialchars($product->name) ?>"
                      aria-label="Hapus Produk"
                    >
                      <iconify-icon icon="lucide:trash-2" class="size-4"></iconify-icon>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Table Footer with Pagination -->
    <?php if ($total > 0): ?>
      <div class="border-t border-border px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-4 bg-surface">
        <p class="text-xs text-muted">
          Menampilkan <span class="font-semibold text-primary"><?= $startIndex + 1 ?></span> sampai <span class="font-semibold text-primary"><?= min($startIndex + count($products), $total) ?></span> dari <span class="font-semibold text-primary"><?= $total ?></span> produk
        </p>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
          <nav class="flex items-center -space-x-px rounded-lg shadow-2xs text-sm" aria-label="Pagination">
            <?php
              $searchQuery = $search !== '' ? '&q=' . urlencode($search) : '';
              $prevPage = max(1, $currentPage - 1);
              $nextPage = min($totalPages, $currentPage + 1);

              $delta = 2;
              $startPage = max(1, $currentPage - $delta);
              $endPage = min($totalPages, $currentPage + $delta);
            ?>

            <!-- Previous Button -->
            <?php if ($currentPage > 1): ?>
              <a href="/products?page=<?= $prevPage . $searchQuery ?>" class="min-h-9 min-w-9 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-s-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors" aria-label="Previous">
                <iconify-icon icon="lucide:chevron-left" class="size-4"></iconify-icon>
                <span class="hidden sm:inline">Sebelumnya</span>
              </a>
            <?php else: ?>
              <span class="min-h-9 min-w-9 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-s-lg border border-border text-muted opacity-40 cursor-not-allowed">
                <iconify-icon icon="lucide:chevron-left" class="size-4"></iconify-icon>
                <span class="hidden sm:inline">Sebelumnya</span>
              </span>
            <?php endif; ?>

            <!-- First page jump if not in range -->
            <?php if ($startPage > 1): ?>
              <a href="/products?page=1<?= $searchQuery ?>" class="min-h-9 min-w-9 flex justify-center items-center border border-border text-primary hover:bg-hover py-2 px-3 text-sm focus:outline-hidden transition-colors">1</a>
              <?php if ($startPage > 2): ?>
                <span class="min-h-9 min-w-9 flex justify-center items-center border border-border text-muted py-2 px-2 text-sm">...</span>
              <?php endif; ?>
            <?php endif; ?>

            <!-- Page Number Links -->
            <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
              <?php if ($p === $currentPage): ?>
                <span class="min-h-9 min-w-9 flex justify-center items-center border border-border bg-brand text-white font-semibold py-2 px-3 text-sm" aria-current="page">
                  <?= $p ?>
                </span>
              <?php else: ?>
                <a href="/products?page=<?= $p . $searchQuery ?>" class="min-h-9 min-w-9 flex justify-center items-center border border-border text-primary hover:bg-hover py-2 px-3 text-sm focus:outline-hidden transition-colors">
                  <?= $p ?>
                </a>
              <?php endif; ?>
            <?php endfor; ?>

            <!-- Last page jump if not in range -->
            <?php if ($endPage < $totalPages): ?>
              <?php if ($endPage < $totalPages - 1): ?>
                <span class="min-h-9 min-w-9 flex justify-center items-center border border-border text-muted py-2 px-2 text-sm">...</span>
              <?php endif; ?>
              <a href="/products?page=<?= $totalPages . $searchQuery ?>" class="min-h-9 min-w-9 flex justify-center items-center border border-border text-primary hover:bg-hover py-2 px-3 text-sm focus:outline-hidden transition-colors">
                <?= $totalPages ?>
              </a>
            <?php endif; ?>

            <!-- Next Button -->
            <?php if ($currentPage < $totalPages): ?>
              <a href="/products?page=<?= $nextPage . $searchQuery ?>" class="min-h-9 min-w-9 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-e-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors" aria-label="Next">
                <span class="hidden sm:inline">Berikutnya</span>
                <iconify-icon icon="lucide:chevron-right" class="size-4"></iconify-icon>
              </a>
            <?php else: ?>
              <span class="min-h-9 min-w-9 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-e-lg border border-border text-muted opacity-40 cursor-not-allowed">
                <span class="hidden sm:inline">Berikutnya</span>
                <iconify-icon icon="lucide:chevron-right" class="size-4"></iconify-icon>
              </span>
            <?php endif; ?>
          </nav>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Konfirmasi Hapus Produk -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs items-center justify-center p-4 transition-opacity duration-200" role="dialog" aria-modal="true">
  <div class="m-auto bg-surface border border-border rounded-2xl max-w-md w-full p-6 shadow-xl relative animate-in fade-in zoom-in-95 duration-150">
    <div class="flex items-center gap-3 text-rose-600 mb-3">
      <span class="size-10 rounded-full bg-rose-500/10 flex items-center justify-center shrink-0">
        <iconify-icon icon="lucide:alert-triangle" class="size-5"></iconify-icon>
      </span>
      <h3 class="text-lg font-bold text-primary">Konfirmasi Hapus</h3>
    </div>

    <p class="text-sm text-muted mb-6">
      Apakah Anda yakin ingin menghapus produk <strong id="delete-product-name" class="text-primary font-semibold"></strong>? Tindakan ini permanen dan tidak dapat dibatalkan.
    </p>

    <form method="POST" action="/products/delete" class="flex items-center justify-end gap-3">
      <input type="hidden" name="id" id="delete-product-id" value="">
      
      <button 
        type="button" 
        id="btn-cancel-delete" 
        class="py-2 px-4 inline-flex items-center justify-center text-sm font-medium rounded-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors cursor-pointer"
      >
        Batal
      </button>

      <button 
        type="submit" 
        class="py-2 px-4 inline-flex items-center justify-center gap-x-1.5 text-sm font-semibold rounded-lg bg-rose-600 text-white hover:bg-rose-700 shadow-xs focus:outline-hidden transition-colors cursor-pointer"
      >
        <iconify-icon icon="lucide:trash-2" class="size-4"></iconify-icon>
        <span>Ya, Hapus</span>
      </button>
    </form>
  </div>
</div>

<!-- Inisialisasi Script Modal Hapus -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('delete-modal');
    const deleteIdInput = document.getElementById('delete-product-id');
    const deleteNameSpan = document.getElementById('delete-product-name');
    const cancelBtn = document.getElementById('btn-cancel-delete');

    function openModal(id, name) {
      if (!modal) return;
      deleteIdInput.value = id;
      deleteNameSpan.textContent = `"${name}"`;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeModal() {
      if (!modal) return;
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      deleteIdInput.value = '';
    }

    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        openModal(id, name);
      });
    });

    cancelBtn?.addEventListener('click', closeModal);

    modal?.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeModal();
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !modal?.classList.contains('hidden')) {
        closeModal();
      }
    });
  });
</script>
