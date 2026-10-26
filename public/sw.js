const CACHE_NAME = 'mifantasy-static-v1';
const STATIC_ASSETS = ['/manifest.json', '/offline.html'];

self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS)));
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin || !isStaticAsset(url)) {
        return;
    }

    event.respondWith(
        fetch(request).then(response => {
            if (response.ok) {
                const copy = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
            }

            return response;
        }).catch(() => caches.match(request).then(cached => {
            if (cached) {
                return cached;
            }

            return request.destination === 'document' ? caches.match('/offline.html') : Response.error();
        }))
    );
});

function isStaticAsset(url) {
    return /\.(?:css|js|svg|png|jpg|jpeg|webp|woff2?)$/i.test(url.pathname);
}
