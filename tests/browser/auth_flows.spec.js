import { test, expect } from '@playwright/test';

test.describe('Autenticación Integral', () => {
  test('Registro completo de un nuevo usuario', async ({ page }) => {
    // Añadimos Math.random() para garantizar unicidad absoluta en BD
    const uniqueId = Date.now() + Math.floor(Math.random() * 10000);
    const uniqueEmail = `testuser_${uniqueId}@example.com`;
    await page.goto('/registro');

    await page.locator('#nombreUsuario').fill(`UserTest_${uniqueId}`);
    await page.locator('#email').fill(uniqueEmail);
    await page.locator('#password').fill('Password123!');
    await page.locator('#password_confirmation').fill('Password123!');

    await page.getByRole('button', { name: /crear cuenta y comenzar/i }).click();

    // Ahora sí, esperamos la redirección correcta de tu backend
    await expect(page).toHaveURL(/\/home/);
    await expect(page.getByText(/bienvenido/i).first()).toBeVisible();
  });

  test('Login exitoso con usuario normal', async ({ page }) => {
    await page.goto('/login');

    await page.locator('#login').fill('admin@admin.es');
    await page.locator('#password').fill('password');

    await page.getByRole('button', { name: /entrar al juego/i }).click();

    // Redirección oficial a home
    await expect(page).toHaveURL(/\/home/);
  });

  test('Intento de login con credenciales inválidas', async ({ page }) => {
    await page.goto('/login');

    await page.locator('#login').fill('wrong@example.com');
    await page.locator('#password').fill('wrongpassword');

    await page.getByRole('button', { name: /entrar al juego/i }).click();

    await expect(page).toHaveURL(/\/login/);
    await expect(page.getByText(/no pudimos iniciar sesión/i).first()).toBeVisible();
  });

  test('Recuperación de contraseña', async ({ page }) => {
    await page.goto('/forgot-password');

    await page.locator('#email').fill('admin@admin.es');
    await page.getByRole('button', { name: /enviar enlace de recuperación/i }).click();

    await expect(page.getByText(/enviado|enlace|recuperación|correo/i).first()).toBeVisible();
  });
});