import { defineConfig } from '@playwright/test';

// ponytail: load .env natively if available
if (typeof process.loadEnvFile === 'function') {
  try {
    process.loadEnvFile('.env');
  } catch (e) {
    // Silently continue if .env does not exist
  }
}

export default defineConfig({
  testDir: './tests/browser',
  timeout: 30000,
  workers: 1,
  fullyParallel: false,
  reporter: [['list'], ['html', { open: 'never' }]],
  webServer: {
    command: 'php artisan serve --host=127.0.0.1 --port=8081 --no-reload',
    url: 'http://127.0.0.1:8081',
    reuseExistingServer: false,
    timeout: 30000,
    env: {
      ...process.env,
      APP_ENV: 'local',
      APP_URL: 'http://127.0.0.1:8081',
      DB_CONNECTION: 'sqlite',
      DB_DATABASE: 'database/database.sqlite',
      CACHE_STORE: 'file',
      SESSION_DRIVER: 'file',
      QUEUE_CONNECTION: 'sync',
      MAIL_MAILER: 'log',
      REDIS_CLIENT: 'predis',
    },
  },
  use: {
    baseURL: 'http://127.0.0.1:8081',
    headless: true,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  }
});