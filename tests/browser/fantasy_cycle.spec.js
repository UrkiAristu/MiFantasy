import { test, expect } from '@playwright/test';

async function login(page, email) {
  await page.goto('/login');
  await page.locator('#login').fill(email);
  await page.locator('#password').fill('password');
  await page.getByRole('button', { name: /entrar al juego/i }).click();
  await expect(page).toHaveURL(/\/home/);
}

test('Admin Local guarda resultados y el jugador consulta la clasificación', async ({ browser }) => {
  const adminContext = await browser.newContext();
  const adminPage = await adminContext.newPage();
  const adminErrors = [];
  adminPage.on('pageerror', error => adminErrors.push(error));

  await login(adminPage, 'b2badmin@mifantasy.com');
  await adminPage.goto('/admin-local/partidos');
  await expect(adminPage.locator('form[x-data="{ saving: false }"]')).toBeVisible();
  const form = adminPage.locator('form[x-data="{ saving: false }"]');
  const submitBtn = form.locator('button[type="submit"]');
  await expect(submitBtn).toContainText(/Guardar Jornada/);
  await submitBtn.click();
  expect(adminErrors).toHaveLength(0);
  await adminContext.close();

  const playerContext = await browser.newContext();
  const playerPage = await playerContext.newPage();
  const playerErrors = [];
  playerPage.on('pageerror', error => playerErrors.push(error));

  await login(playerPage, 'carlos@mifantasy.com');
  await playerPage.goto('/user/liguillas/1');
  await expect(playerPage.locator('#tab-pane-alineacion')).toBeVisible();
  await playerPage.locator('#tab-btn-clasificacion').click();
  await expect(playerPage.getByRole('columnheader', { name: /puntos/i })).toBeVisible();
  const livePoints = await playerPage.request.get('/user/liguillas/1/live-points');
  expect(livePoints.status()).toBe(200);
  expect((await livePoints.json())).toBeTruthy();
  expect(playerErrors).toHaveLength(0);
  await playerContext.close();
});
