export function testmailAddress(tag) {
  const namespace = (process.env.TESTMAIL_NAMESPACE || 'lm2f9').trim();

  return `${namespace}.${tag}@inbox.testmail.app`;
}

export async function queryTestmail(tag) {
  const apiKey = (process.env.TESTMAIL_API_KEY || '').trim();
  if (!apiKey) {
    throw new Error('TESTMAIL_API_KEY is required to query testmail.app');
  }

  const namespace = (process.env.TESTMAIL_NAMESPACE || 'lm2f9').trim();
  const url = `https://api.testmail.app/api/json?apikey=${encodeURIComponent(apiKey)}&namespace=${encodeURIComponent(namespace)}&tag=${encodeURIComponent(tag)}`;

  const response = await fetch(url);

  if (!response.ok) {
    throw new Error(`testmail.app returned HTTP ${response.status}`);
  }

  return response.json();
}
