import { test, expect } from '@playwright/test'
import path from 'path'
import fs from 'fs'

const screenshotsDir = path.resolve(process.cwd(), 'tests/screenshots')

// 4 Breakpoints Tailwind CSS
const breakpoints = [
  { name: 'mobile', width: 375, height: 667, desc: '<640px (1 Kolom Grid, Hamburger Menu)' },
  { name: 'tablet-sm', width: 768, height: 1024, desc: '>=640px (2 Kolom Grid, Desktop Navbar)' },
  { name: 'desktop-lg', width: 1024, height: 768, desc: '>=1024px (3 Kolom Grid)' },
  { name: 'desktop-xl', width: 1440, height: 900, desc: '>=1280px (4 Kolom Grid)' },
]

test.beforeAll(() => {
  if (!fs.existsSync(screenshotsDir)) {
    fs.mkdirSync(screenshotsDir, { recursive: true })
  }
})

for (const bp of breakpoints) {
  test(`Capture responsive screenshot on ${bp.name} (${bp.width}px)`, async ({ page }) => {
    await page.setViewportSize({ width: bp.width, height: bp.height })
    await page.goto('/')

    // Tunggu produk dan font ter-render
    await page.waitForSelector('#product-list > article')

    const screenshotPath = path.join(screenshotsDir, `${bp.name}.png`)
    await page.screenshot({ path: screenshotPath, fullPage: true })

    expect(fs.existsSync(screenshotPath)).toBe(true)
  })
}
