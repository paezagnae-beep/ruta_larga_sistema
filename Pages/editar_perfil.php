<?php
session_start();
// SEGURIDAD: Si no hay sesión iniciada, lo devolvemos al login
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

// Configuración de conexión
$mysqli = new mysqli("localhost", "root", "", "proyecto");
$nombreUsuario = $_SESSION["usuario"];
$mensaje = "";
$esError = false;

// 1. Obtener datos actuales del usuario (Incluyendo Nombre y Apellido)
$stmt = $mysqli->prepare("SELECT Email, Nombre, Apellido FROM usuarios WHERE Email = ?");
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuarioActual = $resultado->fetch_assoc();

// 2. Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoNombre = $_POST['nombre'] ?? '';
    $nuevoApellido = $_POST['apellido'] ?? '';
    $nuevoEmail = $_POST['email'] ?? '';
    $nuevaClave = $_POST['password'] ?? '';
    $confirmarClave = $_POST['confirm_password'] ?? '';

    $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?\":{}|<>]).{8,}$/";

    if ($nuevaClave !== $confirmarClave) {
        $mensaje = "Las contraseñas no coinciden.";
        $esError = true;
    } elseif (!empty($nuevaClave) && !preg_match($regex, $nuevaClave)) {
        $mensaje = "La nueva contraseña no cumple los requisitos de seguridad.";
        $esError = true;
    } else {
        // Lógica de actualización con Nombre y Apellido
        if (!empty($nuevaClave)) {
            $hash = password_hash($nuevaClave, PASSWORD_DEFAULT);
            $update = $mysqli->prepare("UPDATE usuarios SET Nombre = ?, Apellido = ?, Email = ?, Contraseña = ? WHERE Email = ?");
            $update->bind_param("sssss", $nuevoNombre, $nuevoApellido, $nuevoEmail, $hash, $nombreUsuario);
        } else {
            $update = $mysqli->prepare("UPDATE usuarios SET Nombre = ?, Apellido = ?, Email = ? WHERE Email = ?");
            $update->bind_param("ssss", $nuevoNombre, $nuevoApellido, $nuevoEmail, $nombreUsuario);
        }

        if ($update->execute()) {
            $_SESSION["usuario"] = $nuevoEmail; // Actualizar sesión por si cambió el correo
            header("Location: menu.php?update=success");
            exit();
        } else {
            $mensaje = "Error al actualizar los datos.";
            $esError = true;
        }
    }
}
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
        body { 
            font-family: Georgia, 'Times New Roman', Times, serif; 
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/fondo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .valid { color: #10b981; font-weight: bold; }
        .invalid { color: #9ca3af; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-6 md:px-10 py-4 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.95)] shadow-xl border-b border-gray-700">
        <h2 class="text-white text-xl md:text-2xl font-bold tracking-wider uppercase">Ruta Larga</h2>
        <nav class="flex items-center gap-4">
            <a href="menu.php" class="text-gray-300 hover:text-white text-xs font-bold uppercase tracking-widest transition-all flex items-center gap-2">
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

            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Nombre</label>
                        <input type="text" name="nombre" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all"
                            value="<?= htmlspecialchars($usuarioActual['Nombre'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Apellido</label>
                        <input type="text" name="apellido" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all"
                            value="<?= htmlspecialchars($usuarioActual['Apellido'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Correo Electrónico</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all"
                        value="<?= htmlspecialchars($usuarioActual['Email']) ?>">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Nueva Contraseña</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 mb-1 uppercase tracking-widest">Confirmar Clave</label>
                        <input type="password" name="confirm_password" id="confirm_password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <ul class="grid grid-cols-2 gap-x-2 gap-y-1 text-[10px] italic">
                        <li id="req-length" class="invalid flex items-center"><span class="bullet mr-1">○</span> 8+ caracteres</li>
                        <li id="req-upper" class="invalid flex items-center"><span class="bullet mr-1">○</span> Una Mayúscula</li>
                        <li id="req-lower" class="invalid flex items-center"><span class="bullet mr-1">○</span> Una Minúscula</li>
                        <li id="req-symbol" class="invalid flex items-center"><span class="bullet mr-1">○</span> Un Símbolo</li>
                    </ul>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-gray-800 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition-all duration-300 uppercase tracking-[0.2em] text-xs">
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
        const passwordInput = document.getElementById('password');
        const requirements = {
            length: { el: document.getElementById('req-length'), regex: /.{8,}/ },
            upper: { el: document.getElementById('req-upper'), regex: /[A-Z]/ },
            lower: { el: document.getElementById('req-lower'), regex: /[a-z]/ },
            symbol: { el: document.getElementById('req-symbol'), regex: /[!@#$%^&*(),.?":{}|<>]/ }
        };

        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            if(val === "") {
                Object.values(requirements).forEach(r => {
                    r.el.className = 'invalid flex items-center';
                    r.el.querySelector('.bullet').textContent = '○';
                });
                return;
            }
            Object.keys(requirements).forEach(key => {
                const req = requirements[key];
                const isValid = req.regex.test(val);
                req.el.className = isValid ? 'valid flex items-center' : 'invalid flex items-center';
                req.el.querySelector('.bullet').textContent = isValid ? '●' : '○';
            });
        });
    </script>
</body>
</html>