import { test, expect } from '@playwright/test';

test.describe('Flujo del Jugador (User Experience)', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.locator('#login').fill('carlos@mifantasy.com');
    await page.locator('#password').fill('password');
    await page.getByRole('button', { name: /entrar al juego/i }).click();

    // El robot ahora sabe que vas a mandarle a /home
    await expect(page).toHaveURL(/\/home/);
  });

  test('Navegación del Dashboard (/home)', async ({ page }) => {
    await expect(page.getByText(/mis liguillas/i).first()).toBeVisible();
    await expect(page.getByText(/crear liguilla/i).first()).toBeVisible();
    await expect(page.getByText(/unirme a liguilla/i).first()).toBeVisible();
  });

  test('Flujo de Liguillas (Listado)', async ({ page }) => {
    await page.goto('/user/liguillas');
    await expect(page).toHaveURL(/\/user\/liguillas/);

    await expect(page.getByText(/competición fantasy/i).first()).toBeVisible();
  });

  test('Flujo del Detalle de Liguilla (Tabs: Alineación, Plantilla, Clasificación)', async ({ page }) => {
    await page.goto('/user/liguillas/1');
    await expect(page).toHaveURL(/\/user\/liguillas\/1/);

    // 1. Tab Alineación
    await expect(page.getByRole('button', { name: /guardar alineación/i }).first()).toBeVisible();

    // 2. Tab Plantilla
    await page.locator('#tab-btn-plantilla').click();
    await expect(page.getByRole('heading', { name: /mi plantilla/i }).first()).toBeVisible();

    // 3. Tab Clasificación
    await page.locator('#tab-btn-clasificacion').click();
    await expect(page.getByRole('heading', { name: /tabla de clasificación/i }).first()).toBeVisible();

    await expect(page.getByRole('columnheader', { name: /pos/i }).first()).toBeVisible();
    await expect(page.getByRole('columnheader', { name: /manager/i }).first()).toBeVisible();
    await expect(page.getByRole('columnheader', { name: /puntos/i }).first()).toBeVisible();
  });
});