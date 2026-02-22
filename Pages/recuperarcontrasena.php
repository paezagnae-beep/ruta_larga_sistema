
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Recuperar Contraseña</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex items-center justify-center">

  <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold mb-4 text-center text-gray-800">Recuperar Contraseña</h1>
    <p class="text-gray-700 text-sm text-center mb-6">
      Ingresa tu correo electrónico para recibir un enlace de recuperación.
    </p>

    <form class="space-y-4">
      <input type="email" placeholder="Correo electrónico" required
        class="w-full px-4 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"/>
      
      <button type="submit"
        class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 rounded font-semibold transition-colors duration-200">
        Enviar enlace de recuperación
      </button>
    </form>

    <div class="text-center mt-6">
      <a href="login.php" class="text-gray-600 hover:underline">Volver al inicio de sesión</a>
    </div>
  </div>

</body>
</html>