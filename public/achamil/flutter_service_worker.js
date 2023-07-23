'use strict';
const MANIFEST = 'flutter-app-manifest';
const TEMP = 'flutter-temp-cache';
const CACHE_NAME = 'flutter-app-cache';

const RESOURCES = {"assets/AssetManifest.bin": "90f40f65465f29d449da9ef8f476b5ec",
"assets/AssetManifest.json": "7ae6c3a6d03e39ecbf77211307068016",
"assets/assets/fonts/ChamilIcons.ttf": "753a5a2dce066754ae28c04397db7eec",
"assets/assets/fonts/elmessri/ElMessiri-Bold.ttf": "4498ccf4fc6a16e754a0da5d41a64a9c",
"assets/assets/fonts/elmessri/ElMessiri-Medium.ttf": "179378483f9ce4d232ac7c3480e37943",
"assets/assets/fonts/elmessri/ElMessiri-Regular.ttf": "0dfae81209fc5b5e0c3cff63db36b31b",
"assets/assets/i18n/ar.json": "a4faf9cbde0e2c9ccda2b0695e129924",
"assets/assets/i18n/en_US.json": "cecdf94dca0ced6e6b24f8e504460036",
"assets/assets/i18n/fr.json": "355dc15af14b0c6961bfbd79f66cc316",
"assets/assets/images/achamil_sd.png": "0851a73ffddb8ab6fdcd0304c36d85ea",
"assets/assets/images/app/logo-app.png": "b85e3eae8a5a870e2dfb267e64b38d64",
"assets/assets/images/arabe_img.png": "9af9a0496ad78812d1c4442227784828",
"assets/assets/images/card1_mokbil.png": "49af8ca7345c178013851faca7cf70c0",
"assets/assets/images/card2_ostad.png": "98061e82c27f09b460ee58fc3cc79411",
"assets/assets/images/card3_motalim.png": "32af4ac649bb3ff6ab8cf16eeca1fc96",
"assets/assets/images/folder.png": "903f48bef73afc54f147e4c693582d0a",
"assets/assets/images/french_img.png": "5368aff0923a5647c7c673bf9427c608",
"assets/assets/images/graph.png": "fb4b937b0764ae3c6e238795182a504b",
"assets/assets/images/logo_1.png": "a7b15fc52c1e3f7225c2c9465d5ab55b",
"assets/assets/images/math_img.png": "d6ed4754e2accf907dc0d55f538b17b1",
"assets/assets/images/mokbil_madakhil/madkhal_1.png": "e0499a0b68d41b66b92488354a5111bf",
"assets/assets/images/mokbil_madakhil/madkhal_2.png": "dc11d4f26a21768c335e0ad67929ec18",
"assets/assets/images/mokbil_madakhil/madkhal_3.png": "46633b6c68440752226c1866ff35dd5e",
"assets/assets/images/mokbil_madakhil/madkhal_4.png": "fa1bd7a0bff9449188bb783303d7923a",
"assets/assets/images/mokbil_madakhil/madkhal_5.png": "2483a7e5928361f91887021b1ebb1e35",
"assets/assets/images/mokbil_madakhil/madkhal_6.png": "55d5cbc5eb59dc0f32e3b6cdbb8b7440",
"assets/assets/images/mokbil_madakhil/madkhal_7.png": "be4fbea2b5807fb3b7c3820b12a426b4",
"assets/assets/images/mokbil_madakhil/madkhal_8.png": "d373b42e35a13bfea23304e5e41c5008",
"assets/assets/images/science_img.png": "a124bed176ee0db12cc71db133fcf404",
"assets/assets/images/signal.png": "888f84d3dead28c19ea6b88d1d46df4f",
"assets/assets/images/signal2.png": "1df8c14cba51b0d5bb389b0c09beb1de",
"assets/assets/images/userpic.png": "cfbd15d8453e9966bde3e980cb344847",
"assets/assets/pdf/khotata_01.pdf": "89ee87a2741dccdcb42554cbd80884ce",
"assets/assets/svg/leaf.svg": "48f86d48778c16a4807116e93394beae",
"assets/assets/svg/svg_fig.svg": "b24140138f9c52d293a6aef571e76b80",
"assets/FontManifest.json": "7c14f0c6b17cb67154cf2c33e36ad652",
"assets/fonts/MaterialIcons-Regular.otf": "7ac9888914f7227a4715c0deb209a662",
"assets/NOTICES": "ae9df90abb730bf880a0c23bd97712a4",
"assets/packages/cupertino_icons/assets/CupertinoIcons.ttf": "57d849d738900cfd590e9adc7e208250",
"assets/packages/font_awesome_flutter/lib/fonts/fa-brands-400.ttf": "0db203e8632f03baae0184700f3bda48",
"assets/packages/font_awesome_flutter/lib/fonts/fa-regular-400.ttf": "01bb14ae3f14c73ee03eed84f480ded9",
"assets/packages/font_awesome_flutter/lib/fonts/fa-solid-900.ttf": "efc6c90b58d765987f922c95c2031dd2",
"assets/shaders/ink_sparkle.frag": "f8b80e740d33eb157090be4e995febdf",
"canvaskit/canvaskit.js": "76f7d822f42397160c5dfc69cbc9b2de",
"canvaskit/canvaskit.wasm": "f48eaf57cada79163ec6dec7929486ea",
"canvaskit/chromium/canvaskit.js": "8c8392ce4a4364cbb240aa09b5652e05",
"canvaskit/chromium/canvaskit.wasm": "fc18c3010856029414b70cae1afc5cd9",
"canvaskit/skwasm.js": "1df4d741f441fa1a4d10530ced463ef8",
"canvaskit/skwasm.wasm": "6711032e17bf49924b2b001cef0d3ea3",
"canvaskit/skwasm.worker.js": "19659053a277272607529ef87acf9d8a",
"favicon.png": "108d5b267c4fd5c3c99fbce51852526e",
"flutter.js": "6b515e434cea20006b3ef1726d2c8894",
"icons/Icon-192.png": "d00a6639efc405656cba77c89bc4b0c2",
"icons/Icon-512.png": "101dcc66ebdad9efe21cf99f9381dbaf",
"icons/Icon-maskable-192.png": "d00a6639efc405656cba77c89bc4b0c2",
"icons/Icon-maskable-512.png": "101dcc66ebdad9efe21cf99f9381dbaf",
"index.html": "ad193cf37f1df9f6a43fe4d6b52149df",
"/": "ad193cf37f1df9f6a43fe4d6b52149df",
"main.dart.js": "bf401d8d9c72f67b21c935694636b043",
"manifest.json": "d41d8cd98f00b204e9800998ecf8427e",
"version.json": "f7038de1e3ece356a36ee500f5dbc5c1"};
// The application shell files that are downloaded before a service worker can
// start.
const CORE = ["main.dart.js",
"index.html",
"assets/AssetManifest.json",
"assets/FontManifest.json"];

