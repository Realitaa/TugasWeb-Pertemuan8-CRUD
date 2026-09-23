<div class="space-y-12">
  <!-- Hero / Greeting -->
  <div>
    <h1 class="text-3xl sm:text-4xl font-bold text-primary">Selamat Datang, <span init-name>Reza</span></h1>
    <p class="text-base sm:text-lg text-muted mt-1.5">Mau belanja apa hari ini? Silahkan lihat katalog belanja kami.</p>
  </div>

  <!-- Product List Catalog Section -->
  <section id="product" class="scroll-mt-20">
    <div id="product-list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 min-h-125">
      <!-- Skeleton Loading Placeholders -->
      <?php for ($i = 0; $i < 4; $i++): ?>
        <article class="flex flex-col bg-surface border border-border shadow-2xs rounded-xl overflow-hidden animate-pulse">
          <div class="w-full pt-[75%] bg-border/40"></div>
          <div class="p-4 flex flex-col flex-1 gap-3">
            <div class="flex justify-between items-center"><div class="h-4 w-12 bg-border/50 rounded"></div><div class="h-5 w-20 bg-border/50 rounded"></div></div>
            <div class="h-5 w-3/4 bg-border/50 rounded"></div>
            <div class="h-4 w-full bg-border/40 rounded"></div>
            <div class="mt-auto h-9 w-full bg-border/60 rounded-lg"></div>
          </div>
        </article>
      <?php endfor; ?>
    </div>

    <!-- Pagination Container (Rendered by JS) -->
    <div class="mt-10 flex justify-center">
      <nav id="pagination-container" class="flex items-center -space-x-px" aria-label="Pagination">
      </nav>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="pt-10 border-t border-border scroll-mt-20">
    <div class="w-full">
      <h2 class="text-2xl font-bold text-primary sm:text-3xl">Tentang RealCommerce</h2>
      <p class="mt-3 text-muted leading-relaxed">
        RealCommerce adalah platform katalog produk modern yang dirancang untuk memberikan pengalaman eksplorasi belanja yang cepat, responsif, dan nyaman. Dibangun dengan teknologi web modern menggunakan <strong>Tailwind CSS v4</strong> dan <strong>Vite</strong>, website ini mendukung penyesuaian tema terang/gelap serta terintegrasi langsung dengan pemesanan melalui WhatsApp.
      </p>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="pt-10 border-t border-border scroll-mt-20">
    <div>
      <h2 class="text-2xl font-bold text-primary sm:text-3xl">Hubungi Kami</h2>
      <p class="mt-3 text-muted leading-relaxed">
        Punya pertanyaan mengenai produk atau membutuhkan bantuan seputar pemesanan? Jangan ragu untuk menghubungi kami.
      </p>

      <div class="mt-6 space-y-4">
        <div class="flex items-center gap-3">
          <span class="size-10 flex items-center justify-center rounded-lg bg-surface border border-border text-primary">
            <iconify-icon icon="lucide:map-pin" class="size-5"></iconify-icon>
          </span>
          <div>
            <h4 class="text-sm font-semibold text-primary">Lokasi</h4>
            <p class="text-sm text-muted" init-location>Indonesia</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="size-10 flex items-center justify-center rounded-lg bg-surface border border-border text-primary">
            <iconify-icon icon="lucide:phone" class="size-5"></iconify-icon>
          </span>
          <div>
            <h4 class="text-sm font-semibold text-primary">WhatsApp / Kontak</h4>
            <p class="text-sm text-muted" init-phone>+62 888-0767-3506</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="size-10 flex items-center justify-center rounded-lg bg-surface border border-border text-primary">
            <iconify-icon icon="lucide:user" class="size-5"></iconify-icon>
          </span>
          <div>
            <h4 class="text-sm font-semibold text-primary">Admin Pengelola</h4>
            <p class="text-sm text-muted" init-fullname>Reza Mulia Putra</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
