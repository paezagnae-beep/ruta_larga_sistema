<?php
session_start();
require_once dirname(__DIR__) . "/config/session.php";
$sesion = new SessionManager();
$sesion->validarSesion();

require_once dirname(__DIR__) . "/controller/editar_perfilController.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$userObj = new Usuario("localhost", "root", "", "proyecto");
$mensaje = "";
$esError = false; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // VALIDACIÓN DE SERVIDOR: Verificar que la nueva clave no sea igual a la anterior
    if (!empty($_POST['old_password']) && !empty($_POST['new_password']) && $_POST['old_password'] === $_POST['new_password']) {
        $mensaje = "La nueva clave no puede ser idéntica a la clave actual. Por seguridad, elija una diferente.";
        $esError = true;
    } else {
        if ($userObj->actualizarPerfil($_SESSION["usuario"], $_POST)) {
            header("Location: menuView.php?update=success");
            exit();
        } else {
            $mensaje = $userObj->getLastError();
            $esError = true;
        }
    }
}

$usuarioActual = $userObj->obtenerDatos($_SESSION["usuario"]);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
            backdrop-filter: blur(10px);
        }

        .valid { color: #10b981; font-weight: bold; }
        .invalid { color: #9ca3af; }
        .error-match { color: #ef4444; font-weight: bold; font-style: italic; }
    </style>
</head>

<body class="flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-6 md:px-10 py-4 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.95)] shadow-xl border-b border-gray-700">
        <h2 class="text-white text-xl md:text-2xl font-bold tracking-wider uppercase">Ruta Larga</h2>
        <nav class="flex items-center gap-4">
            <a href="menuView.php" class="text-gray-300 hover:text-white text-xs font-bold uppercase tracking-widest transition-all flex items-center gap-2">
                <i class="ph ph-arrow-left"></i> Volver al Menú
            </a>
        </nav>
    </header>

    <main class="flex-grow pt-28 pb-12 px-6 flex items-center justify-center">
        <div class="max-w-2xl w-full glass-card p-8 md:p-10 rounded-3xl shadow-2xl border border-gray-100">

            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-800 rounded-full mb-4 text-orange-600 shadow-lg">
                    <i class="ph ph-user-focus text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 italic">Datos Personales</h1>
            </div>

            <?php if ($mensaje): ?>
                <div class="mb-6 p-3 rounded text-sm text-center <?= $esError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="perfilForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Nombre</label>
                        <input type="text" name="nombre" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all" value="<?= htmlspecialchars($usuarioActual['Nombre'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Apellido</label>
                        <input type="text" name="apellido" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all" value="<?= htmlspecialchars($usuarioActual['Apellido'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Correo Electrónico (No editable)</label>
                    <input type="email" name="email" readonly class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-100 text-gray-500 cursor-not-allowed outline-none" value="<?= htmlspecialchars($usuarioActual['Email']) ?>">
                </div>

                <hr class="border-gray-100 my-4">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Clave Actual</label>
                        <input type="password" name="old_password" id="old_password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Nueva Clave</label>
                        <input type="password" name="new_password" id="new_password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <p class="text-[9px] font-bold text-gray-400 mb-2 uppercase tracking-widest">Requisitos de nueva clave:</p>
                    <ul class="grid grid-cols-2 gap-x-2 gap-y-1 text-[10px] italic">
                        <li id="req-length" class="invalid flex items-center"><span class="bullet mr-1">○</span> 8+ caracteres</li>
                        <li id="req-upper" class="invalid flex items-center"><span class="bullet mr-1">○</span> Una Mayúscula</li>
                        <li id="req-lower" class="invalid flex items-center"><span class="bullet mr-1">○</span> Una Minúscula</li>
                        <li id="req-symbol" class="invalid flex items-center"><span class="bullet mr-1">○</span> Un Símbolo</li>
                        <li id="req-diff" class="valid flex items-center"><span class="bullet mr-1">●</span> Diferente a la actual</li>
                    </ul>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-gray-800 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-[0.2em] text-xs">
                        Actualizar Perfil
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-400 py-6 text-center text-[9px] tracking-[0.3em] uppercase border-t border-gray-800">
        &copy; 2026 RUTA LARGA | GESTIÓN DE CUENTA
    </footer>

    <script>
        const oldPass = document.getElementById('old_password');
        const newPass = document.getElementById('new_password');
        const reqDiff = document.getElementById('req-diff');
        
        const requirements = {
            length: { el: document.getElementById('req-length'), regex: /.{8,}/ },
            upper: { el: document.getElementById('req-upper'), regex: /[A-Z]/ },
            lower: { el: document.getElementById('req-lower'), regex: /[a-z]/ },
            symbol: { el: document.getElementById('req-symbol'), regex: /[!@#$%^&*(),.?":{}|<>]/ }
        };

        function checkForm() {
            const vOld = oldPass.value;
            const vNew = newPass.value;

            // 1. Validar complejidad
            if (vNew === "") {
                Object.values(requirements).forEach(r => {
                    r.el.className = 'invalid flex items-center';
                    r.el.querySelector('.bullet').textContent = '○';
                });
            } else {
                Object.keys(requirements).forEach(key => {
                    const req = requirements[key];
                    const isValid = req.regex.test(vNew);
                    req.el.className = isValid ? 'valid flex items-center' : 'invalid flex items-center';
                    req.el.querySelector('.bullet').textContent = isValid ? '●' : '○';
                });
            }

            // 2. Validar que no sean iguales
            if (vNew !== "" && vOld !== "" && vNew === vOld) {
                reqDiff.className = 'error-match flex items-center';
                reqDiff.innerHTML = '<span class="bullet mr-1">●</span> ¡No use la misma clave!';
            } else {
                reqDiff.className = 'valid flex items-center';
                reqDiff.innerHTML = '<span class="bullet mr-1">●</span> Diferente a la actual';
            }
        }

        newPass.addEventListener('input', checkForm);
        oldPass.addEventListener('input', checkForm);
    </script>
</body>
</html>