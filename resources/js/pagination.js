export function renderPagination({ container, currentPage, totalPages, onPageChange }) {
  if (!container) return

  if (totalPages <= 1) {
    container.innerHTML = ''
    return
  }

  // Tentukan range nomor halaman yang ditampilkan (maksimal 5 nomor di sekitar currentPage)
  const delta = 2
  const range = []
  for (let i = Math.max(1, currentPage - delta); i <= Math.min(totalPages, currentPage + delta); i++) {
    range.push(i)
  }

  const isPrevDisabled = currentPage <= 1
  const isNextDisabled = currentPage >= totalPages

  let html = `
    <!-- Tombol Sebelumnya -->
    <button type="button" data-page="${currentPage - 1}" ${isPrevDisabled ? 'disabled' : ''} class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-border text-primary hover:bg-hover focus:outline-hidden disabled:opacity-40 disabled:pointer-events-none cursor-pointer disabled:cursor-not-allowed" aria-label="Previous">
      <i data-lucide="chevron-left" class="shrink-0 size-3.5"></i>
      <span class="hidden sm:block">Sebelumnya</span>
    </button>
  `

  // Jika halaman pertama tidak masuk di range
  if (range[0] > 1) {
    html += `
      <button type="button" data-page="1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-border text-primary hover:bg-hover py-2 px-3 text-sm focus:outline-hidden cursor-pointer">1</button>
    `
    if (range[0] > 2) {
      html += `<span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-border text-muted py-2 px-2 text-sm">...</span>`
    }
  }

  // Nomor halaman
  range.forEach(page => {
    const isActive = page === currentPage
    const activeClass = isActive
      ? 'bg-brand text-white font-medium'
      : 'text-primary hover:bg-hover'

    html += `
      <button type="button" data-page="${page}" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-border ${activeClass} py-2 px-3 text-sm focus:outline-hidden cursor-pointer" ${isActive ? 'aria-current="page"' : ''}>
        ${page}
      </button>
    `
  })

  // Jika halaman terakhir tidak masuk di range
  if (range[range.length - 1] < totalPages) {
    if (range[range.length - 1] < totalPages - 1) {
      html += `<span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-border text-muted py-2 px-2 text-sm">...</span>`
    }
    html += `
      <button type="button" data-page="${totalPages}" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-border text-primary hover:bg-hover py-2 px-3 text-sm focus:outline-hidden cursor-pointer">${totalPages}</button>
    `
  }

  // Tombol Selanjutnya
  html += `
    <button type="button" data-page="${currentPage + 1}" ${isNextDisabled ? 'disabled' : ''} class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-border text-primary hover:bg-hover focus:outline-hidden disabled:opacity-40 disabled:pointer-events-none cursor-pointer disabled:cursor-not-allowed" aria-label="Next">
      <span class="hidden sm:block">Selanjutnya</span>
      <i data-lucide="chevron-right" class="shrink-0 size-3.5"></i>
    </button>
  `

  container.innerHTML = html

  // Event listener tombol
  container.querySelectorAll('button[data-page]').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetPage = Number(btn.getAttribute('data-page'))
      if (targetPage >= 1 && targetPage <= totalPages && targetPage !== currentPage) {
        onPageChange(targetPage)
      }
    })
  })
}
