<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Caja Fuerte 🔐</title>
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
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-950 via-indigo-950 to-gray-900 text-gray-100 min-h-screen flex flex-col font-sans"
      x-data="{
          enteredCode: '',
          secretCode: '2716',
          isUnlocked: false,
          isWrong: false,

          // 📅 Fecha y hora fijas de inicio (29 de noviembre de 2026 a las 17:00 CET)
          hintsStartIso: '2026-11-29T17:00:00+01:00',
          hintsStartTime: 0,

          // Estado para el Pop-up de pistas cada 24h
          showHints: false,
          hintIntervalMs: 24 * 60 * 60 * 1000, // 24 horas en milisegundos
          now: Date.now(),
          unlockedHints: 0,
          countdownText: '',
          hints: [
              '1️⃣ Primera pista: Las manzanas del desayuno de Cuenca.',
              '2️⃣ Segunda pista: La suma de los dígitos de nuestro portal en Murcia.',
              '3️⃣ Tercera pista: Las veces que salimos juntos en las fotos de Altea. (La 27 y 28 cuentan)',
          ],

          init() {
              if (localStorage.getItem('safe_unlocked') === 'true') {
                  this.isUnlocked = true;
              }

              const parsedStart = Date.parse(this.hintsStartIso);
              this.hintsStartTime = Number.isNaN(parsedStart) ? Date.now() : parsedStart;

              this.updateHintProgress();

              setInterval(() => {
                  this.now = Date.now();
                  this.updateHintProgress();
              }, 1000);
          },

          updateHintProgress() {
              // Si aún no ha llegado la fecha/hora de inicio
              if (this.now < this.hintsStartTime) {
                  this.unlockedHints = 0;
                  const remainingMs = this.hintsStartTime - this.now;
                  this.countdownText = `Las pistas empiezan en ${this.formatDuration(remainingMs)}`;
                  return;
              }

              const elapsed = this.now - this.hintsStartTime;
              const extraUnlocked = Math.floor(elapsed / this.hintIntervalMs);
              this.unlockedHints = Math.min(this.hints.length, 1 + extraUnlocked);

              if (this.unlockedHints >= this.hints.length) {
                  this.countdownText = '¡Todas las pistas disponibles!';
                  return;
              }

              const nextUnlockAt = this.hintsStartTime + (extraUnlocked + 1) * this.hintIntervalMs;
              const remainingMs = Math.max(0, nextUnlockAt - this.now);
              this.countdownText = `Siguiente pista en ${this.formatDuration(remainingMs)}`;
          },

          formatDuration(ms) {
              const totalSeconds = Math.floor(ms / 1000);
              const days = Math.floor(totalSeconds / 86400);
              const hours = String(Math.floor((totalSeconds % 86400) / 3600)).padStart(2, '0');
              const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
              const seconds = String(totalSeconds % 60).padStart(2, '0');

              return days > 0
                  ? `${days}d ${hours}h ${minutes}m ${seconds}s`
                  : `${hours}h ${minutes}m ${seconds}s`;
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
        <h1 class="text-xl md:text-2xl font-extrabold text-purple-300">🔐 La Caja Fuerte</h1>
        <a href="<?php echo e(route('dashboard')); ?>" class="text-xs bg-purple-900/60 hover:bg-purple-800 text-purple-200 font-semibold px-4 py-2 rounded-full transition border border-purple-700/50">
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
            <div class="flex justify-center gap-3 mb-6 mt-4">
                <template x-for="i in 4">
                    <div class="w-12 h-14 bg-purple-950/80 border-2 rounded-2xl flex items-center justify-center text-2xl font-bold font-mono transition-colors"
                         :class="enteredCode.length >= i ? 'border-purple-400 text-white shadow-[0_0_15px_rgba(168,85,247,0.4)]' : 'border-purple-900/60 text-purple-700'"
                         x-text="enteredCode.length >= i ? '•' : '·'">
                    </div>
                </template>
            </div>

            <!-- Mensaje de error visual -->
            <div class="h-6 mb-2">
                <p x-show="isWrong" x-cloak class="text-red-400 text-xs font-bold animate-pulse">❌ Código incorrecto. ¡Prueba otra vez!</p>
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

            <!-- 💡 Botón para abrir el Pop-up de Pistas -->
            <div class="mt-6 pt-4 border-t border-purple-800/40">
                <button @click="showHints = true"
                        class="text-xs text-purple-300 hover:text-amber-300 font-semibold flex items-center justify-center gap-1.5 mx-auto transition group">
                    <span class="text-base group-hover:scale-125 transition-transform">💡</span>
                    <span class="underline decoration-purple-500/50 underline-offset-4">
                        Pistas (<span x-text="unlockedHints"></span>/<span x-text="hints.length"></span>)
                    </span>
                </button>
            </div>

        </div>

        <!-- 🔓 ESTADO 2: LA CAJA ABIERTA (Con premio físico y botón de canje) -->
        <div x-show="isUnlocked"
             x-cloak
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
                    Premio Especial ✨
                </div>

                <h3 class="text-lg font-bold text-white mt-2 mb-3">¡10 Sobres de Vaiana para ti!</h3>

                <!-- 🖼️ Imagen de Vaiana -->
                <div class="w-full flex justify-center mb-4">
                    <img src="<?php echo e(asset('images/safe/vaiana.jpeg')); ?>"
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

    </main>

    <!-- 🪟 POP-UP / MODAL DE PISTAS -->
    <div x-show="showHints"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">

        <!-- Caja del Modal -->
        <div @click.outside="showHints = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4"
             class="bg-gradient-to-b from-purple-950 to-gray-950 border-2 border-purple-500/50 rounded-3xl max-w-md w-full p-6 shadow-[0_0_40px_rgba(168,85,247,0.4)] relative">

            <!-- Botón Cerrar (X) -->
            <button @click="showHints = false"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-purple-900/60 hover:bg-purple-800 text-purple-300 flex items-center justify-center font-bold transition">
                ✕
            </button>

            <!-- Título del Pop-up -->
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-xl">
                    💡
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-white">Pistas Secretas</h3>
                    <p class="text-xs text-purple-300">Se desbloquea una nueva pista cada 24 horas</p>
                </div>
            </div>

            <!-- Estado del temporizador -->
            <div class="bg-amber-500/10 border border-amber-400/30 rounded-2xl px-4 py-2.5 text-center mb-4">
                <p class="text-xs font-semibold text-amber-300" x-text="countdownText"></p>
            </div>

            <!-- Lista de pistas -->
            <div class="space-y-3 my-4 max-h-64 overflow-y-auto pr-1">
                <template x-for="(hint, index) in hints" :key="index">
                    <div class="rounded-2xl p-3.5 text-sm leading-relaxed border"
                         :class="index < unlockedHints
                             ? 'bg-purple-900/40 border-purple-700/50 text-purple-100 shadow-inner'
                             : 'bg-black/40 border-purple-900/40 text-purple-400/70'">
                        <template x-if="index < unlockedHints">
                            <p x-text="hint"></p>
                        </template>
                        <template x-if="index >= unlockedHints">
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <span>🔒 Pista #<span x-text="index + 1"></span> bloqueada</span>
                                <span class="font-mono text-amber-300/90" x-text="index === unlockedHints ? countdownText.replace('Siguiente pista en ', '').replace('Las pistas empiezan en ', '') : '24h+'"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Botón cerrar -->
            <div class="mt-4">
                <button @click="showHints = false"
                        class="w-full bg-purple-900/60 hover:bg-purple-800 text-purple-200 font-semibold py-2.5 px-4 rounded-xl text-xs border border-purple-700/50 transition">
                    Volver a intentar el código
                </button>
            </div>

        </div>
    </div>

</body>
</html>
<?php /**PATH C:\Proyectos\aniversario2\resources\views/safe.blade.php ENDPATH**/ ?>