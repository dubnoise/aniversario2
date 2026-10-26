<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Para mi persona favorita ❤️</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- 🚀 AÑADIDO: Alpine.js para que el reproductor funcione -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-rose-50 text-gray-800 min-h-screen flex items-center justify-center px-4 font-sans">

    <div class="bg-white p-8 md:p-12 rounded-3xl shadow-2xl max-w-lg w-full text-center border-2 border-rose-200">

        <span class="text-6xl mb-4 inline-block animate-bounce">💖</span>

        <h1 class="text-3xl md:text-4xl font-extrabold text-rose-600 mb-3">¡Hola, mi amor!</h1>
        <p class="text-gray-600 text-sm md:text-base mb-6 leading-relaxed">
            Has entrado en un rincón muy especial creado solo para ti. Un pequeño recordatorio de todo lo que significas para mí.
        </p>

        <!-- Contador de tiempo juntos desglosado -->
        <div class="bg-rose-50 border border-rose-200 py-3 px-4 rounded-2xl mb-8 shadow-inner">
            <p class="text-xs text-rose-400 font-semibold uppercase tracking-wider mb-1">Nuestro tiempo juntos</p>
            <p class="text-rose-700 font-bold text-sm md:text-base">
                @if($years > 0)
                    {{ $years }} {{ $years == 1 ? 'año' : 'años' }},
                @endif
                @if($months > 0 || $years > 0)
                    {{ $months }} {{ $months == 1 ? 'mes' : 'meses' }} y
                @endif
                {{ $days }} {{ $days == 1 ? 'día' : 'días' }}
            </p>
        </div>

        <!-- Botón hacia la página de tarjetas -->
        <div class="space-y-3">
            <a href="{{ route('gift.board') }}"
               class="block w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg transition transform hover:-translate-y-0.5">
                Ver las 10 razones de mi amor 🎁
            </a>

            <a href="{{ route('gift') }}"
               class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 px-6 rounded-xl text-xs transition">
                🔒 Bloquear y salir
            </a>
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