// During install, the TEMP cache is populated with the application shell files.
self.addEventListener("install", (event) => {
  self.skipWaiting();
  return event.waitUntil(
    caches.open(TEMP).then((cache) => {
      return cache.addAll(
        CORE.map((value) => new Request(value, {'cache': 'reload'})));
    })
  );
});
// During activate, the cache is populated with the temp files downloaded in
// install. If this service worker is upgrading from one with a saved
// MANIFEST, then use this to retain unchanged resource files.
self.addEventListener("activate", function(event) {
  return event.waitUntil(async function() {
    try {
      var contentCache = await caches.open(CACHE_NAME);
      var tempCache = await caches.open(TEMP);
      var manifestCache = await caches.open(MANIFEST);
      var manifest = await manifestCache.match('manifest');
      // When there is no prior manifest, clear the entire cache.
      if (!manifest) {
        await caches.delete(CACHE_NAME);
        contentCache = await caches.open(CACHE_NAME);
        for (var request of await tempCache.keys()) {
          var response = await tempCache.match(request);
          await contentCache.put(request, response);
        }
        await caches.delete(TEMP);
        // Save the manifest to make future upgrades efficient.
        await manifestCache.put('manifest', new Response(JSON.stringify(RESOURCES)));
        // Claim client to enable caching on first launch
        self.clients.claim();
        return;
      }
      var oldManifest = await manifest.json();
      var origin = self.location.origin;
      for (var request of await contentCache.keys()) {
        var key = request.url.substring(origin.length + 1);
        if (key == "") {
          key = "/";
        }
        // If a resource from the old manifest is not in the new cache, or if
        // the MD5 sum has changed, delete it. Otherwise the resource is left
        // in the cache and can be reused by the new service worker.
        if (!RESOURCES[key] || RESOURCES[key] != oldManifest[key]) {
          await contentCache.delete(request);
        }
      }
      // Populate the cache with the app shell TEMP files, potentially overwriting
      // cache files preserved above.
      for (var request of await tempCache.keys()) {
        var response = await tempCache.match(request);
        await contentCache.put(request, response);
      }
      await caches.delete(TEMP);
      // Save the manifest to make future upgrades efficient.
      await manifestCache.put('manifest', new Response(JSON.stringify(RESOURCES)));
      // Claim client to enable caching on first launch
      self.clients.claim();
      return;
    } catch (err) {
      // On an unhandled exception the state of the cache cannot be guaranteed.
      console.error('Failed to upgrade service worker: ' + err);
      await caches.delete(CACHE_NAME);
      await caches.delete(TEMP);
      await caches.delete(MANIFEST);
    }
  }());
});
// The fetch handler redirects requests for RESOURCE files to the service
// worker cache.
self.addEventListener("fetch", (event) => {
  if (event.request.method !== 'GET') {
    return;
  }
  var origin = self.location.origin;
  var key = event.request.url.substring(origin.length + 1);
  // Redirect URLs to the index.html
  if (key.indexOf('?v=') != -1) {
    key = key.split('?v=')[0];
  }
  if (event.request.url == origin || event.request.url.startsWith(origin + '/#') || key == '') {
    key = '/';
  }
  // If the URL is not the RESOURCE list then return to signal that the
  // browser should take over.
  if (!RESOURCES[key]) {
    return;
  }
  // If the URL is the index.html, perform an online-first request.
  if (key == '/') {
    return onlineFirst(event);
  }
  event.respondWith(caches.open(CACHE_NAME)
    .then((cache) =>  {
      return cache.match(event.request).then((response) => {
        // Either respond with the cached resource, or perform a fetch and
        // lazily populate the cache only if the resource was successfully fetched.
        return response || fetch(event.request).then((response) => {
          if (response && Boolean(response.ok)) {
            cache.put(event.request, response.clone());
          }
          return response;
        });
      })
    })
  );
});
self.addEventListener('message', (event) => {
  // SkipWaiting can be used to immediately activate a waiting service worker.
  // This will also require a page refresh triggered by the main worker.
  if (event.data === 'skipWaiting') {
    self.skipWaiting();
    return;
  }
  if (event.data === 'downloadOffline') {
    downloadOffline();
    return;
  }
});
// Download offline will check the RESOURCES for all files not in the cache
// and populate them.
async function downloadOffline() {
  var resources = [];
  var contentCache = await caches.open(CACHE_NAME);
  var currentContent = {};
  for (var request of await contentCache.keys()) {
    var key = request.url.substring(origin.length + 1);
    if (key == "") {
      key = "/";
    }
    currentContent[key] = true;
  }
  for (var resourceKey of Object.keys(RESOURCES)) {
    if (!currentContent[resourceKey]) {
      resources.push(resourceKey);
    }
  }
  return contentCache.addAll(resources);
}
// Attempt to download the resource online before falling back to
// the offline cache.
function onlineFirst(event) {
  return event.respondWith(
    fetch(event.request).then((response) => {
      return caches.open(CACHE_NAME).then((cache) => {
        cache.put(event.request, response.clone());
        return response;
      });
    }).catch((error) => {
      return caches.open(CACHE_NAME).then((cache) => {
        return cache.match(event.request).then((response) => {
          if (response != null) {
            return response;
          }
          throw error;
        });
      });
    })
  );
}
