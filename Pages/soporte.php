<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contacto | Ruta Larga Furgones Unidos</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { 
        font-family: Georgia, 'Times New Roman', Times, serif; 
        /* Configuración de la imagen de fondo original */
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/fondo.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
    /* Estilo Glassmorphism original */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(5px);
    }
  </style>
</head>

<body class="flex flex-col min-h-screen">

  <header class="fixed top-0 w-full px-10 py-5 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.95)] shadow-xl">
    <h2 class="text-white text-2xl font-bold tracking-wider uppercase">Ruta Larga</h2>
    <nav class="flex items-center gap-6 text-sm uppercase tracking-widest">
      <a href="menu.php" class="text-white hover:text-gray-300 transition-colors flex items-center gap-2">
        <ion-icon name="home-outline" class="text-lg"></ion-icon> Inicio
      </a>
    </nav>
  </header>

  <section class="pt-40 pb-20 text-center">
    <h1 class="text-5xl font-bold text-white tracking-tighter italic drop-shadow-lg">CONTÁCTANOS</h1>
    <div class="h-1.5 w-24 bg-orange-600 mx-auto mt-4 rounded-full"></div>
    <p class="text-gray-200 mt-4 uppercase tracking-[0.3em] text-xs font-semibold">Estamos aquí para escucharte</p>
  </section>

  <main class="flex-grow flex items-center justify-center px-4 py-10">
    <div class="glass-card w-full max-w-2xl p-10 rounded-2xl shadow-2xl">
      
      <div class="mb-10">
        <h2 class="text-2xl font-bold text-gray-800 italic">Envíanos un Mensaje</h2>
        <p class="text-gray-500 text-sm mt-2">Completa el formulario y nuestro equipo te responderá a la brevedad.</p>
      </div>

      <form class="space-y-6" action="https://formspree.io/f/mlgwvpqv" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest" for="nombre">Nombre Completo</label>
            <input type="text" id="nombre" name="nombre" required 
              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:bg-white outline-none transition-all placeholder-gray-300"
              placeholder="Ej: Luis Galindez">
          </div>

          <div class="space-y-2">
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest" for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required 
              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:bg-white outline-none transition-all placeholder-gray-300"
              placeholder="Ej: tu.correo@empresa.com">
          </div>
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest" for="asunto">Asunto</label>
          <input type="text" id="asunto" name="asunto" required 
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:bg-white outline-none transition-all placeholder-gray-300"
            placeholder="Ej: Consulta sobre servicios">
        </div>

        <div class="space-y-2">
          <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest" for="mensaje">Mensaje</label>
          <textarea id="mensaje" name="mensaje" rows="5" required 
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:bg-white outline-none transition-all placeholder-gray-300 resize-none"
            placeholder="Escribe tu consulta aquí..."></textarea>
        </div>

        <button type="submit" 
          class="w-full bg-[#08082c] hover:bg-orange-600 text-white font-bold py-4 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-[0.2em] text-sm">
          Enviar Mensaje ahora
        </button>
      </form>
    </div>
  </main>

  <footer class="bg-[rgb(8,8,44)] text-gray-500 py-10 px-10 text-center mt-auto">
    <p class="text-[10px] tracking-[0.2em] uppercase mb-4">&copy; 2026 Ruta Larga Furgones Unidos Carrizal. Todos los derechos reservados.</p>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>