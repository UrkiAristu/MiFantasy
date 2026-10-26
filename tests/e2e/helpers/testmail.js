export function testmailAddress(tag) {
  const namespace = process.env.TESTMAIL_NAMESPACE || 'lm2f9';

  return `${namespace}.${tag}@inbox.testmail.app`;
}

export async function queryTestmail(tag) {
  const apiKey = process.env.TESTMAIL_API_KEY;
  if (!apiKey) {
    throw new Error('TESTMAIL_API_KEY is required to query testmail.app');
  }

  const namespace = process.env.TESTMAIL_NAMESPACE || 'lm2f9';
  const url = new URL('https://api.testmail.app/api/json');
  url.searchParams.set('namespace', namespace);
  url.searchParams.set('tag', tag);
  url.searchParams.set('livequery', 'true');

  const response = await fetch(url, {
    headers: { Authorization: `Bearer ${apiKey}` },
  });

  if (!response.ok) {
    throw new Error(`testmail.app returned HTTP ${response.status}`);
  }

  return response.json();
}
