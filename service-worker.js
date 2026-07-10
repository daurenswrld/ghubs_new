const CACHE_NAME = 'gh-cache-v4';
const ASSETS_TO_CACHE = [
  './',
  './?source=pwa',
  'wp-content/themes/gymnastics_hub_new/style.css',
  'wp-content/themes/gymnastics_hub_new/js/stefa.js',
  'wp-content/themes/gymnastics_hub_new/img/app-icon-192.png?v=3',
  'wp-content/themes/gymnastics_hub_new/img/app-icon-512.png?v=3'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
        console.error('PWA Cache pre-adding failed:', err);
      });
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  // Only intercept GET requests
  if (event.request.method !== 'GET') return;

  // Do not intercept wp-admin or admin-ajax.php calls
  if (event.request.url.includes('/wp-admin') || event.request.url.includes('admin-ajax.php')) {
    return;
  }

  // Do not intercept chrome extension requests
  if (event.request.url.startsWith('chrome-extension://')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // Cache successful GET requests
        if (response && response.status === 200 && response.type === 'basic') {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        // Fallback to cache if network is down
        return caches.match(event.request);
      })
  );
});
