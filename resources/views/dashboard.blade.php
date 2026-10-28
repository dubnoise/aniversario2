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

            <a href="{{ route('gift.map') }}"
            class="block w-full bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold py-3.5 px-6 rounded-2xl shadow transition text-center mb-3">
                🗺️ Ver nuestros lugares especiales
            </a>

            <a href="{{ route('gift.safe') }}"
            class="block w-full bg-purple-950/60 hover:bg-purple-900 border border-purple-700/50 text-purple-200 font-bold py-3.5 px-6 rounded-2xl shadow transition text-center mb-3">
                🔐 Abrir la caja fuerte secreta
            </a>

            <a href="{{ route('gift') }}"
               class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 px-6 rounded-xl text-xs transition">
                🔒 Bloquear y salir
            </a>
        </div>
    </div>
</body>
</html>
