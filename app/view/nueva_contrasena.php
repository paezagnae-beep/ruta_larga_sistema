<?php
session_start();
if (!isset($_SESSION['id_usuario_recu'])) {
    header("Location: /app/view/recuperarcontrasena.php");
    exit();
}

require_once "../controller/contrasenaController.php";
$controller = new ContrasenaController();
$resultado = $controller->procesarCambio();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nueva Contraseña | Ruta Larga</title>
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
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
        }

        .valid { color: #10b981; font-weight: bold; }
        .invalid { color: #9ca3af; }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full glass-card p-10 rounded-2xl shadow-2xl border border-gray-100">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 border-b-4 border-gray-600 inline-block pb-2 italic">Restablecer Clave</h1>
        </div>

        <?php if ($resultado['mensaje']): ?>
            <div class="p-3 mb-4 rounded text-sm text-center bg-red-50 text-red-700 border border-red-200">
                <?= $resultado['mensaje'] ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" id="passwordForm" class="space-y-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Nueva Contraseña</label>
                <div class="relative">
                    <input type="password" name="nueva_pass" id="password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-gray-400 outline-none transition-all"
                        placeholder="••••••••" />
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Confirmar Contraseña</label>
                <div class="relative">
                    <input type="password" name="confirmar_pass" id="confirm_password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-gray-400 outline-none transition-all"
                        placeholder="Repita su contraseña" />
                </div>
                <p id="match-msg" class="text-[9px] mt-1 hidden"></p>
            </div>

            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg shadow-inner">
                <p class="text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-widest">Requisitos de seguridad:</p>
                <ul class="grid grid-cols-2 gap-x-2 gap-y-1 text-[10px] italic">
                    <li id="req-length" class="invalid flex items-center transition-colors"><span class="bullet mr-1">○</span> 8+ caracteres</li>
                    <li id="req-upper" class="invalid flex items-center transition-colors"><span class="bullet mr-1">○</span> Una Mayúscula</li>
                    <li id="req-lower" class="invalid flex items-center transition-colors"><span class="bullet mr-1">○</span> Una Minúscula</li>
                    <li id="req-symbol" class="invalid flex items-center transition-colors"><span class="bullet mr-1">○</span> Un Símbolo</li>
                </ul>
            </div>

            <button type="submit" name="guardar_clave" id="btn-submit"
                class="w-full bg-gray-700 hover:bg-gray-900 text-white font-bold py-3.5 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm">
                Actualizar Contraseña
            </button>
        </form>

        <div class="text-center mt-6 border-t border-gray-100 pt-6">
            <a href="loginView.php" class="text-xs text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest italic">
                Cancelar y regresar
            </a>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const matchMsg = document.getElementById('match-msg');
        
        const requirements = {
            length: { el: document.getElementById('req-length'), regex: /.{8,}/ },
            upper: { el: document.getElementById('req-upper'), regex: /[A-Z]/ },
            lower: { el: document.getElementById('req-lower'), regex: /[a-z]/ },
            symbol: { el: document.getElementById('req-symbol'), regex: /[!@#$%^&*(),.?":{}|<>]/ }
        };

        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            Object.keys(requirements).forEach(key => {
                const req = requirements[key];
                const isValid = req.regex.test(val);
                req.el.classList.toggle('valid', isValid);
                req.el.classList.toggle('invalid', !isValid);
                req.el.querySelector('.bullet').textContent = isValid ? '●' : '○';
            });
            checkMatch();
        });

        confirmInput.addEventListener('input', checkMatch);

        function checkMatch() {
            if (confirmInput.value === "") { matchMsg.classList.add('hidden'); return; }
            matchMsg.classList.remove('hidden');
            const isMatch = passwordInput.value === confirmInput.value;
            matchMsg.textContent = isMatch ? "✓ Las contraseñas coinciden" : "✗ Las contraseñas no coinciden";
            matchMsg.className = isMatch ? "text-[9px] mt-1 text-green-600 font-bold" : "text-[9px] mt-1 text-red-500 italic";
        }
    </script>
</body>
</html>