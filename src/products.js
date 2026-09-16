import productsData from '../data/products.json'
import { renderPagination } from './pagination.js'
import { formatRupiah, calculateOriginalPrice, getTimeStatus, scrollToElement } from './utils.js'
import { fullname, phone } from './initVal.js'

export function setupProducts() {
  const productContainer = document.getElementById('product-list')
  const paginationContainer = document.getElementById('pagination-container')

  if (!productContainer) return

  const itemsPerPage = 8
  let currentPage = 1
  const totalProducts = productsData.length
  const totalPages = Math.ceil(totalProducts / itemsPerPage)

  function displayProducts(page) {
    currentPage = page
    const startIndex = (page - 1) * itemsPerPage
    const paginatedProducts = productsData.slice(startIndex, startIndex + itemsPerPage)

    productContainer.innerHTML = paginatedProducts.map((product, idx) => {
      const originalPrice = calculateOriginalPrice(product.price, product.discountPercentage)
      const formattedPrice = formatRupiah(product.price)
      const formattedOriginalPrice = originalPrice ? formatRupiah(originalPrice) : ''
      const prefilledMessage = `Selamat ${getTimeStatus()}, ${fullname}. Saya ingin membeli produk ${product.title}. Apakah produk ini masih tersedia?`
      const waLink = `https://wa.me/${phone}/?text=${encodeURIComponent(prefilledMessage)}`
      const isAboveTheFold = idx < 4

      return `
        <article class="flex flex-col bg-surface border border-border shadow-2xs rounded-xl overflow-hidden transition-transform duration-200 hover:-translate-y-1">
          <div class="relative w-full pt-[75%] bg-surface/50 overflow-hidden">
            <img class="absolute inset-0 w-full h-full object-contain p-4" src="${product.thumbnail}" alt="${product.title}" width="300" height="225" ${isAboveTheFold ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'}>
            ${product.discountPercentage > 0 ? `
              <span class="absolute top-3 right-3 bg-brand text-white text-xs font-bold px-2 py-1 rounded-md shadow-xs">
                -${Math.round(product.discountPercentage)}%
              </span>
            ` : ''}
          </div>
          <div class="p-4 flex flex-col flex-1">
            <div class="flex items-center justify-between gap-2 mb-2">
              <span class="text-xs font-medium inline-flex items-center gap-1 shrink-0 self-center">
                <i data-lucide="star" class="size-3.5 fill-amber-400 text-amber-400 shrink-0"></i>
                <span>${product.rating}</span>
              </span>
              <div class="text-right flex flex-col items-end shrink-0 min-w-0">
                ${originalPrice ? `<span class="text-xs text-muted line-through whitespace-nowrap leading-tight">${formattedOriginalPrice}</span>` : ''}
                <span class="text-base font-bold whitespace-nowrap leading-tight">${formattedPrice}</span>
              </div>
            </div>
            <h3 class="font-semibold text-primary line-clamp-1 text-base" title="${product.title}">
              ${product.title}
            </h3>
            <p class="mt-1 text-sm text-muted line-clamp-2 flex-1">
              ${product.description}
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


