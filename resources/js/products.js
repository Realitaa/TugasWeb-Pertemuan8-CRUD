import { renderPagination } from './pagination.js'
import { formatRupiah, calculateOriginalPrice, getTimeStatus, scrollToElement } from './utils.js'
import { fullname, phone } from './initVal.js'

export function setupProducts() {
  const productContainer = document.getElementById('product-list')
  const paginationContainer = document.getElementById('pagination-container')

  if (!productContainer) return

  const productsData = Array.isArray(window.__PRODUCTS_DATA__) ? window.__PRODUCTS_DATA__ : []

  if (productsData.length === 0) {
    productContainer.innerHTML = `
      <div class="col-span-full py-16 text-center">
        <iconify-icon icon="lucide:package-open" class="size-16 text-muted/40 mx-auto mb-3"></iconify-icon>
        <h3 class="text-lg font-semibold text-primary">Belum Ada Produk</h3>
        <p class="text-sm text-muted mt-1">Saat ini belum ada produk yang tersedia di katalog.</p>
      </div>
    `
    if (paginationContainer) paginationContainer.innerHTML = ''
    return
  }

  const itemsPerPage = 8
  let currentPage = 1
  const totalProducts = productsData.length
  const totalPages = Math.ceil(totalProducts / itemsPerPage)

  function displayProducts(page) {
    currentPage = page
    const startIndex = (page - 1) * itemsPerPage
    const paginatedProducts = productsData.slice(startIndex, startIndex + itemsPerPage)

    productContainer.innerHTML = paginatedProducts.map((product, idx) => {
      const discount = Number(product.discountPercentage || 0)
      const hasDiscount = discount > 0
      const originalPrice = hasDiscount ? calculateOriginalPrice(product.price, discount) : null
      const formattedPrice = formatRupiah(product.price)
      const formattedOriginalPrice = originalPrice ? formatRupiah(originalPrice) : ''
      const productName = product.title || product.name || 'Produk'
      const prefilledMessage = `Selamat ${getTimeStatus()}, ${fullname}. Saya ingin membeli produk ${productName}. Apakah produk ini masih tersedia?`
      const waLink = `https://wa.me/${phone}/?text=${encodeURIComponent(prefilledMessage)}`
      const isAboveTheFold = idx < 4

      const hasThumbnail = Boolean(product.thumbnail && String(product.thumbnail).trim() !== '')
      const hasRating = product.rating !== null && product.rating !== undefined && product.rating !== '' && Number(product.rating) > 0
      const descriptionText = product.description && String(product.description).trim() !== ''
        ? product.description
        : 'Tidak ada deskripsi produk.'

      return `
        <article class="flex flex-col bg-surface border border-border shadow-2xs rounded-xl overflow-hidden transition-transform duration-200 hover:-translate-y-1">
          <div class="relative w-full pt-[75%] bg-surface/50 overflow-hidden">
            ${hasThumbnail ? `
              <img class="absolute inset-0 w-full h-full object-contain p-4 transition-opacity duration-200" 
                   src="${product.thumbnail}" 
                   alt="${productName}" 
                   width="300" 
                   height="225" 
                   ${isAboveTheFold ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'}
                   onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
              >
              <div class="hidden absolute inset-0 flex flex-col items-center justify-center text-muted bg-surface/50 p-4">
                <iconify-icon icon="lucide:package" class="size-16 opacity-40 mb-1"></iconify-icon>
                <span class="text-xs text-muted/70">Gambar tidak tersedia</span>
              </div>
            ` : `
              <div class="absolute inset-0 flex flex-col items-center justify-center text-muted bg-surface/50 p-4">
                <iconify-icon icon="lucide:package" class="size-16 opacity-40 mb-1"></iconify-icon>
                <span class="text-xs text-muted/70">Gambar default</span>
              </div>
            `}
            ${hasDiscount ? `
              <span class="absolute top-3 right-3 bg-brand text-white text-xs font-bold px-2 py-1 rounded-md shadow-xs">
                -${Math.round(discount)}%
              </span>
            ` : ''}
          </div>
          <div class="p-4 flex flex-col flex-1">
            <div class="flex items-center justify-between gap-2 mb-2">
              ${hasRating ? `
                <span class="text-xs font-medium inline-flex items-center gap-1 shrink-0 self-center">
                  <iconify-icon icon="lucide:star" class="size-3.5 fill-amber-400 text-amber-400 shrink-0"></iconify-icon>
                  <span>${Number(product.rating).toFixed(2)}</span>
                </span>
              ` : `<span></span>`}
              <div class="text-right flex flex-col items-end shrink-0 min-w-0">
                ${originalPrice ? `<span class="text-xs text-muted line-through whitespace-nowrap leading-tight">${formattedOriginalPrice}</span>` : ''}
                <span class="text-base font-bold whitespace-nowrap leading-tight">${formattedPrice}</span>
              </div>
            </div>
            <h3 class="font-semibold text-primary line-clamp-1 text-base" title="${productName}">
              ${productName}
            </h3>
            <p class="mt-1 text-sm text-muted line-clamp-2 flex-1" title="${descriptionText}">
              ${descriptionText}
            </p>
            <a class="mt-4 py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg bg-brand text-white hover:bg-brand-hover focus:outline-hidden transition-colors" href="${waLink}" target="_blank">
              Beli Sekarang
            </a>
          </div>
        </article>
      `
    }).join('')

    // Render pagination
    if (paginationContainer) {
      renderPagination({
        container: paginationContainer,
        currentPage,
        totalPages,
        onPageChange: (newPage) => {
          displayProducts(newPage)
          scrollToElement('#product')
        }
      })
    }
  }

  // Initial render
  displayProducts(1)
}
