<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>10 Cosas que Amo de Ti ❤️</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- 🚀 Alpine.js para la interactividad de las tarjetas y el reproductor -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>

    <style>
        .perspective { perspective: 1000px; }
        .transform-style-3d { transform-style: preserve-3d; }
        .backface-hidden { backface-visibility: hidden; }
        .rotate-y-180 { transform: rotateY(180deg); }
    </style>
</head>
<body class="bg-rose-50 text-gray-800 min-h-screen py-10 px-4 font-sans">

    <div class="max-w-5xl mx-auto text-center" x-data="{ openedCount: 0, total: {{ count(array_filter($reasons, fn($r) => $r['unlocked'])) }} }">

        <!-- Header -->
        <h1 class="text-4xl md:text-5xl font-extrabold text-rose-600 mb-2">10 Cosas que Amo de Ti</h1>
        <p class="text-rose-400 mb-6">Una razón nueva disponible cada 24 horas ❤️</p>

        <!-- Botón para volver al menú principal -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-xs bg-white border border-rose-300 text-rose-500 px-4 py-2 rounded-full shadow-sm hover:bg-rose-50 transition">
                ← Volver al inicio
            </a>
        </div>

        <!-- Grid de Tarjetas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($reasons as $reason)
                <div class="perspective h-72">

                    @if($reason['unlocked'])
                        <!-- Tarjeta Desbloqueada -->
                        <div x-data="{ flipped: false }"
                             class="h-full cursor-pointer"
                             @click="if(!flipped) { flipped = true; openedCount++; if(openedCount === total) { confetti({ particleCount: 150, spread: 80, origin: { y: 0.6 } }); } }">

                            <div class="relative w-full h-full duration-500 transform-style-3d transition-transform shadow-xl rounded-2xl"
                                 :class="{ 'rotate-y-180': flipped }">

                                <!-- Cara Frontal -->
                                <div class="absolute w-full h-full bg-gradient-to-br from-rose-400 to-rose-600 rounded-2xl flex flex-col justify-center items-center text-white p-4 backface-hidden border-2 border-rose-300">
                                    <span class="text-5xl mb-3">{{ $reason['icon'] }}</span>
                                    <h3 class="text-3xl font-extrabold">#{{ $loop->iteration }}</h3>
                                    <p class="text-xs mt-3 opacity-90 tracking-wider">TOCA PARA REVELAR</p>
                                </div>

                                <!-- Cara Trasera -->
                                <div class="absolute w-full h-full bg-white rounded-2xl p-4 flex flex-col justify-between items-center rotate-y-180 backface-hidden border-2 border-rose-200">
                                    <img src="{{ $reason['image'] }}" alt="Foto" class="w-24 h-24 object-cover rounded-full shadow-md mt-2">
                                    <div class="text-center my-auto">
                                        <h4 class="font-bold text-rose-600 text-md">{{ $reason['title'] }}</h4>
                                        <p class="text-xs text-gray-600 mt-2 leading-tight">{{ $reason['description'] }}</p>
                                    </div>
                                    <span class="text-xs text-rose-400">❤️</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Tarjeta Bloqueada con Cuenta Atrás Interactiva -->
                        <div x-data="{
                                timeLeft: Math.floor({{ $reason['seconds_left'] }}),
                                init() {
                                    setInterval(() => {
                                        if (this.timeLeft > 0) this.timeLeft--;
                                    }, 1000);
                                },
                                get formattedTime() {
                                    if (this.timeLeft <= 0) {
                                        window.location.reload();
                                        return '¡Desbloqueando...';
                                    }
                                    let h = Math.floor(this.timeLeft / 3600);
                                    let m = Math.floor((this.timeLeft % 3600) / 60);
                                    let s = Math.floor(this.timeLeft % 60);

                                    let mStr = String(m).padStart(2, '0');
                                    let sStr = String(s).padStart(2, '0');

                                    return `${h}h ${mStr}m ${sStr}s`;
                                }
                             }"
                             class="w-full h-full bg-gray-100 rounded-2xl flex flex-col justify-center items-center text-gray-500 p-4 border-2 border-gray-300 shadow-inner cursor-not-allowed opacity-80"
                             @click="alert('Esta tarjeta se abrirá en cuanto termine la cuenta atrás ⏳')">

                            <span class="text-4xl mb-2">🔒</span>
                            <h3 class="text-2xl font-bold text-gray-700">#{{ $loop->iteration }}</h3>
                            <p class="text-xs mt-2 text-gray-500">Disponible en:</p>
                            <span class="text-sm font-mono font-bold text-rose-500 mt-1" x-text="formattedTime"></span>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

    </div>

    <!-- 🎵 Reproductor de Música con Fade In -->
    <div x-data="{
            isPlaying: false,
            audio: null,
            fadeInAudio() {
                this.audio.volume = 0; // Empezamos en silencio absoluto
                this.audio.play().then(() => {
                    this.isPlaying = true;
                    localStorage.setItem('music_is_playing', 'true');

                    // Efecto Fade In gradual (sube el volumen poco a poco)
                    let targetVolume = 0.05; // Volumen máximo deseado
                    let step = 0.001;        // Cantidad que sube en cada paso
                    let intervalTime = 100; // Velocidad del incremento (milisegundos)

                    let fadeInterval = setInterval(() => {
                        if (this.audio.volume < targetVolume - step) {
                            this.audio.volume += step;
                        } else {
                            this.audio.volume = targetVolume; // Fijamos el tope exacto
                            clearInterval(fadeInterval);
                        }
                    }, intervalTime);

                }).catch(e => {
                    this.isPlaying = false;
                });
            },
            init() {
                this.audio = document.getElementById('bg-music');

                let savedTime = localStorage.getItem('music_current_time');
                let wasPlaying = localStorage.getItem('music_is_playing') === 'true';

                if (savedTime) {
                    this.audio.currentTime = parseFloat(savedTime);
                }

                if (wasPlaying) {
                    this.fadeInAudio();
                }

                // Guardamos la posición exacta cada medio segundo
                setInterval(() => {
                    if (this.audio && !this.audio.paused) {
                        localStorage.setItem('music_current_time', this.audio.currentTime);
                    }
                }, 500);
            },
            togglePlay() {
                if (this.isPlaying) {
                    this.audio.pause();
                    this.isPlaying = false;
                    localStorage.setItem('music_is_playing', 'false');
                } else {
                    this.fadeInAudio(); // Aplica el fade in también al darle al botón manual
                }
            }
        }"
        class="fixed bottom-6 right-6 z-50">

        <!-- Elemento de audio -->
        <audio id="bg-music" loop>
            <source src="{{ asset('audio/cancion.mp3') }}" type="audio/mpeg">
            Tu navegador no soporta el elemento de audio.
        </audio>

        <!-- Botón flotante interactivo -->
        <button @click="togglePlay()"
                class="flex items-center gap-3 bg-white/90 backdrop-blur-md border-2 border-rose-300 text-rose-600 px-4 py-3 rounded-full shadow-xl hover:bg-rose-50 transition transform hover:scale-105">

            <div class="relative w-7 h-7 flex items-center justify-center">
                <span class="text-xl" :class="{ 'animate-spin': isPlaying }">💿</span>
            </div>

            <div class="text-left text-xs">
                <p class="font-bold text-gray-800" x-text="isPlaying ? 'Reproduciendo música...' : 'Pausado'"></p>
                <p class="text-rose-400" x-text="isPlaying ? 'Haz clic para pausar' : 'Haz clic para reproducir'"></p>
            </div>
        </button>
    </div>

</body>
</html>
