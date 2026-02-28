<?php
// 1. Lógica de rutas blindada para evitar el error "Class not found"
require_once dirname(__DIR__) . "/app/presenter/LoginPresenter.php";

$presenter = new LoginPresenter();
$mensaje = $presenter->iniciarSesion(); // Captura error de login (si existe)
$esError = false; 

// 2. LÓGICA DE MENSAJES DUALES
if ($mensaje) {
    // Si el presenter devuelve texto, es un error de credenciales
    $esError = true;
} elseif (isset($_GET['mensaje']) && $_GET['mensaje'] == 'sesion_cerrada') {
    // Si viene de logout.php, es un mensaje de éxito
    $mensaje = "Has salido del sistema correctamente.";
    $esError = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema | Ruta Larga</title>
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
<body class="bg-gray-50 flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-10 py-5 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.9)] shadow-lg">
        <h2 class="text-white text-2xl font-bold tracking-wider">RUTA LARGA</h2>
        <nav class="hidden md:flex gap-8 text-sm uppercase tracking-widest">
            <a href="soporte.php" class="text-white hover:text-gray-300 transition-colors">Soporte</a>
        </nav>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pt-32 pb-12">
        <div class="bg-white w-full max-w-md p-10 rounded-2xl shadow-2xl border border-gray-100">
            
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 border-b-4 border-gray-600 inline-block pb-2 italic">Bienvenido</h2>
                <p class="text-gray-500 mt-4 text-sm uppercase tracking-tighter">Gestión de Flota y Logística</p>
            </div>

            <?php if($mensaje): ?>
                <div class="mb-6 p-4 border-l-4 shadow-sm rounded flex items-center animate-pulse
                    <?php echo $esError ? 'bg-red-50 border-red-500 text-red-700' : 'bg-green-50 border-green-500 text-green-700'; ?>">
                    
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <?php if($esError): ?>
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        <?php else: ?>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        <?php endif; ?>
                    </svg>

                    <span class="font-medium text-sm"><?php echo $mensaje; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest">Correo Electrónico</label>
                    <input type="email" name="correo" required 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all placeholder-gray-300"
                        placeholder="usuario@rutalarga.com">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest">Contraseña</label>
                    <input type="password" name="clave" required 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all placeholder-gray-300"
                        placeholder="••••••••">
                    
                    <div class="text-right mt-2">
                        <a href="recuperar_clave.php" class="text-[10px] text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest italic">
                        <a href="recuperarcontrasena.php" class="text-[10px] text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest italic">
                            ¿Olvidó su contraseña?
                        </a>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" 
                        class="w-full bg-[#666666] hover:bg-[#444444] text-white font-bold py-3.5 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm">
                        Iniciar Sesión
                    </button>

                    <a href="nuevoregistro1.php" 
                        class="w-full border-2 border-[#666666] text-[#666666] hover:bg-[#666666] hover:text-white font-bold py-3 rounded-lg text-center transition-all duration-300 uppercase tracking-widest text-sm">
                        Registrarme
                    </a>
                </div>
            </form>

            <div class="mt-8 text-center border-t border-gray-100 pt-6">
                <a href="soporte.php" class="text-xs text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest italic">
                    ¿Problemas con su cuenta? Soporte técnico
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-500 py-6 text-center text-[10px] tracking-[0.2em] uppercase">
        &copy; 2026 RUTA LARGA FURGONES UNIDOS | Logística Segura
    </footer>

</body>
</html>