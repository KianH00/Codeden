// service-worker.js

const CACHE_NAME = "v1_cache";
const URLS_TO_CACHE = [
  "/",
  "/index.html",
  "/home.php",
  "/styles.css",
  "/user.css", // Add your CSS file if needed
  "/script.js",
  "/user.js", // Add your JS file if needed
  "/imagess/logolog.png" // Example image
];

// Install Event - Cache Files
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("Opened cache");
      return cache.addAll(URLS_TO_CACHE);
    })
  );
});

// Activate Event - Cleanup Old Caches
self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log("Deleting old cache:", cache);
            return caches.delete(cache);
          }
        })
      )
    )
  );
});

// Fetch Event - Serve Cached Content
self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
