import { test, expect } from '@playwright/test';

test.describe('Panel de Administración Total (Admin Master)', () => {
  test('Acceso denegado a usuarios normales', async ({ page }) => {
    await page.goto('/zonaAdmin');
    await expect(page).not.toHaveURL(/\/zonaAdmin/);
  });

  test.describe('Autenticado como Admin', () => {
    test.beforeEach(async ({ page }) => {
      await page.goto('/login');
      await page.locator('#login').fill('admin@admin.es');
      await page.locator('#password').fill('password');
      await page.getByRole('button', { name: /entrar al juego/i }).click();
      await expect(page).toHaveURL(/\/(home|dashboard|zonaAdmin)/);
    });

    test('Dashboard Admin (/zonaAdmin)', async ({ page }) => {
      await page.goto('/zonaAdmin');
      await expect(page).toHaveURL(/\/zonaAdmin/);
      await expect(page.getByText(/Zona de Administración|Administración/i).first()).toBeVisible();
    });

    test('Auditoría de Rutas CRUD', async ({ page }) => {
      await page.goto('/admin/usuarios');
      await expect(page).toHaveURL(/\/admin\/usuarios/);
      await expect(page.getByText(/usuarios|usuario/i).first()).toBeVisible();

      await page.goto('/admin/liguillas');
      await expect(page).toHaveURL(/\/admin\/liguillas/);
      await expect(page.getByText(/liguillas|liguilla|ligas/i).first()).toBeVisible();

      await page.goto('/admin/torneos');
      await expect(page).toHaveURL(/\/admin\/torneos/);
      await expect(page.getByText(/torneos/i).first()).toBeVisible();

      await page.goto('/admin/equipos');
      await expect(page).toHaveURL(/\/admin\/equipos/);
      await expect(page.getByText(/equipos|equipo/i).first()).toBeVisible();

      await page.goto('/admin/jugadores');
      await expect(page).toHaveURL(/\/admin\/jugadores/);
      await expect(page.getByText(/jugadores|jugador/i).first()).toBeVisible();
    });

    test('Creación/Edición (Simulada)', async ({ page }) => {
      await page.goto('/admin/torneos');

      const createButton = page.getByRole('button', { name: /crear torneo/i }).first();

      await expect(createButton).toBeVisible();
      await createButton.click();

      await expect(page.getByRole('heading', { name: /crear torneo/i }).first()).toBeVisible();
    });
  });
});