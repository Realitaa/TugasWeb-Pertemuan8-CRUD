import Toastify from 'toastify-js'
import 'toastify-js/src/toastify.css'

/**
 * Inisialisasi Flash Message Toastify jika elemen #flash-message ada di halaman.
 */
export function initFlashToast() {
  const flashEl = document.getElementById('flash-message')
  if (!flashEl) return

  const type = flashEl.getAttribute('data-type') || 'info'
  const message = flashEl.getAttribute('data-message') || ''

  if (!message) return

  showToast(message, type)
}

/**
 * Menampilkan notifikasi Toastify di pojok kanan bawah.
 *
 * @param {string} message 
 * @param {'success'|'error'|'info'|'warning'} type 
 */
export function showToast(message, type = 'info') {
  Toastify({
    text: message,
    duration: 4500,
    close: true,
    gravity: 'bottom', // 'top' or 'bottom'
    position: 'right', // 'left', 'center' or 'right'
    stopOnFocus: true,
    className: `custom-toast custom-toast-${type}`,
  }).showToast()
}
