<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros Lugares Especiales 🗺️</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- 🌍 Leaflet CSS para el mapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body class="bg-rose-50 text-gray-800 min-h-screen flex flex-col font-sans"
      x-data="{
          activePlace: null,
          currentIndex: 0,
          fullscreen: false,
          zoomScale: 1,

          prevMedia() {
              if (!this.activePlace) return;
              this.currentIndex = (this.currentIndex === 0) ? this.activePlace.media.length - 1 : this.currentIndex - 1;
              this.resetZoom();
          },
          nextMedia() {
              if (!this.activePlace) return;
              this.currentIndex = (this.currentIndex === this.activePlace.media.length - 1) ? 0 : this.currentIndex + 1;
              this.resetZoom();
          },
          zoomIn() {
              if (this.zoomScale < 3) this.zoomScale += 0.5;
          },
          zoomOut() {
              if (this.zoomScale > 1) this.zoomScale -= 0.5;
          },
          resetZoom() {
              this.zoomScale = 1;
          },
          closeAll() {
              this.fullscreen = false;
              this.activePlace = null;
              this.resetZoom();
          }
      }"
      @open-place.window="activePlace = $event.detail; currentIndex = 0; fullscreen = false; resetZoom();"
      @keydown.escape.window="if(fullscreen) { fullscreen = false; resetZoom(); } else { closeAll(); }"
      @keydown.left.window="if(activePlace) prevMedia()"
      @keydown.right.window="if(activePlace) nextMedia()">

    <!-- Header -->
    <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center border-b border-rose-200 relative z-10">
        <h1 class="text-xl md:text-2xl font-extrabold text-rose-600">🗺️ Nuestros Viajecitos</h1>
        <a href="{{ route('dashboard') }}" class="text-xs bg-rose-100 text-rose-600 font-semibold px-4 py-2 rounded-full hover:bg-rose-200 transition">
            ← Volver al inicio
        </a>
    </header>

    <!-- Contenedor del Mapa -->
    <div class="flex-grow relative">
        <div id="map" class="w-full h-[calc(100vh-73px)] z-0"></div>
    </div>

    <!-- 🖼️ MODAL DE ÁLBUM (VISTA PRINCIPAL DEL LUGAR) -->
    <div x-show="activePlace !== null && !fullscreen"
         style="display: none;"
         class="fixed inset-0 z-40 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">

        <div @click.away="closeAll()"
             class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border-2 border-rose-200 relative flex flex-col items-center max-h-[95vh] overflow-y-auto">

            <!-- Botón cerrar -->
            <button @click="closeAll()" class="absolute top-4 right-4 bg-gray-100 hover:bg-rose-100 text-gray-600 hover:text-rose-600 w-9 h-9 rounded-full flex items-center justify-center font-bold transition z-10 shadow">
                ✕
            </button>

            <!-- Título y Descripción -->
            <h3 class="text-2xl font-extrabold text-rose-600 mb-1 text-center pr-10 pl-4" x-text="activePlace ? activePlace.title : ''"></h3>
            <p class="text-gray-500 text-xs mb-4 text-center px-4" x-text="activePlace ? activePlace.description : ''"></p>

            <!-- Visor Principal con opción de clic para Ampliar -->
            <div class="relative w-full h-[380px] sm:h-[450px] bg-black/90 rounded-2xl overflow-hidden flex items-center justify-center mb-4 border border-rose-100 shadow-inner group">

                <template x-if="activePlace && activePlace.media.length > 0">
                    <div class="w-full h-full flex items-center justify-center cursor-pointer" @click="fullscreen = true">
                        <template x-if="activePlace.media[currentIndex].type === 'image'">
                            <img :src="'{{ asset('') }}' + activePlace.media[currentIndex].url"
                                 class="max-h-full max-w-full object-contain transition transform group-hover:scale-105 duration-300">
                        </template>
                        <template x-if="activePlace.media[currentIndex].type === 'video'">
                            <video :src="'{{ asset('') }}' + activePlace.media[currentIndex].url" controls class="max-h-full max-w-full object-contain"></video>
                        </template>

                        <!-- Indicador flotante sobre la foto -->
                        <div x-show="activePlace.media[currentIndex].type === 'image'"
                             class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5 opacity-90 group-hover:opacity-100 group-hover:bg-rose-600 transition">
                            <span>🔍</span> <span>Toca para ver más grande</span>
                        </div>
                    </div>
                </template>

                <!-- Botones Flecha Anterior / Siguiente -->
                <template x-if="activePlace && activePlace.media.length > 1">
                    <div class="absolute inset-0 flex justify-between items-center px-3 pointer-events-none">
                        <button @click.stop="prevMedia()"
                                class="pointer-events-auto bg-white/80 hover:bg-white text-rose-600 w-12 h-12 rounded-full shadow-lg flex items-center justify-center text-lg font-bold transition transform hover:scale-110">
                            ❮
                        </button>
                        <button @click.stop="nextMedia()"
                                class="pointer-events-auto bg-white/80 hover:bg-white text-rose-600 w-12 h-12 rounded-full shadow-lg flex items-center justify-center text-lg font-bold transition transform hover:scale-110">
                            ❯
                        </button>
                    </div>
                </template>
            </div>

            <!-- Contador de fotos -->
            <div class="text-xs font-bold text-rose-500 mb-3" x-text="activePlace ? (currentIndex + 1 + ' de ' + activePlace.media.length) : ''"></div>

            <!-- Miniaturas para seleccionar -->
            <div class="flex gap-2.5 overflow-x-auto max-w-full pb-2 px-1 w-full justify-center">
                <template x-for="(media, index) in (activePlace ? activePlace.media : [])">
                    <button @click="currentIndex = index; resetZoom();"
                            class="w-16 h-16 rounded-xl overflow-hidden border-2 transition transform hover:scale-105 flex-shrink-0 relative shadow-sm"
                            :class="currentIndex === index ? 'border-rose-500 scale-105 ring-2 ring-rose-300' : 'border-gray-200 opacity-60'">
                        <template x-if="media.type === 'image'">
                            <img :src="'{{ asset('') }}' + media.url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="media.type === 'video'">
                            <div class="w-full h-full bg-gray-900 flex items-center justify-center text-white text-[10px] font-bold">▶ Vídeo</div>
                        </template>
                    </button>
                </template>
            </div>

        </div>
    </div>

    <!-- 🔍 VISOR A PANTALLA COMPLETA (LIGHTBOX CON ZOOM) -->
    <div x-show="fullscreen"
         style="display: none;"
         class="fixed inset-0 z-50 bg-black/95 flex flex-col justify-between items-center p-4 backdrop-blur-xl">

        <!-- Barra Superior de Herramientas -->
        <div class="w-full max-w-5xl flex justify-between items-center text-white z-10 pt-2 px-2">
            <div>
                <p class="font-bold text-sm text-rose-300" x-text="activePlace ? activePlace.title : ''"></p>
                <p class="text-xs text-gray-400" x-text="activePlace ? (currentIndex + 1 + ' / ' + activePlace.media.length) : ''"></p>
            </div>

            <!-- Botones de Zoom y Cierre -->
            <div class="flex items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md rounded-full px-3 py-1 flex items-center gap-2 border border-white/10">
                    <button @click="zoomOut()" class="w-7 h-7 flex items-center justify-center font-bold text-lg hover:text-rose-400 transition" title="Reducir">-</button>
                    <span class="text-xs font-mono w-12 text-center" x-text="Math.round(zoomScale * 100) + '%'"></span>
                    <button @click="zoomIn()" class="w-7 h-7 flex items-center justify-center font-bold text-lg hover:text-rose-400 transition" title="Aumentar">+</button>
                </div>

                <button @click="fullscreen = false; resetZoom();"
                        class="bg-white/10 hover:bg-rose-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold transition border border-white/10">
                    ✕
                </button>
            </div>
        </div>

        <!-- Imagen en Gran Tamaño (Soporta Zoom) -->
        <div class="relative w-full flex-grow flex items-center justify-center overflow-hidden my-4">
            <template x-if="activePlace && activePlace.media.length > 0">
                <div class="w-full h-full flex items-center justify-center">
                    <template x-if="activePlace.media[currentIndex].type === 'image'">
                        <img :src="'{{ asset('') }}' + activePlace.media[currentIndex].url"
                             class="max-h-[85vh] max-w-[90vw] object-contain transition-transform duration-200 select-none shadow-2xl rounded-lg"
                             :style="'transform: scale(' + zoomScale + '); cursor: ' + (zoomScale > 1 ? 'grab' : 'zoom-in')"
                             @click="if(zoomScale === 1) zoomIn(); else resetZoom();">
                    </template>
                    <template x-if="activePlace.media[currentIndex].type === 'video'">
                        <video :src="'{{ asset('') }}' + activePlace.media[currentIndex].url" controls class="max-h-[85vh] max-w-[90vw] object-contain rounded-lg"></video>
                    </template>
                </div>
            </template>

            <!-- Flechas de navegación en Pantalla Completa -->
            <template x-if="activePlace && activePlace.media.length > 1">
                <div class="absolute inset-0 flex justify-between items-center px-4 pointer-events-none">
                    <button @click="prevMedia()"
                            class="pointer-events-auto bg-black/50 hover:bg-rose-600 text-white w-14 h-14 rounded-full border border-white/20 flex items-center justify-center text-xl font-bold transition transform hover:scale-110 shadow-2xl">
                        ❮
                    </button>
                    <button @click="nextMedia()"
                            class="pointer-events-auto bg-black/50 hover:bg-rose-600 text-white w-14 h-14 rounded-full border border-white/20 flex items-center justify-center text-xl font-bold transition transform hover:scale-110 shadow-2xl">
                        ❯
                    </button>
                </div>
            </template>
        </div>

        <!-- Miniaturas en la parte inferior de la Pantalla Completa -->
        <div class="flex gap-2 overflow-x-auto max-w-md pb-2 px-2 w-full justify-center z-10">
            <template x-for="(media, index) in (activePlace ? activePlace.media : [])">
                <button @click="currentIndex = index; resetZoom();"
                        class="w-12 h-12 rounded-lg overflow-hidden border transition transform hover:scale-105 flex-shrink-0"
                        :class="currentIndex === index ? 'border-rose-500 ring-2 ring-rose-400 opacity-100 scale-105' : 'border-white/20 opacity-40 hover:opacity-80'">
                    <template x-if="media.type === 'image'">
                        <img :src="'{{ asset('') }}' + media.url" class="w-full h-full object-cover">
                    </template>
                    <template x-if="media.type === 'video'">
                        <div class="w-full h-full bg-gray-900 flex items-center justify-center text-white text-[9px] font-bold">▶</div>
                    </template>
                </button>
            </template>
        </div>

    </div>

    <!-- 🌍 Leaflet JS y Lógica del Mapa -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('map').setView([40.4168, -3.7038], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const places = @json($places);

            places.forEach(place => {
                const marker = L.marker([place.lat, place.lng]).addTo(map);

                marker.on('click', () => {
                    window.dispatchEvent(new CustomEvent('open-place', { detail: place }));
                });
            });
        });
    </script>
</body>
</html>
