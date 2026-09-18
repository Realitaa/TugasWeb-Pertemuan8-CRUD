/**
 * initIcon.js
 * Inisialisasi ikon Lucide dan pemantau mutasi DOM otomatis
 */

import { 
  createIcons, 
  Menu, 
  Sun, 
  Moon, 
  Laptop, 
  Star, 
  ChevronLeft, 
  ChevronRight, 
  MapPin, 
  Phone, 
  User,
  Copyright
} from 'lucide'

const appIcons = {
  Menu,
  Sun,
  Moon,
  Laptop,
  Star,
  ChevronLeft,
  ChevronRight,
  MapPin,
  Phone,
  User,
  Copyright
}

/**
 * Merender semua elemen <i data-lucide="..."> menjadi ikon SVG Lucide
 */
export function renderIcons() {
  createIcons({
    icons: appIcons,
  })
}

/**
 * Mengamati perubahan DOM untuk otomatis mengonversi tag <i data-lucide="..."> baru
 */
export function setupIconObserver() {
  if (typeof window === 'undefined' || !('MutationObserver' in window)) return

  let isScheduled = false
  const observer = new MutationObserver((mutations) => {
    let hasNewIcons = false
    for (const mutation of mutations) {
      if (mutation.type === 'childList') {
        for (const node of mutation.addedNodes) {
          if (node.nodeType === Node.ELEMENT_NODE) {
            if (node.matches?.('i[data-lucide]') || node.querySelector?.('i[data-lucide]')) {
              hasNewIcons = true
              break
            }
          }
        }
      }
      if (hasNewIcons) break
    }

    if (hasNewIcons && !isScheduled) {
      isScheduled = true
      requestAnimationFrame(() => {
        renderIcons()
        isScheduled = false
      })
    }
  })

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  })
}