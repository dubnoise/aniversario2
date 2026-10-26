<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Una sorpresa para ti ❤️</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-rose-100 flex items-center justify-center min-h-screen px-4 font-sans">
    <div class="bg-white p-8 rounded-3xl shadow-2xl max-w-md w-full text-center border border-rose-200">
        <div class="text-6xl mb-4">🔐</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Acceso Reservado</h1>
        <p class="text-gray-600 mb-6 text-sm">Introduce el código secreto para desbloquear tu regalo.</p>

        <form action="{{ route('login.verify') }}" method="POST" class="space-y-4">
            @csrf
            <input type="password" name="code" placeholder="Código Secreto" autofocus
                   class="w-full text-center text-xl tracking-widest px-4 py-3 border-2 border-rose-300 rounded-xl focus:outline-none focus:border-rose-500 transition">

            @error('code')
                <p class="text-rose-500 text-xs font-semibold mt-1">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-3 rounded-xl shadow-lg transition">
                Desbloquear Regalo ❤️
            </button>
        </form>
    </div>
</body>
</html>
