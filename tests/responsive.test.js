import { describe, it, expect, beforeEach } from 'vitest'
import fs from 'fs'
import path from 'path'
import { setupProducts } from '../src/products.js'

describe('Tailwind CSS Responsive Utility Classes & Structure', () => {
  beforeEach(() => {
    const htmlPath = path.resolve(__dirname, '../index.html')
    let html = fs.readFileSync(htmlPath, 'utf-8')
    // Hapus tag link remote saat unit testing agar happy-dom tidak melakukan fetch jaringan
    html = html.replace(/<link\b[^>]*>/gi, '')
    document.documentElement.innerHTML = html
  })

  it('1. Memastikan grid produk menerapkan kelas responsif mobile-first (1 -> 2 -> 3 -> 4 kolom)', () => {
    const productGrid = document.querySelector('#product-list')
    expect(productGrid).not.toBeNull()

    const classList = productGrid.className
    // Mobile default: 1 kolom
    expect(classList).toContain('grid-cols-1')
    // Breakpoint sm (>=640px): 2 kolom
    expect(classList).toContain('sm:grid-cols-2')
    // Breakpoint lg (>=1024px): 3 kolom
    expect(classList).toContain('lg:grid-cols-3')
    // Breakpoint xl (>=1280px): 4 kolom
    expect(classList).toContain('xl:grid-cols-4')
  })

  it('2. Memastikan navbar responsif dengan mobile-first hamburger toggle & desktop navlist', () => {
    const hamburgerBtn = document.querySelector('#hs-navbar-example-collapse')
    const navCollapse = document.querySelector('#hs-navbar-example')
    const navHeader = document.querySelector('header')

    expect(hamburgerBtn).not.toBeNull()
    expect(navCollapse).not.toBeNull()
    expect(navHeader).not.toBeNull()

    // Mobile: Hamburger terlihat, Desktop (sm): disembunyikan
    expect(hamburgerBtn.parentElement.className).toContain('sm:hidden')

    // Mobile menu: default 'hidden', Desktop (sm): flex row & auto grow
    expect(navCollapse.className).toContain('hidden')
    expect(navCollapse.className).toContain('sm:flex')
    expect(navCollapse.className).toContain('sm:items-center')

    // Sticky navbar
    expect(navHeader.className).toContain('sticky')
    expect(navHeader.className).toContain('top-0')
  })

  it('3. Memastikan gambar produk menerapkan class responsive image', () => {
    setupProducts()

    const productImages = document.querySelectorAll('#product-list img')
    expect(productImages.length).toBeGreaterThan(0)

    productImages.forEach(img => {
      expect(img.className).toContain('w-full')
      expect(img.className).toContain('h-full')
      expect(img.className).toContain('object-contain')
    })
  })

  it('4. Memastikan kartu produk memiliki elemen lengkap (gambar + rating + harga + diskon + tombol beli)', () => {
    setupProducts()

    const cards = document.querySelectorAll('#product-list > article')
    expect(cards.length).toBeGreaterThan(0)

    const firstCard = cards[0]
    expect(firstCard.querySelector('img')).not.toBeNull()
    expect(firstCard.querySelector('h3')).not.toBeNull()
    expect(firstCard.querySelector('p')).not.toBeNull()
    expect(firstCard.querySelector('a')).not.toBeNull()
    expect(firstCard.querySelector('a').textContent.trim()).toBe('Beli Sekarang')
  })

  it('5. Memastikan footer responsif ada di dokumen', () => {
    const footer = document.querySelector('footer')
    expect(footer).not.toBeNull()
    expect(footer.querySelector('div').className).toContain('flex-col')
    expect(footer.querySelector('div').className).toContain('sm:flex-row')
  })
})
