<?php
  // Nilai aktif: utamakan $old jika ada validation error, jika tidak gunakan data model $product
  $valName = $old['name'] ?? $product->name;
  $valSku = $old['sku'] ?? $product->sku;
  $valCatId = isset($old['category_id']) ? (string)$old['category_id'] : (string)($product->categoryId ?? '');
  $valSupId = isset($old['supplier_id']) ? (string)$old['supplier_id'] : (string)($product->supplierId ?? '');
  $valPrice = isset($old['price']) ? $old['price'] : $product->price;
  $valStock = isset($old['stock']) ? $old['stock'] : $product->stock;
  $valDescription = $old['description'] ?? $product->description ?? '';
  $valDiscount = isset($old['discount_percentage']) ? $old['discount_percentage'] : ($product->discountPercentage !== null ? (string)$product->discountPercentage : '');
  $valRating = isset($old['rating']) ? $old['rating'] : ($product->rating !== null ? (string)$product->rating : '');
  $valThumbnail = $old['thumbnail'] ?? $product->thumbnail ?? '';
?>

<div class="max-w-2xl mx-auto space-y-6">
  <!-- Header -->
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-primary tracking-tight">Edit Produk</h1>
      <p class="text-sm text-muted mt-1">Perbarui data produk <strong class="text-primary"><?= e($product->name) ?></strong>.</p>
    </div>
    <a href="/products" class="py-2 px-3.5 inline-flex items-center gap-x-1.5 text-sm font-medium rounded-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors">
      <iconify-icon icon="lucide:arrow-left" class="size-4"></iconify-icon>
      <span>Kembali</span>
    </a>
  </div>

  <!-- Form Card -->
  <div class="bg-surface border border-border rounded-xl p-6 shadow-2xs">
    <form method="POST" action="/products/edit" class="space-y-5">
      <input type="hidden" name="id" value="<?= $product->id ?>">

      <!-- Nama Produk -->
      <div>
        <label for="name" class="block text-sm font-semibold text-primary mb-1.5">
          Nama Produk <span class="text-rose-500">*</span>
        </label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          value="<?= e((string)$valName) ?>" 
          placeholder="Contoh: Wireless Bluetooth Headset" 
          class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
          required
        >
        <?php if (isset($errors['name'])): ?>
          <p class="text-xs text-rose-500 mt-1"><?= e($errors['name']) ?></p>
        <?php endif; ?>
      </div>

      <!-- SKU -->
      <div>
        <label for="sku" class="block text-sm font-semibold text-primary mb-1.5">
          SKU (Kode Unik Produk) <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <input 
            type="text" 
            id="sku" 
            name="sku" 
            value="<?= e((string)$valSku) ?>" 
            placeholder="Contoh: ELE-BLU-HEA-001" 
            class="py-2.5 px-3.5 block w-full font-mono rounded-lg border <?= isset($errors['sku']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden uppercase transition-colors"
            required
          >
        </div>
        <p class="text-xs text-muted mt-1">Hanya huruf besar, angka, dan strip (-). Harus unik.</p>
        <?php if (isset($errors['sku'])): ?>
          <p class="text-xs text-rose-500 mt-1"><?= e($errors['sku']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Kategori & Supplier (Grid 2 Kolom) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Dropdown Kategori -->
        <div>
          <label for="category_id" class="block text-sm font-semibold text-primary mb-1.5">
            Kategori Produk
          </label>
          <div class="relative">
            <select 
              id="category_id" 
              name="category_id" 
              class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['category_id']) ? 'border-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors cursor-pointer"
            >
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($categories as $cat): ?>
                <?php $selected = $valCatId === (string)$cat->id ? 'selected' : ''; ?>
                <option value="<?= $cat->id ?>" <?= $selected ?>>
                  <?= e($cat->name) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php if (isset($errors['category_id'])): ?>
            <p class="text-xs text-rose-500 mt-1"><?= e($errors['category_id']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Dropdown Supplier -->
        <div>
          <label for="supplier_id" class="block text-sm font-semibold text-primary mb-1.5">
            Pemasok (Supplier)
          </label>
          <div class="relative">
            <select 
              id="supplier_id" 
              name="supplier_id" 
              class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['supplier_id']) ? 'border-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors cursor-pointer"
            >
              <option value="">-- Pilih Supplier --</option>
              <?php foreach ($suppliers as $sup): ?>
                <?php $selected = $valSupId === (string)$sup->id ? 'selected' : ''; ?>
                <option value="<?= $sup->id ?>" <?= $selected ?>>
                  <?= e($sup->name) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php if (isset($errors['supplier_id'])): ?>
            <p class="text-xs text-rose-500 mt-1"><?= e($errors['supplier_id']) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Harga & Stok (Grid 2 Kolom) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Harga -->
        <div>
          <label for="price" class="block text-sm font-semibold text-primary mb-1.5">
            Harga Satuan (Rp) <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute inset-y-0 inset-s-0 flex items-center ps-3.5 pointer-events-none text-muted text-sm font-semibold">
              Rp
            </span>
            <input 
              type="number" 
              id="price" 
              name="price" 
              step="1" 
              min="0"
              value="<?= e((string)$valPrice) ?>" 
              placeholder="0" 
              class="py-2.5 ps-10 pe-3.5 block w-full rounded-lg border <?= isset($errors['price']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
              required
            >
          </div>
          <?php if (isset($errors['price'])): ?>
            <p class="text-xs text-rose-500 mt-1"><?= e($errors['price']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Stok -->
        <div>
          <label for="stock" class="block text-sm font-semibold text-primary mb-1.5">
            Jumlah Stok <span class="text-rose-500">*</span>
          </label>
          <input 
            type="number" 
            id="stock" 
            name="stock" 
            step="1" 
            min="0"
            value="<?= e((string)$valStock) ?>" 
            placeholder="0" 
            class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['stock']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
            required
          >
          <?php if (isset($errors['stock'])): ?>
            <p class="text-xs text-rose-500 mt-1"><?= e($errors['stock']) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Diskon & Rating (Grid 2 Kolom) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Diskon -->
        <div>
          <label for="discount_percentage" class="block text-sm font-semibold text-primary mb-1.5">
            Diskon (%) <span class="text-xs font-normal text-muted">(Opsional, 1-100)</span>
          </label>
          <div class="relative">
            <input 
              type="number" 
              id="discount_percentage" 
              name="discount_percentage" 
              step="0.01" 
              min="1" 
              max="100"
              value="<?= e((string)$valDiscount) ?>" 
              placeholder="Contoh: 10.5" 
              class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['discount_percentage']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
            >
          </div>
          <?php if (isset($errors['discount_percentage'])): ?>
            <p class="text-xs text-rose-500 mt-1"><?= e($errors['discount_percentage']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Rating -->
        <div>
          <label for="rating" class="block text-sm font-semibold text-primary mb-1.5">
            Rating Produk <span class="text-xs font-normal text-muted">(Opsional, maks 5.00)</span>
          </label>
          <input 
            type="number" 
            id="rating" 
            name="rating" 
            step="0.01" 
            min="0" 
            max="5"
            value="<?= e((string)$valRating) ?>" 
            placeholder="Contoh: 4.85" 
            class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['rating']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
          >
          <?php if (isset($errors['rating'])): ?>
            <p class="text-xs text-rose-500 mt-1"><?= e($errors['rating']) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Link Gambar & Preview Reaktif -->
      <div>
        <label for="thumbnail" class="block text-sm font-semibold text-primary mb-1.5">
          Link Gambar Produk <span class="text-xs font-normal text-muted">(Opsional, URL gambar)</span>
        </label>
        <div class="relative">
          <input 
            type="text" 
            id="thumbnail" 
            name="thumbnail" 
            value="<?= e((string)$valThumbnail) ?>" 
            placeholder="https://example.com/images/product.jpg" 
            class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['thumbnail']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
          >
        </div>
        <?php if (isset($errors['thumbnail'])): ?>
          <p class="text-xs text-rose-500 mt-1"><?= e($errors['thumbnail']) ?></p>
        <?php endif; ?>

        <!-- Reaktif Image Preview Box -->
        <div id="image-preview-wrapper" class="mt-3 p-3.5 rounded-lg border border-dashed border-border bg-canvas/50">
          <div id="image-preview-empty" class="flex flex-col items-center justify-center py-5 text-center text-muted">
            <iconify-icon icon="lucide:image" class="size-8 mb-1.5 opacity-40"></iconify-icon>
            <span class="text-xs">Preview gambar akan otomatis muncul saat URL dimasukkan</span>
          </div>

          <div id="image-preview-active" class="hidden flex items-center gap-3.5">
            <div class="relative w-20 h-20 rounded-lg overflow-hidden border border-border bg-surface shrink-0 flex items-center justify-center">
              <img id="image-preview-img" src="" alt="Preview Gambar" class="w-full h-full object-contain" />
              <div id="image-preview-loading" class="hidden absolute inset-0 bg-surface/80 flex items-center justify-center">
                <iconify-icon icon="lucide:loader-2" class="size-5 text-brand animate-spin"></iconify-icon>
              </div>
            </div>
            <div class="space-y-1 min-w-0 flex-1">
              <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                <iconify-icon icon="lucide:check-circle" class="size-3.5"></iconify-icon>
                <span>Gambar berhasil dimuat</span>
              </p>
              <p id="image-preview-url" class="text-xs text-muted truncate font-mono"></p>
            </div>
          </div>

          <div id="image-preview-error" class="hidden flex items-center gap-2 py-2 px-3 text-rose-500 text-xs bg-rose-500/10 rounded-md border border-rose-500/20">
            <iconify-icon icon="lucide:alert-circle" class="size-4 shrink-0"></iconify-icon>
            <span>Gagal memuat gambar dari URL. Pastikan link gambar dapat diakses secara publik.</span>
          </div>
        </div>
      </div>

      <!-- Deskripsi Produk (Textarea) -->
      <div>
        <label for="description" class="block text-sm font-semibold text-primary mb-1.5">
          Deskripsi Produk <span class="text-xs font-normal text-muted">(Opsional)</span>
        </label>
        <textarea 
          id="description" 
          name="description" 
          rows="3" 
          placeholder="Tuliskan spesifikasi, keunggulan, atau catatan produk ini..." 
          class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['description']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
        ><?= e((string)$valDescription) ?></textarea>
        <?php if (isset($errors['description'])): ?>
          <p class="text-xs text-rose-500 mt-1"><?= e($errors['description']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Action Buttons -->
      <div class="pt-4 border-t border-border flex items-center justify-end gap-3">
        <a href="/products" class="py-2.5 px-4 inline-flex items-center justify-center text-sm font-medium rounded-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors">
          Batal
        </a>
        <button type="submit" class="py-2.5 px-5 inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg bg-brand text-white hover:bg-brand-hover shadow-xs focus:outline-hidden transition-colors cursor-pointer">
          <iconify-icon icon="lucide:save" class="size-4"></iconify-icon>
          <span>Perbarui Produk</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
(function() {
  const input = document.getElementById('thumbnail');
  const emptyState = document.getElementById('image-preview-empty');
  const activeState = document.getElementById('image-preview-active');
  const errorState = document.getElementById('image-preview-error');
  const previewImg = document.getElementById('image-preview-img');
  const previewUrl = document.getElementById('image-preview-url');
  const loadingState = document.getElementById('image-preview-loading');

  function updatePreview(url) {
    const trimmed = (url || '').trim();
    if (!trimmed) {
      emptyState?.classList.remove('hidden');
      activeState?.classList.add('hidden');
      errorState?.classList.add('hidden');
      if (previewImg) previewImg.src = '';
      return;
    }

    emptyState?.classList.add('hidden');
    errorState?.classList.add('hidden');
    activeState?.classList.remove('hidden');
    loadingState?.classList.remove('hidden');
    if (previewUrl) previewUrl.textContent = trimmed;

    const testImg = new Image();
    testImg.onload = function() {
      loadingState?.classList.add('hidden');
      if (previewImg) previewImg.src = trimmed;
    };
    testImg.onerror = function() {
      loadingState?.classList.add('hidden');
      activeState?.classList.add('hidden');
      errorState?.classList.remove('hidden');
    };
    testImg.src = trimmed;
  }

  if (input) {
    input.addEventListener('input', function() {
      updatePreview(this.value);
    });
    if (input.value.trim() !== '') {
      updatePreview(input.value);
    }
  }
})();
</script>
