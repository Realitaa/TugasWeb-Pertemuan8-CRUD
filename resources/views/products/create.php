<div class="max-w-2xl mx-auto space-y-6">
  <!-- Header -->
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-primary tracking-tight">Tambah Produk Baru</h1>
      <p class="text-sm text-muted mt-1">Masukkan rincian informasi produk baru ke dalam sistem inventaris.</p>
    </div>
    <a href="/products" class="py-2 px-3.5 inline-flex items-center gap-x-1.5 text-sm font-medium rounded-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors">
      <iconify-icon icon="lucide:arrow-left" class="size-4"></iconify-icon>
      <span>Kembali</span>
    </a>
  </div>

  <!-- Form Card -->
  <div class="bg-surface border border-border rounded-xl p-6 shadow-2xs">
    <form method="POST" action="/products/create" class="space-y-5">
      <!-- Nama Produk -->
      <div>
        <label for="name" class="block text-sm font-semibold text-primary mb-1.5">
          Nama Produk <span class="text-rose-500">*</span>
        </label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          value="<?= e($old['name'] ?? '') ?>" 
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
            value="<?= e($old['sku'] ?? '') ?>" 
            placeholder="Contoh: ELE-BLU-HEA-001" 
            class="py-2.5 px-3.5 block w-full font-mono rounded-lg border <?= isset($errors['sku']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden uppercase transition-colors"
            required
          >
        </div>
        <p class="text-xs text-muted mt-1">Hanya huruf besar, angka, dan strip (-). Pastikan belum pernah digunakan.</p>
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
                <?php $selected = (string)($old['category_id'] ?? '') === (string)$cat->id ? 'selected' : ''; ?>
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
                <?php $selected = (string)($old['supplier_id'] ?? '') === (string)$sup->id ? 'selected' : ''; ?>
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
              value="<?= e($old['price'] ?? '') ?>" 
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
            value="<?= e($old['stock'] ?? '0') ?>" 
            placeholder="0" 
            class="py-2.5 px-3.5 block w-full rounded-lg border <?= isset($errors['stock']) ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border' ?> bg-canvas text-primary text-sm focus:border-brand focus:ring-1 focus:ring-brand focus:outline-hidden transition-colors"
            required
          >
          <?php if (isset($errors['stock'])): ?>
            <p class="text-xs text-rose-500 mt-1"><?= e($errors['stock']) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="pt-4 border-t border-border flex items-center justify-end gap-3">
        <a href="/products" class="py-2.5 px-4 inline-flex items-center justify-center text-sm font-medium rounded-lg border border-border text-primary hover:bg-hover focus:outline-hidden transition-colors">
          Batal
        </a>
        <button type="submit" class="py-2.5 px-5 inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg bg-brand text-white hover:bg-brand-hover shadow-xs focus:outline-hidden transition-colors cursor-pointer">
          <iconify-icon icon="lucide:check" class="size-4"></iconify-icon>
          <span>Simpan Produk</span>
        </button>
      </div>
    </form>
  </div>
</div>
