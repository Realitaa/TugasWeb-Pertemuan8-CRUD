import 'iconify-icon'
import '../css/app.css'
import { setupNavbar } from './navbar.js'
import { setupProducts } from './products.js'
import './initVal.js'
import { initFlashToast } from './toast.js'

setupNavbar()
setupProducts()
initFlashToast()
