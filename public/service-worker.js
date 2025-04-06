const CACHE_NAME = 'discursivamente-cache-v1';
const OFFLINE_URL = '/offline.php'; // ou '/offline.html'

const urlsToCache = [
  '/',
  '/public/css/estilos.css',
  '/public/js/main.js',
  '/site.webmanifest',
  OFFLINE_URL
];

// Instalação do Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(urlsToCache);
    })
  );
});

// Ativação e limpeza de caches antigos
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Intercepta requisições para servir do cache
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      // Se tiver no cache, retorna; se não, faz fetch na rede
      return response || fetch(event.request).catch(() => {
        // Se a requisição for HTML, retorna a página offline
        if (event.request.destination === 'document') {
          return caches.match(OFFLINE_URL);
        }
      });
    })
  );
});
