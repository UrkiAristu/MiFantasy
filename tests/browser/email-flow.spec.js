import { test, expect } from '@playwright/test';
import { queryTestmail, testmailAddress } from '../e2e/helpers/testmail.js';

test('consulta la bandeja dinámica de testmail.app', async () => {
  test.skip(!process.env.TESTMAIL_API_KEY, 'TESTMAIL_API_KEY no está definida');

  try {
    const tag = `e2e-${Date.now()}`;
    const inbox = await queryTestmail(tag);

    expect(testmailAddress(tag)).toBe(`lm2f9.${tag}@inbox.testmail.app`);
    expect(inbox).toBeTruthy();
  } catch (error) {
    if (error.message.includes('testmail.app returned HTTP 400') || error.message.includes('HTTP 401') || error.message.includes('HTTP 403')) {
      test.skip(true, 'La API de testmail.app no está disponible o el token es de prueba/inválido');
    } else {
      throw error;
    }
  }
});
