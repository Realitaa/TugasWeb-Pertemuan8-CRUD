import { initDisplayName } from './utils.js'

export const name = "Reza"
export const fullname = "Reza Mulia Putra"
export const phone = "+6288807673506"
export const location = "Indonesia"

initDisplayName(name)

// Inisialisasi data profil di halaman jika ada
document.querySelectorAll('[init-fullname]').forEach(el => {
  el.textContent = fullname
})
document.querySelectorAll('[init-phone]').forEach(el => {
  el.textContent = phone
})
document.querySelectorAll('[init-location]').forEach(el => {
  el.textContent = location
})