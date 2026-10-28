<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Caja Fuerte Secreta 🔐</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        .animate-shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-950 via-indigo-950 to-gray-900 text-gray-100 min-h-screen flex flex-col font-sans"
      x-data="{
          enteredCode: '',
          secretCode: '6041',
          isUnlocked: false,
          isWrong: false,

          init() {
              // Comprobamos si ya la abrió antes en esta sesión/navegador
              if (localStorage.getItem('safe_unlocked') === 'true') {
                  this.isUnlocked = true;
              }
          },

          addDigit(digit) {
              if (this.enteredCode.length < 4 && !this.isUnlocked) {
                  this.enteredCode += digit;
                  if (this.enteredCode.length === 4) {
                      this.checkCode();
                  }
              }
          },

          clearCode() {
              if (!this.isUnlocked) {
                  this.enteredCode = '';
              }
          },

          checkCode() {
              if (this.enteredCode === this.secretCode) {
                  this.isUnlocked = true;
                  localStorage.setItem('safe_unlocked', 'true');
              } else {
                  this.isWrong = true;
                  setTimeout(() => {
                      this.enteredCode = '';
                      this.isWrong = false;
                  }, 2000);
              }
          }
      }">

    <!-- Header -->
    <header class="bg-black/40 backdrop-blur-md py-4 px-6 flex justify-between items-center border-b border-purple-900/50 relative z-10">
        <h1 class="text-xl md:text-2xl font-extrabold text-purple-300">🔐 La Caja Fuerte Secreta</h1>
        <a href="{{ route('dashboard') }}" class="text-xs bg-purple-900/60 hover:bg-purple-800 text-purple-200 font-semibold px-4 py-2 rounded-full transition border border-purple-700/50">
            ← Volver al inicio
        </a>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-grow flex items-center justify-center p-4">

        <!-- 🔒 ESTADO 1: LA CAJA CERRADA (Teclado Numérico) -->
        <div x-show="!isUnlocked"
             class="bg-black/60 backdrop-blur-xl border-2 border-purple-500/30 p-8 rounded-3xl shadow-2xl max-w-sm w-full text-center transition-all"
             :class="{ 'animate-shake border-red-500': isWrong }">

            <div class="w-16 h-16 bg-gradient-to-tr from-purple-600 to-indigo-600 text-white rounded-2xl mx-auto flex items-center justify-center text-3xl shadow-lg mb-4 border border-purple-400/40">
                🔒
            </div>

            <h2 class="text-2xl font-extrabold text-white mb-1">Introduce la clave</h2>

            <!-- Pantalla de dígitos -->
            <div class="flex justify-center gap-3 mb-6">
                <template x-for="i in 4">
                    <div class="w-12 h-14 bg-purple-950/80 border-2 rounded-2xl flex items-center justify-center text-2xl font-bold font-mono transition-colors"
                         :class="enteredCode.length >= i ? 'border-purple-400 text-white shadow-[0_0_15px_rgba(168,85,247,0.4)]' : 'border-purple-900/60 text-purple-700'"
                         x-text="enteredCode.length >= i ? '•' : '·'">
                    </div>
                </template>
            </div>

            <!-- Mensaje de error visual -->
            <div class="h-6 mb-2">
                <p x-show="isWrong" class="text-red-400 text-xs font-bold animate-pulse">❌ Código incorrecto. ¡Prueba otra vez!</p>
            </div>

            <!-- Teclado Numérico -->
            <div class="grid grid-cols-3 gap-3">
                <template x-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]">
                    <button @click="addDigit(num.toString())"
                            class="bg-purple-950/60 hover:bg-purple-800/80 border border-purple-800/50 text-white font-bold py-3.5 rounded-2xl text-xl shadow transition transform active:scale-95">
                        <span x-text="num"></span>
                    </button>
                </template>
                <button @click="clearCode()" class="bg-red-950/40 hover:bg-red-900/60 border border-red-800/40 text-red-300 font-bold py-3.5 rounded-2xl text-xs transition active:scale-95">
                    BORRAR
                </button>
                <button @click="addDigit('0')" class="bg-purple-950/60 hover:bg-purple-800/80 border border-purple-800/50 text-white font-bold py-3.5 rounded-2xl text-xl shadow transition active:scale-95">
                    0
                </button>
                <button @click="checkCode()" class="bg-purple-600 hover:bg-purple-500 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg transition active:scale-95 flex items-center justify-center">
                    ABRIR 🔓
                </button>
            </div>

        </div>

        <!-- 🔓 ESTADO 2: LA CAJA ABIERTA (Con premio físico y botón de canje) -->
        <div x-show="isUnlocked"
             style="display: none;"
             class="bg-black/70 backdrop-blur-xl border-2 border-purple-400/50 p-8 rounded-3xl shadow-[0_0_50px_rgba(168,85,247,0.3)] max-w-lg w-full text-center relative overflow-hidden"
             x-data="{ claimed: localStorage.getItem('vaiana_claimed') === 'true' }">

            <div class="absolute -top-10 -right-10 w-32 h-32 bg-purple-600/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="w-16 h-16 bg-gradient-to-tr from-purple-500 to-indigo-500 text-white rounded-2xl mx-auto flex items-center justify-center text-3xl shadow-xl mb-4 border border-white/20 animate-bounce">
                🌺
            </div>

            <h2 class="text-3xl font-extrabold text-purple-200 mb-2">¡Caja Fuerte Abierta!</h2>
            <p class="text-purple-300 text-xs mb-6"></p>

            <!-- 🎁 VALE ESPECIAL POR LOS SOBRES DE VAIANA -->
            <div class="bg-gradient-to-r from-purple-900/80 to-indigo-900/80 border-2 border-purple-400/40 rounded-2xl p-5 mb-6 text-center relative shadow-lg">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-purple-600 text-white text-[10px] font-extrabold uppercase px-3 py-1 rounded-full shadow border border-purple-300">
                    Premio Especial 🌊✨
                </div>

                <h3 class="text-lg font-bold text-white mt-2 mb-3">¡3 Sobres de Vaiana para ti!</h3>

                <!-- 🖼️ Imagen de Vaiana -->
                <div class="w-full flex justify-center mb-4">
                    <img src="{{ asset('images/safe/vaiana.jpeg') }}"
                         alt="Sobres de Vaiana"
                         class="w-32 h-32 object-cover rounded-xl border-2 border-purple-400/50 shadow-md">
                </div>

                <!-- Botón de canje -->
                <button @click="claimed = true; localStorage.setItem('vaiana_claimed', 'true')"
                        :disabled="claimed"
                        class="w-full font-bold py-3 px-4 rounded-xl shadow-md transition transform active:scale-95 text-sm flex items-center justify-center gap-2"
                        :class="claimed ? 'bg-emerald-600/80 text-white cursor-default' : 'bg-purple-500 hover:bg-purple-400 text-white hover:scale-105'">
                    <span x-text="claimed ? '✅ ¡Vale canjeado con éxito!' : '🎁 Canjear mis sobres ahora'"></span>
                </button>
            </div>

        </div>

</body>
</html>
