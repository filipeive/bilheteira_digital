self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('bilhetes-cache-v1').then((cache) => {
            return cache.addAll([
                '/',
                '/validar',
                '/manifest.json',
                '/build/manifest.json'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    // Basic network-first strategy for the API and pages, fallback to cache
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
