<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Recuperar Contraseña</title>
  <script src="https://cdn.tailwindcss.com"></script>
    <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; }
    .navbar-custom { background-color: #08082c; }
    .modal-header { background-color: #08082c; color: white; }
    .badge-rif { background: #e8f5e9; color: #2e7d32; font-weight: bold; border: 1px solid #c8e6c9; }
</style>
    <style>
    body { 
        font-family: Georgia, 'Times New Roman', Times, serif; 
        /* Configuración de la imagen de fondo */
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/fondo.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
    /* Glassmorphism para las tarjetas si prefieres un estilo más moderno */
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
    }
</style>
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