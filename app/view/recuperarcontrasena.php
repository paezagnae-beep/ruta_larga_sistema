<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recuperar Contraseña | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        *{
            font-family: Georgia, 'Times New Roman', Times, serif;
        }
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../../assets/img/fondo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full glass-card p-8 rounded-2xl shadow-2xl">
        <div class="flex justify-center mb-6">
            <div class="bg-[#08082c] p-4 rounded-full shadow-lg">
                <i class="ph ph-lock-key-open text-white text-4xl"></i>
            </div>
        </div>

        <h1 class="text-2xl font-bold mb-2 text-center text-gray-800">¿Problemas de acceso?</h1>
        <p class="text-gray-600 text-sm text-center mb-8">
            Ingresa tu correo electrónico y te enviaremos un código de verificación para restablecer tu contraseña.
        </p>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'enviado'): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 mb-6 text-sm rounded-r flex items-start shadow-sm">
                    <i class="ph ph-check-circle text-lg mr-2 text-green-600"></i>
                    <div>
                        <span class="font-bold">¡Código enviado!</span>
                        <p>Revisa tu correo. El código es válido por tiempo limitado.</p>
                    </div>
                </div>
            <?php elseif ($_GET['status'] == 'error_correo'): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-800 p-4 mb-6 text-sm rounded-r flex items-start shadow-sm">
                    <i class="ph ph-warning-circle text-lg mr-2 text-red-600"></i>
                    <div>
                        <span class="font-bold">Error de envío</span>
                        <p>No se pudo conectar con el servidor de correos. Intenta más tarde.</p>
                    </div>
                </div>
            <?php elseif ($_GET['status'] == 'no_existe'): ?>
                <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-800 p-4 mb-6 text-sm rounded-r flex items-start shadow-sm">
                    <i class="ph ph-user-minus text-lg mr-2 text-amber-600"></i>
                    <div>
                        <span class="font-bold">No encontrado</span>
                        <p>Este correo no está registrado en el sistema de Ruta Larga.</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="../controller/recuperacionController.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest ml-1">Correo Electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="ph ph-envelope-simple text-xl"></i>
                    </span>
                    <input 
                        type="email" 
                        name="email_recuperar" 
                        placeholder="ejemplo@rutalarga.com" 
                        required
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#08082c] focus:border-transparent outline-none transition-all duration-200 bg-gray-50" 
                    />
                </div>
            </div>

            <button 
                type="submit" 
                name="enviar_peticion"
                class="w-full bg-[#08082c] hover:bg-[#1a1a4d] text-white py-3.5 rounded-xl font-bold shadow-xl flex items-center justify-center gap-2 transform transition active:scale-95">
                <i class="ph ph-paper-plane-tilt text-lg"></i>
                Enviar Instrucciones
            </button>
        </form>

        <div class="text-center mt-10 pt-6 border-t border-gray-100">
            <a href="loginView.php" class="text-sm font-bold text-[#08082c] hover:text-blue-800 flex items-center justify-center gap-2 transition-colors">
                <i class="ph ph-caret-left"></i> 
                Volver al inicio de sesión
            </a>
        </div>
    </div>

</body>
</html>