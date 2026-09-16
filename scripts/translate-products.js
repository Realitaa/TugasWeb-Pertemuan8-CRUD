import fs from 'fs/promises'
import path from 'path'
import { fileURLToPath } from 'url'
import { translate } from '@vitalets/google-translate-api'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const DELIMITER = ' ||| '
const MAX_CHAR_LIMIT = 4000 // Batas aman per batch request

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms))
}

async function translateViaWeb(text) {
  const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=id&dt=t&q=${encodeURIComponent(text)}`
  const res = await fetch(url)
  if (!res.ok) {
    throw new Error(`Google API status: ${res.status}`)
  }
  const data = await res.json()
  // data[0] adalah array potongan terjemahan [[translated, original], ...]
  return data[0].map(item => item[0]).join('')
}

async function translateWithRetry(text, retries = 5, delayMs = 3000) {
  for (let attempt = 1; attempt <= retries; attempt++) {
    try {
      // Coba library @vitalets/google-translate-api terlebih dahulu
      const res = await translate(text, { to: 'id' })
      return res.text
    } catch (err) {
      console.warn(`⚠️ Library translate rate-limited, mencoba fallback googleapis...`)
      try {
        const fallbackText = await translateViaWeb(text)
        return fallbackText
      } catch (fallbackErr) {
        console.warn(`⚠️ Percobaan ${attempt}/${retries} gagal: ${fallbackErr.message}`)
        if (attempt === retries) throw fallbackErr
        await sleep(delayMs * attempt)
      }
    }
  }
}

// Escape karakter khusus regex
function escapeRegex(string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
}

async function translateProducts() {
  try {
    const rawPath = path.resolve(__dirname, '../data/products-raw.json')
    const rawData = await fs.readFile(rawPath, 'utf-8')
    const products = JSON.parse(rawData)

    console.log(`🚀 Memulai translasi deskripsi untuk ${products.length} produk (nama produk tetap original)...`)

    // Siapkan data deskripsi dengan placeholder untuk nama produk di dalamnya
    const preparedItems = products.map((item, index) => {
      // Ganti kemunculan nama produk (case-insensitive) di dalam deskripsi dengan token non-translatable
      const titleRegex = new RegExp(escapeRegex(item.title), 'gi')
      const maskedDesc = item.description.replace(titleRegex, '___PRDNAME___')
      
      return {
        index,
        originalTitle: item.title,
        originalDesc: item.description,
        maskedDesc
      }
    })

    // Buat batch deskripsi agar tidak melebihi MAX_CHAR_LIMIT
    const batches = []
    let currentBatchItems = []
    let currentBatchLength = 0

    for (let i = 0; i < preparedItems.length; i++) {
      const item = preparedItems[i]
      const addedLength = (currentBatchItems.length > 0 ? DELIMITER.length : 0) + item.maskedDesc.length

      if (currentBatchLength + addedLength > MAX_CHAR_LIMIT && currentBatchItems.length > 0) {
        batches.push(currentBatchItems)
        currentBatchItems = [item]
        currentBatchLength = item.maskedDesc.length
      } else {
        currentBatchItems.push(item)
        currentBatchLength += addedLength
      }
    }

    if (currentBatchItems.length > 0) {
      batches.push(currentBatchItems)
    }

    console.log(`📦 Dibagi menjadi ${batches.length} batch (<${MAX_CHAR_LIMIT} karakter/batch).`)

    const translatedProducts = [...products]

    for (let b = 0; b < batches.length; b++) {
      const batch = batches[b]
      console.log(`🔄 Menerjemahkan batch ${b + 1}/${batches.length} (${batch.length} deskripsi produk)...`)

      // Gabungkan deskripsi yang sudah dimask
      const combinedText = batch.map(item => item.maskedDesc).join(DELIMITER)

      const translatedCombined = await translateWithRetry(combinedText)
      const translatedParts = translatedCombined.split(/\s*\|\|\|\s*/)

      for (let i = 0; i < batch.length; i++) {
        const item = batch[i]
        let translatedDesc = translatedParts[i]?.trim() || item.originalDesc

        // Kembalikan placeholder ___PRDNAME___ atau variasi terjemahan ke nama aslinya
        translatedDesc = translatedDesc
          .replace(/_{1,3}\s*PRDNAME\s*_{1,3}/gi, item.originalTitle)
          .replace(/___PRDNAME___/gi, item.originalTitle)
          .replace(/\{\{\s*(PRODUCT|PRODUK)_NAME\s*\}\}/gi, item.originalTitle)
          .replace(new RegExp(`_+\\s*${escapeRegex(item.originalTitle)}`, 'g'), item.originalTitle)
          .replace(new RegExp(`${escapeRegex(item.originalTitle)}\\s*_+`, 'g'), item.originalTitle)

        translatedProducts[item.index] = {
          ...translatedProducts[item.index],
          title: item.originalTitle, // Judul produk tetap 100% original
          description: translatedDesc
        }
      }

      // Jeda sejenak untuk menghindari rate limit 429
      if (b < batches.length - 1) {
        await sleep(1500)
      }
    }

    const outputPath = path.resolve(__dirname, '../data/products-translated.json')
    await fs.writeFile(outputPath, JSON.stringify(translatedProducts, null, 2), 'utf-8')

    console.log(`✅ Sukses menerjemahkan deskripsi produk! Disimpan ke: ${outputPath}`)
  } catch (error) {
    console.error('❌ Error saat proses translasi:', error)
  }
}

translateProducts()
