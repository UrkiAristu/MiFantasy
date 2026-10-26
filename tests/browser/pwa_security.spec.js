import { test, expect } from '@playwright/test';

test.describe('Landing y PWA', () => {
  test('expone metadatos y recursos PWA válidos', async ({ page, request }) => {
    const landing = await page.goto('/');
    expect(landing?.status()).toBe(200);

    await expect(page.locator('meta[name="theme-color"]')).toHaveAttribute('content', /.+/);
    await expect(page.locator('meta[name="viewport"]')).toHaveAttribute('content', /viewport-fit=cover/);
    await expect(page.locator('link[rel="manifest"]')).toHaveAttribute('href', /manifest\.json/);

    expect((await request.get('/manifest.json')).status()).toBe(200);
    expect((await request.get('/sw.js')).status()).toBe(200);
  });
});

test.describe('Protección de rutas anónimas', () => {
  for (const path of ['/home', '/admin-local/dashboard', '/admin/usuarios']) {
    test(`${path} redirige al login sin sesión`, async ({ page }) => {
      await page.goto(path);
      await expect(page).toHaveURL(/\/login/);
    });
  }
});
