import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/browser',
  timeout: 30000,
  reporter: 'html',
  use: {
    baseURL: 'http://localhost:8080', // Apunta directo al contenedor web de Docker
    headless: true,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  }
});