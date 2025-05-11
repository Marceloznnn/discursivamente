const CACHE_NAME = 'discursivamente-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/index.php',
  '/assets/css/global.css',
  '/assets/js/global.js',
  '/assets/images/logo/favicon-192x192.png',
  '/assets/images/logo/favicon-512x512.png',
  '/offline.html'
];

// Instala e armazena os arquivos em cache
self.addEventListener('install', event => {
  console.log('[SW] Instalando...');
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

// Remove caches antigos quando uma nova versão é ativada
self.addEventListener('activate', event => {
  console.log('[SW] Ativando...');
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
      )
    )
  );
});

// Intercepta as requisições e responde com cache ou rede
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      // Se tiver cache, retorna
      if (cachedResponse) return cachedResponse;

      // Se não tiver, busca da rede
      return fetch(event.request).catch(() => {
        // Se falhar e for navegação (HTML), exibe página offline
        if (event.request.mode === 'navigate') {
          return caches.match('/offline.html');
        }
      });
    })
  );
});
