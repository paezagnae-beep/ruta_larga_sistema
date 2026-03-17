<?php
require_once dirname(__DIR__) . '/config/session.php';

$sesion = new SessionManager();
$sesion->validarSesion();

require_once dirname(__DIR__) . "/controller/cambiarClaveController.php";

$controller = new CambiarClaveController();
$data = $controller->manejarPeticion();

$mensaje_status = $data['mensaje'];
$esError = $data['error'];
$exito = $data['exito'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *{
            font-family: Georgia, 'Times New Roman', Times, serif;
        }
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
            url('../../assets/img/fondo.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
        }

        /* Estilo para el logo en el header */
        .logo-header {
            height: 50px;
            width: auto;
            object-fit: contain;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-10 py-4 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.95)] shadow-lg border-b border-gray-700">
        <div class="flex items-center">
            <img src="../../assets/img/carrizal.png" alt="Logo Ruta Larga" class="logo-header">
        </div>
        <h2 class="text-white text-sm md:text-xl font-bold tracking-wider uppercase hidden md:block">Logística Segura</h2>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pt-32 pb-12">
        <div class="glass-card w-full max-w-md p-10 rounded-2xl shadow-2xl border border-gray-100">

            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 border-b-4 border-gray-600 inline-block pb-2 italic">Nueva Clave</h2>
                <p class="text-gray-500 mt-4 text-sm uppercase tracking-tighter">Cree una contraseña segura</p>
            </div>

            <?php if ($mensaje_status): ?>
                <div class="mb-6 p-4 border-l-4 <?php echo $exito ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700 animate-pulse'; ?> flex items-center">
                    <span class="font-medium text-sm"><?php echo $mensaje_status; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!$exito): ?>
                <form action="" method="post" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest">Nueva Contraseña</label>
                        <input type="password" name="pass1" required
                            class="w-full px-4 py-3 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-gray-400 outline-none"
                            placeholder="••••••••">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest">Confirmar Contraseña</label>
                        <input type="password" name="pass2" required
                            class="w-full px-4 py-3 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-gray-400 outline-none"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="cambiar"
                            class="w-full bg-[#444444] hover:bg-black text-white font-bold py-3.5 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all uppercase tracking-widest text-sm">
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center pt-4">
                    <a href="loginView.php"
                        class="block w-full bg-[#08082c] hover:bg-black text-white font-bold py-4 rounded-lg shadow-lg transition-all uppercase tracking-widest text-sm">
                        Ir al Inicio de Sesión
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-500 py-6 text-center text-[10px] tracking-[0.2em] uppercase mt-auto">
        &copy; 2026 RUTA LARGA | Logística Segura
    </footer>
</body>

</html>