/**
 * Service Worker for Sellora Location Tracker PWA
 * 
 * Provides caching for offline functionality and improved performance.
 * Note: This does NOT provide background location tracking when app is closed,
 * as browsers restrict this functionality for privacy and battery reasons.
 */

const CACHE_NAME = 'sellora-tracker-v1.0.0';
const STATIC_CACHE_NAME = 'sellora-static-v1.0.0';
const DYNAMIC_CACHE_NAME = 'sellora-dynamic-v1.0.0';

// Files to cache immediately on install
const STATIC_FILES = [
  '/',
  '/track',
  '/team-map',
  '/manifest.json',
  'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
  'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
  'https://cdn.tailwindcss.com',
  // Add your app's CSS and JS files here
];

// API endpoints to cache with network-first strategy
const API_ENDPOINTS = [
  '/api/locations/latest',
  '/api/locations/history',
  '/api/health'
];

// Maximum cache size for dynamic content
const MAX_DYNAMIC_CACHE_SIZE = 50;

/**
 * Install Event
 * Pre-cache static assets
 */
self.addEventListener('install', event => {
  console.log('[SW] Installing service worker...');
  
  event.waitUntil(
    caches.open(STATIC_CACHE_NAME)
      .then(cache => {
        console.log('[SW] Pre-caching static assets');
        return cache.addAll(STATIC_FILES);
      })
      .then(() => {
        console.log('[SW] Static assets cached successfully');
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('[SW] Failed to cache static assets:', error);
      })
  );
});

/**
 * Activate Event
 * Clean up old caches
 */
self.addEventListener('activate', event => {
  console.log('[SW] Activating service worker...');
  
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName !== STATIC_CACHE_NAME && 
                cacheName !== DYNAMIC_CACHE_NAME) {
              console.log('[SW] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('[SW] Service worker activated');
        return self.clients.claim();
      })
  );
});

/**
 * Fetch Event
 * Handle network requests with appropriate caching strategies
 */
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }
  
  // Handle different types of requests
  if (isStaticAsset(request)) {
    event.respondWith(handleStaticAsset(request));
  } else if (isAPIRequest(request)) {
    event.respondWith(handleAPIRequest(request));
  } else if (isPageRequest(request)) {
    event.respondWith(handlePageRequest(request));
  } else {
    event.respondWith(handleOtherRequest(request));
  }
});

/**
 * Check if request is for a static asset
 */
function isStaticAsset(request) {
  const url = new URL(request.url);
  return url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/) ||
         url.hostname === 'unpkg.com' ||
         url.hostname === 'cdn.tailwindcss.com';
}

/**
 * Check if request is for API endpoint
 */
function isAPIRequest(request) {
  const url = new URL(request.url);
  return url.pathname.startsWith('/api/');
}

/**
 * Check if request is for a page
 */
function isPageRequest(request) {
  const url = new URL(request.url);
  return request.headers.get('accept')?.includes('text/html');
}

/**
 * Handle static assets with cache-first strategy
 */
function handleStaticAsset(request) {
  return caches.match(request)
    .then(cachedResponse => {
      if (cachedResponse) {
        return cachedResponse;
      }
      
      return fetch(request)
        .then(networkResponse => {
          if (networkResponse.ok) {
            const responseClone = networkResponse.clone();
            caches.open(STATIC_CACHE_NAME)
              .then(cache => cache.put(request, responseClone));
          }
          return networkResponse;
        });
    })
    .catch(() => {
      // Return a fallback for failed static asset requests
      if (request.url.includes('.css')) {
        return new Response('/* Offline fallback CSS */', {
          headers: { 'Content-Type': 'text/css' }
        });
      }
      if (request.url.includes('.js')) {
        return new Response('// Offline fallback JS', {
          headers: { 'Content-Type': 'application/javascript' }
        });
      }
    });
}

/**
 * Handle API requests with network-first strategy
 */
