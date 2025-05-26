<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  <title>Iniciar sesión</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { @apply bg-gradient-to-br from-sky-50 to-sky-100 dark:from-gray-800 dark:to-gray-900; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 dark:text-white">
  <!-- Wrapper -->
  <div class="w-full max-w-md md:max-w-lg bg-white/60 dark:bg-gray-800/60 backdrop-blur rounded-2xl shadow-xl p-8 space-y-6">
    <!-- Branding -->
    <div class="text-center space-y-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 6-4 6-4 6m8-6c0 6-4 6-4 6m0 0v5m0-5c3.333 0 5-3 5-6.5S15.333 3 12 3 7 6 7 9.5 8.667 15 12 15z" />
      </svg>
      <h1 class="text-2xl font-semibold">Sistema Dermatológico</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">Accede con tu correo institucional</p>
    </div>

    <!-- Login form -->
    <form action="/login" method="post" class="space-y-5">
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium mb-1">Correo electrónico</label>
        <input type="email" name="email" id="email" placeholder="usuario@clinica.com" required autofocus
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-700 px-4 py-3 transition focus:outline-none focus:ring-2 focus:ring-sky-500" />
      </div>
      <!-- Password + toggle -->
      <div class="relative">
        <label for="password" class="block text-sm font-medium mb-1">Contraseña</label>
        <input type="password" name="password" id="password" placeholder="••••••••" required minlength="6"
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-700 px-4 py-3 pr-12 transition focus:outline-none focus:ring-2 focus:ring-sky-500" />
        <button type="button" id="togglePwd" class="absolute right-3 top-11 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
          <svg xmlns="http://www.w3.org/2000/svg" id="eyeIcon" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10 3C4 3 1 10 1 10s3 7 9 7 9-7 9-7-3-7-9-7zm0 11a4 4 0 110-8 4 4 0 010 8z" />
          </svg>
        </button>
      </div>
      <!-- Submit -->
      <button type="submit" class="w-full py-3 rounded-lg bg-sky-600 text-white font-medium hover:bg-sky-700 active:bg-sky-800 transition">Entrar</button>
    </form>

    <!-- Footer links -->
    <div class="text-center text-sm text-gray-500 dark:text-gray-400">
      ¿Olvidaste la contraseña? <a href="#" class="text-sky-600 hover:underline">Recupérala aquí</a>
    </div>
  </div>

  <!-- Toggle password script -->
  <script>
    const pwd  = document.getElementById('password');
    const btn  = document.getElementById('togglePwd');
    const icon = document.getElementById('eyeIcon');

    btn.addEventListener('click', () => {
      const isHidden = pwd.type === 'password';
      pwd.type = isHidden ? 'text' : 'password';
      icon.innerHTML = isHidden
        ? '<path d="M13.875 18.825A10.05 10.05 0 0110 19c-5 0-9-7-9-9 1.252-1.868 2.976-3.487 5-4.555m3-1.3A9.968 9.968 0 0110 3c5 0 9 7 9 9 0 1.226-.354 2.599-1 3.975" /><path d="M3 3l14 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
        : '<path d="M10 3C4 3 1 10 1 10s3 7 9 7 9-7 9-7-3-7-9-7zm0 11a4 4 0 110-8 4 4 0 010 8z" />';
    });
  </script>
</body>
</html>