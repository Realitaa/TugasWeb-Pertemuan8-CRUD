import fs from 'fs/promises'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

async function convertCurrency() {
  try {
    console.log('Mengambil nilai tukar USD ke IDR dari frankfurter...')
    const rateRes = await fetch('https://api.frankfurter.dev/v2/rate/USD/IDR')
    
    if (!rateRes.ok) {
      throw new Error(`HTTP Error dari Frankfurter: ${rateRes.status}`)
    }

    const rateData = await rateRes.json()
    // format response: { "amount": 1, "base": "USD", "date": "...", "rates": { "IDR": 16500 } } atau { "rate": ... }
    const rate = rateData.rates?.IDR || rateData.rate || rateData.IDR
    
    if (!rate) {
      throw newError('Nilai tukar IDR tidak ditemukan dalam respons: ' + JSON.stringify(rateData))
    }

    console.log(`💱 Kurs saat ini: 1 USD = Rp ${Number(rate).toLocaleString('id-ID')}`)

    // Baca data yang sudah ditranslasi jika ada, atau products-raw.json
    const translatedPath = path.resolve(__dirname, '../data/products-translated.json')
    const rawPath = path.resolve(__dirname, '../data/products-raw.json')
    
    let sourcePath = rawPath
    try {
      await fs.access(translatedPath)
      sourcePath = translatedPath
      console.log('📂 Menggunakan data yang sudah diterjemahkan: products-translated.json')
    } catch {
      console.log('📂 products-translated.json belum ada, menggunakan products-raw.json')
    }

    const sourceData = await fs.readFile(sourcePath, 'utf-8')
    const products = JSON.parse(sourceData)

    const finalProducts = products.map(product => {
      const priceIdr = Math.round(product.price * rate)
      return {
        ...product,
        priceUSD: product.price,
        price: priceIdr // Harga dalam IDR (Rupiah)
      }
    })

    const finalOutputPath = path.resolve(__dirname, '../data/products.json')
    await fs.writeFile(finalOutputPath, JSON.stringify(finalProducts, null, 2), 'utf-8')

    console.log(`✅ Sukses! ${finalProducts.length} produk tersimpan di ${finalOutputPath} dengan harga IDR (Rupiah).`)
  } catch (error) {
    console.error('❌ Error saat konversi mata uang:', error)
  }
}

convertCurrency()