function handleAPIRequest(request) {
  return fetch(request)
    .then(networkResponse => {
      if (networkResponse.ok) {
        const responseClone = networkResponse.clone();
        caches.open(DYNAMIC_CACHE_NAME)
          .then(cache => {
            cache.put(request, responseClone);
            limitCacheSize(DYNAMIC_CACHE_NAME, MAX_DYNAMIC_CACHE_SIZE);
          });
      }
      return networkResponse;
    })
    .catch(() => {
      // Return cached version if network fails
      return caches.match(request)
        .then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          
          // Return offline fallback for API requests
          return new Response(JSON.stringify({
            error: 'Offline',
            message: 'You are currently offline. Please check your connection.',
            offline: true
          }), {
            status: 503,
            headers: {
              'Content-Type': 'application/json'
            }
          });
        });
    });
}

/**
 * Handle page requests with network-first strategy
 */
function handlePageRequest(request) {
  return fetch(request)
    .then(networkResponse => {
      if (networkResponse.ok) {
        const responseClone = networkResponse.clone();
        caches.open(DYNAMIC_CACHE_NAME)
          .then(cache => cache.put(request, responseClone));
      }
      return networkResponse;
    })
    .catch(() => {
      // Return cached version or offline page
      return caches.match(request)
        .then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          
          // Return offline fallback page
          return caches.match('/track')
            .then(fallback => {
              return fallback || new Response(`
                <!DOCTYPE html>
                <html>
                <head>
                  <title>Offline - Sellora Tracker</title>
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    .offline { color: #666; }
                  </style>
                </head>
                <body>
                  <div class="offline">
                    <h1>You're Offline</h1>
                    <p>Please check your internet connection and try again.</p>
                    <button onclick="window.location.reload()">Retry</button>
                  </div>
                </body>
                </html>
              `, {
                headers: { 'Content-Type': 'text/html' }
              });
            });
        });
    });
}

/**
 * Handle other requests with network-only strategy
 */
function handleOtherRequest(request) {
  return fetch(request);
}

/**
 * Limit cache size by removing oldest entries
 */
function limitCacheSize(cacheName, maxSize) {
  caches.open(cacheName)
    .then(cache => {
      cache.keys()
        .then(keys => {
          if (keys.length > maxSize) {
            // Remove oldest entries
            const keysToDelete = keys.slice(0, keys.length - maxSize);
            keysToDelete.forEach(key => cache.delete(key));
          }
        });
    });
}

/**
 * Background Sync Event (for future enhancement)
 * Note: Background sync has limited browser support and restrictions
 */
self.addEventListener('sync', event => {
  console.log('[SW] Background sync event:', event.tag);
  
  if (event.tag === 'location-sync') {
    event.waitUntil(
      // In a real implementation, you would sync pending location data
      // when the device comes back online
      console.log('[SW] Syncing pending location data...')
    );
  }
});

/**
 * Push Event (for future enhancement)
 * Handle push notifications
 */
self.addEventListener('push', event => {
  console.log('[SW] Push event received');
  
  if (event.data) {
    const data = event.data.json();
    
    const options = {
      body: data.body || 'New notification from Sellora Tracker',
      icon: '/icons/icon-192x192.png',
      badge: '/icons/icon-72x72.png',
      tag: data.tag || 'sellora-notification',
      requireInteraction: false,
      actions: [
        {
          action: 'open',
          title: 'Open App'
        },
        {
          action: 'dismiss',
          title: 'Dismiss'
        }
      ]
    };
    
    event.waitUntil(
      self.registration.showNotification(
        data.title || 'Sellora Tracker',
        options
      )
    );
  }
});

/**
 * Notification Click Event
 */
self.addEventListener('notificationclick', event => {
  console.log('[SW] Notification clicked:', event.action);
  
  event.notification.close();
  
  if (event.action === 'open' || !event.action) {
    event.waitUntil(
      clients.openWindow('/track')
    );
  }
});

/**
 * Message Event
 * Handle messages from the main app
 */
self.addEventListener('message', event => {
  console.log('[SW] Message received:', event.data);
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'GET_VERSION') {
    event.ports[0].postMessage({
      version: CACHE_NAME
    });
  }
});

console.log('[SW] Service worker script loaded');