<?php
session_start();
require_once "../presenter/verificarPresenter.php";

$presenter = new VerificarPresenter();
$data = $presenter->manejarPeticion();

$mensaje_status = $data['mensaje'];
$esError = $data['error'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Código | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Georgia, serif;
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('../assets/img/fondo.jpg');
            background-size: cover; background-attachment: fixed;
        }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(5px); }
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-10 py-5 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.9)] shadow-lg">
        <h2 class="text-white text-2xl font-bold tracking-wider">RUTA LARGA</h2>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pt-32 pb-12">
        <div class="glass-card w-full max-w-md p-10 rounded-2xl shadow-2xl border border-gray-100">

            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 border-b-4 border-gray-600 inline-block pb-2 italic">Verificación</h2>
                <p class="text-gray-500 mt-4 text-sm uppercase tracking-tighter">Ingrese el código de 6 dígitos enviado a su correo</p>
            </div>

            <?php if ($mensaje_status): ?>
                <div class="mb-6 p-4 border-l-4 bg-red-50 border-red-500 text-red-700 flex items-center animate-pulse">
                    <span class="font-medium text-sm"><?php echo $mensaje_status; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-widest text-center">Código de Seguridad</label>
                    <input type="number" name="codigo" required 
                        class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none text-center text-3xl font-bold tracking-[10px]"
                        placeholder="000000">
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <button type="submit" name="validar"
                        class="w-full bg-[#666666] hover:bg-[#444444] text-white font-bold py-3.5 rounded-lg shadow-lg transition-all uppercase tracking-widest text-sm">
                        Validar Código
                    </button>

                    <a href="recuperarcontrasena.php"
                        class="w-full border-2 border-[#666666] text-[#666666] hover:bg-[#666666] hover:text-white font-bold py-3 rounded-lg text-center transition-all uppercase tracking-widest text-sm">
                        Reenviar Correo
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-500 py-6 text-center text-[10px] tracking-[0.2em] uppercase">
        &copy; 2026 RUTA LARGA | Logística Segura
    </footer>

</body>
</html>