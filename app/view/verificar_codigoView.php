<?php
session_start();

// Seguridad: Si no hay sesión de recuperación, volver al inicio
if (!isset($_SESSION['email_recu'])) {
    header("Location: recuperarcontrasena.php");
    exit();
}

require_once "../controller/verificarController.php";

$controller = new VerificarController();
$mensaje_status = "";
$esError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validar'])) {
    $data = $controller->manejarPeticion();
    $mensaje_status = $data['mensaje'];
    $esError = $data['error'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verificar Código | Ruta Larga</title>
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
        .code-input {
            letter-spacing: 0.8rem;
            text-align: center;
            padding-left: 0.8rem;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full glass-card p-8 rounded-2xl shadow-2xl">
        <div class="flex justify-center mb-6">
            <div class="bg-[#08082c] p-4 rounded-full shadow-lg">
                <i class="ph ph-shield-check text-white text-4xl"></i>
            </div>
        </div>

        <h1 class="text-2xl font-bold mb-2 text-center text-gray-800">Verificar Código</h1>
        <p class="text-gray-600 text-sm text-center mb-8">
            Ingresa el código de 6 dígitos enviado a:<br>
            <span class="font-bold text-[#08082c]"><?php echo $_SESSION['email_recu']; ?></span>
        </p>

        <?php if ($mensaje_status): ?>
            <div class="<?php echo $esError ? 'bg-red-100 border-l-4 border-red-500 text-red-800' : 'bg-blue-100 border-l-4 border-blue-500 text-blue-800'; ?> p-4 mb-6 text-sm rounded-r flex items-start shadow-sm">
                <i class="ph <?php echo $esError ? 'ph-warning-circle' : 'ph-info'; ?> text-lg mr-2"></i>
                <div>
                    <span class="font-bold"><?php echo $esError ? 'Atención' : 'Información'; ?></span>
                    <p><?php echo $mensaje_status; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest text-center">Código de Seguridad</label>
                <input 
                    type="number" 
                    name="codigo" 
                    required 
                    autofocus
                    placeholder="000000"
                    oninput="if(this.value.length > 6) this.value = this.value.slice(0, 6);"
                    class="w-full py-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#08082c] focus:border-transparent outline-none transition-all duration-200 bg-gray-50 text-3xl font-bold code-input" 
                />
            </div>

            <button 
                type="submit" 
                name="validar"
                class="w-full bg-[#08082c] hover:bg-[#1a1a4d] text-white py-3.5 rounded-xl font-bold shadow-xl flex items-center justify-center gap-2 transform transition active:scale-95 uppercase tracking-widest text-sm">
                <i class="ph ph-lock-key text-lg"></i>
                Validar Acceso
            </button>
        </form>

        <div class="text-center mt-10 pt-6 border-t border-gray-100">
            <a href="recuperarcontrasena.php" class="text-sm font-bold text-[#08082c] hover:text-blue-800 flex items-center justify-center gap-2 transition-colors">
                <i class="ph ph-arrow-u-up-left"></i> 
                ¿No recibiste el código? Reintentar
            </a>
        </div>
    </div>

</body>
</html>