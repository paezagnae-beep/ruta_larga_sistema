<?php
session_start();
require_once "../presenter/recuperacionPresenter.php";

$presenter = new RecuperacionPresenter();
$data = $presenter->manejarPeticion();

$mensaje_status = $data['mensaje'];
$esError = $data['error'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    </head>
<body class="flex flex-col min-h-screen">
    <main class="flex-grow flex items-center justify-center">
        <div class="glass-card w-full max-w-md p-10 rounded-2xl shadow-2xl">
            
            <?php if ($mensaje_status): ?>
                <div class="mb-6 p-4 border-l-4 <?php echo $esError ? 'bg-red-50 border-red-500 text-red-700' : 'bg-green-50 border-green-500 text-green-700'; ?>">
                    <span class="font-medium text-sm"><?php echo $mensaje_status; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase">Correo Electrónico</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 border rounded-lg" placeholder="usuario@rutalarga.com">
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" name="enviar_codigo" class="w-full bg-[#666666] text-white py-3.5 rounded-lg uppercase text-sm font-bold">
                        Enviar Código
                    </button>
                    <a href="login.php" class="text-center text-sm text-gray-500 hover:underline">Volver al Login</a>
                </div>
            </form>
        </div>
    </main>

    </body>
</html>