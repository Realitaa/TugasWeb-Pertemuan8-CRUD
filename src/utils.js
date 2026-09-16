/**
 * utils.js
 * Kumpulan fungsi utilitas murni (pure functions) yang digunakan di seluruh aplikasi
 */

/**
 * Mengambil item dari localStorage dengan nilai default fallback
 * @param {string} key - Kunci localStorage
 * @param {*} defaultValue - Nilai default jika key tidak ditemukan atau error
 * @returns {*}
 */
export function getStorage(key, defaultValue = null) {
  try {
    const value = localStorage.getItem(key)
    return value !== null ? value : defaultValue
  } catch (error) {
    console.warn(`Gagal membaca localStorage key "${key}":`, error)
    return defaultValue
  }
}

/**
 * Menyimpan item ke localStorage secara aman
 * @param {string} key - Kunci localStorage
 * @param {string} value - Nilai yang disimpan
 */
export function setStorage(key, value) {
  try {
    localStorage.setItem(key, value)
  } catch (error) {
    console.warn(`Gagal menulis localStorage key "${key}":`, error)
  }
}

/**
 * Memformat angka menjadi format mata uang Rupiah (IDR)
 * @param {number|string} amount 
 * @returns {string} Contoh: "Rp 176.783" (dengan non-breaking space agar tidak terpisah)
 */
export function formatRupiah(amount) {
  if (amount === null || amount === undefined || isNaN(amount)) return ''
  return `Rp\u00A0${Math.round(Number(amount)).toLocaleString('id-ID')}`
}

/**
 * Menghitung harga asli sebelum diskon
 * @param {number} price - Harga setelah diskon
 * @param {number} discountPercentage - Persentase diskon
 * @returns {number|null} Harga asli atau null jika tidak ada diskon
 */
export function calculateOriginalPrice(price, discountPercentage) {
  if (!discountPercentage || discountPercentage <= 0) return null
  return Math.round(price / (1 - discountPercentage / 100))
}

/**
 * Mengatur teks elemen berdasarkan atribut [init-name]
 * @param {string} name - Nama yang ingin ditampilkan
 */
export function initDisplayName(name = 'Nama') {
  document.querySelectorAll('[init-name]').forEach(el => {
    el.textContent = name
  })
}

/**
 * Mendapatkan status waktu saat ini dalam Bahasa Indonesia berdasarkan jam WIB (Asia/Jakarta)
 * @param {Date} [date=new Date()] - Objek Date (default: saat ini)
 * @returns {string} 'Pagi' | 'Siang' | 'Sore' | 'Malam'
 */
export function getTimeStatus(date = new Date()) {
  const hour = Number(
    new Intl.DateTimeFormat('en-US', {
      timeZone: 'Asia/Jakarta',
      hour: 'numeric',
      hour12: false,
    }).format(date)
  )

  if (hour >= 5 && hour < 11) return 'Pagi'
  if (hour >= 11 && hour < 15) return 'Siang'
  if (hour >= 15 && hour < 18) return 'Sore'
  return 'Malam'
}

/**
 * Melakukan scroll halus ke elemen target
 * @param {Element|string} target - Elemen DOM atau selector string
 * @param {ScrollLogicalPosition} [block='start'] - Posisi perataan vertikal
 */
export function scrollToElement(target, block = 'start') {
  const element = typeof target === 'string' ? document.querySelector(target) : target
  if (element && typeof element.scrollIntoView === 'function') {
    element.scrollIntoView({ behavior: 'smooth', block })
  }
}