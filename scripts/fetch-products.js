import fs from 'fs/promises'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

async function fetchProducts() {
  try {
    console.log('Mengambil semua data produk dari DummyJSON...')
    const limit = 30
    let skip = 0
    let total = 0
    const allProducts = []

    do {
      console.log(`Mengambil data dari skip=${skip}...`)
      const res = await fetch(`https://dummyjson.com/products?limit=${limit}&skip=${skip}`)

      if (!res.ok) {
        throw new Error(`HTTP Error: ${res.status}`)
      }

      const data = await res.json()
      total = data.total

      const filtered = data.products.map(({ title, description, price, discountPercentage, rating, thumbnail }) => ({
        title,
        description,
        price,
        discountPercentage,
        rating,
        thumbnail
      }))

      allProducts.push(...filtered)
      skip += limit
    } while (skip < total)

    const outputDir = path.resolve(__dirname, '../data')
    await fs.mkdir(outputDir, { recursive: true })

    const outputPath = path.join(outputDir, 'products-raw.json')
    await fs.writeFile(outputPath, JSON.stringify(allProducts, null, 2), 'utf-8')

    console.log(`✅ Berhasil mengambil seluruh data! Total: ${allProducts.length} produk tersimpan di ${outputPath}`)
  } catch (error) {
    console.error('❌ Gagal mengambil produk:', error)
  }
}

fetchProducts()
