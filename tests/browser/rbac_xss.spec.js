import { test, expect } from '@playwright/test';

async function login(page, email) {
  await page.goto('/login');
  await page.locator('#login').fill(email);
  await page.locator('#password').fill('password');
  await page.getByRole('button', { name: /entrar al juego/i }).click();
  await expect(page).toHaveURL(/\/home/);
}

test.describe('RBAC y XSS en navegador', () => {
  for (const email of ['carlos@mifantasy.com', 'b2badmin@mifantasy.com']) {
    test(`${email} no puede acceder a SuperAdmin`, async ({ page }) => {
      await login(page, email);
      const response = await page.goto('/admin/usuarios');
      expect(response?.status()).not.toBe(200);
    });
  }

  test('jugador no puede acceder a Admin Local', async ({ page }) => {
    await login(page, 'carlos@mifantasy.com');
    const response = await page.goto('/admin-local/dashboard');
    expect(response?.status()).not.toBe(200);
  });

  test('el filtro Alpine no ejecuta payloads XSS', async ({ page }) => {
    await login(page, 'admin@admin.es');
    await page.goto('/admin/usuarios');

    await page.locator('input[x-model="search"]').fill('<script>window.__xss_pwned = true;</script><img src=x onerror="window.__xss_pwned = true">');
    await expect.poll(() => page.evaluate(() => window.__xss_pwned)).toBeUndefined();
  });
});
