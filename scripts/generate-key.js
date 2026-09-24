import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');
const envPath = path.join(rootDir, '.env');
const envExamplePath = path.join(rootDir, '.env.example');

/**
 * Generate a standard UUID version 7 (RFC 9562) using native Node crypto.
 * UUID v7 layout:
 * - 48 bits: Unix timestamp in milliseconds
 * - 4 bits: Version 7 (0b0111)
 * - 12 bits: Pseudo-random bits
 * - 2 bits: Variant RFC 9562 (0b10)
 * - 62 bits: Pseudo-random bits
 */
export function generateUuidV7() {
  const bytes = crypto.randomBytes(16);
  const now = Date.now();

  // 48-bit timestamp in milliseconds (bytes 0 to 5)
  bytes.writeUIntBE(now, 0, 6);

  // version 7 (4 bits) in byte 6: 0111xxxx
  bytes[6] = (bytes[6] & 0x0f) | 0x70;

  // variant RFC 9562 (2 bits) in byte 8: 10xxxxxx
  bytes[8] = (bytes[8] & 0x3f) | 0x80;

  const hex = bytes.toString('hex');
  return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
}

export function run() {
  const key = generateUuidV7();

  let envContent = '';
  if (fs.existsSync(envPath)) {
    envContent = fs.readFileSync(envPath, 'utf8');
  } else if (fs.existsSync(envExamplePath)) {
    envContent = fs.readFileSync(envExamplePath, 'utf8');
  }

  const keyRegex = /^DB_RESET_KEY=.*$/m;
  if (keyRegex.test(envContent)) {
    envContent = envContent.replace(keyRegex, `DB_RESET_KEY=${key}`);
  } else {
    envContent = envContent.trimEnd() + `\nDB_RESET_KEY=${key}\n`;
  }

  fs.writeFileSync(envPath, envContent, 'utf8');

  console.log(`\x1b[32m[DONE]\x1b[0m DB_RESET_KEY berhasil di-generate: \x1b[1m${key}\x1b[0m`);
  console.log(`\x1b[36m[INFO]\x1b[0m Kunci telah disimpan ke file .env`);
}

// Execute when run directly
if (process.argv[1] === __filename) {
  run();
}
