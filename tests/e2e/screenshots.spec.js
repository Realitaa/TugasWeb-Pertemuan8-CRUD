import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

const screenshotsDir = path.resolve('tests/screenshots');

test.beforeAll(() => {
  if (!fs.existsSync(screenshotsDir)) {
    fs.mkdirSync(screenshotsDir, { recursive: true });
  }
});

test.describe('E2E Screenshot Generation (Desktop Mode)', () => {
  test.use({ viewport: { width: 1280, height: 800 } });

  test('01 - Screenshot Halaman Utama / Katalog Belanja', async ({ page }) => {
    await page.goto('/');
    // Wait for the client-side products to finish loading from window.__PRODUCTS_DATA__
    await page.waitForSelector('#product-list article', { timeout: 10000 });
    // Small wait for iconify icons and images to render
    await page.waitForTimeout(1000);

    await page.screenshot({
      path: path.join(screenshotsDir, '01-katalog-home.png'),
      fullPage: false,
    });
  });

  test('02 - Screenshot Halaman Manajemen & Tabel Produk', async ({ page }) => {
    await page.goto('/products');
    await page.waitForSelector('table tbody tr', { timeout: 10000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: path.join(screenshotsDir, '02-manajemen-produk.png'),
      fullPage: false,
    });
  });

  test('03 - Screenshot Halaman Form Tambah Produk dengan Preview Gambar Reaktif', async ({ page }) => {
    await page.goto('/products/create');
    await page.waitForSelector('#name', { timeout: 10000 });

    // Fill form with sample realistic data to showcase reactive image preview
    await page.fill('#name', 'Mechanical Gaming Keyboard RGB');
    await page.fill('#sku', 'GAM-KEY-RGB-001');
    await page.fill('#price', '750000');
    await page.fill('#stock', '45');
    await page.fill('#discount_percentage', '15');
    await page.fill('#rating', '4.85');
    await page.fill('#thumbnail', 'https://cdn.dummyjson.com/products/images/beauty/Essence%20Mascara%20Lash%20Princess/thumbnail.png');
    await page.fill('#description', 'Keyboard mekanik gaming dengan switch presisi tinggi, pencahayaan RGB dinamis, dan keycaps PBT tahan lama.');

    // Wait for reactive preview image to load
    await page.waitForSelector('#image-preview-img[src^="http"]', { timeout: 5000 }).catch(() => {});
    await page.waitForTimeout(800);

    await page.screenshot({
      path: path.join(screenshotsDir, '03-tambah-produk.png'),
      fullPage: false,
    });
  });

  test('04 - Screenshot Halaman Form Edit Produk', async ({ page }) => {
    await page.goto('/products/edit?id=1');
    await page.waitForSelector('#name', { timeout: 10000 });
    await page.waitForTimeout(800);

    await page.screenshot({
      path: path.join(screenshotsDir, '04-edit-produk.png'),
      fullPage: false,
    });
  });

  test('05 - Screenshot Modal Konfirmasi Hapus Produk', async ({ page }) => {
    await page.goto('/products');
    await page.waitForSelector('.btn-delete', { timeout: 10000 });

    // Click delete icon button on first item
    await page.locator('.btn-delete').first().click();

    // Verify modal is visible
    const modal = page.locator('#delete-modal');
    await expect(modal).toBeVisible();
    await page.waitForTimeout(500);

    await page.screenshot({
      path: path.join(screenshotsDir, '05-modal-konfirmasi-hapus.png'),
      fullPage: false,
    });
  });

  test('06 - Screenshot Halaman 404 Not Found', async ({ page }) => {
    await page.goto('/halaman-tidak-ditemukan-404');
    await page.waitForTimeout(500);

    await page.screenshot({
      path: path.join(screenshotsDir, '06-halaman-404.png'),
      fullPage: false,
    });
  });
});
