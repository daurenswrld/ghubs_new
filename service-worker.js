const CACHE_NAME = 'gh-cache-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/wp-content/themes/gymnastics_hub_new/style.css',
  '/wp-content/themes/gymnastics_hub_new/js/stefa.js',
  '/wp-content/themes/gymnastics_hub_new/img/app-icon-192.png',
  '/wp-content/themes/gymnastics_hub_new/img/app-icon-512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch(() => {});
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
