<?php
/**
 * CLASE DE CONEXIÓN
 * Maneja la comunicación con la base de datos de forma segura.
 */
class Conexion {
    private $conexion;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->conexion = new mysqli("localhost", "root", "", "proyecto");
            $this->conexion->set_charset("utf8mb4");
        } catch (mysqli_sql_exception $e) {
            die("Error crítico de conexión: No se pudo establecer contacto con el servidor de datos.");
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
}

/**
 * CLASE USUARIO
 * Contiene la lógica de negocio y persistencia.
 */
class Usuario {
    private $email;
    private $password;
    private $conexion;

    public function __construct($email, $password) {
        $this->email = trim($email);
        $this->password = $password;
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // Verifica si el email ya existe para evitar errores de llave duplicada
    public function existeEmail() {
        $sql = "SELECT Email FROM usuarios WHERE Email = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0;
    }

    public function registrar() {
        if ($this->existeEmail()) {
            return "duplicado";
        }

        $hash = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (Email, Contraseña) VALUES (?, ?)";
        
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ss", $this->email, $hash);
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
}

// --- PROCESAMIENTO DEL FORMULARIO ---
$mensaje = "";
$tipoMensaje = "";

if (isset($_POST["guardar"])) {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"] ?? '';

    /**
     * EXPRESIÓN REGULAR EXPLICADA:
     * ^(?=.*[a-z])  -> Debe contener al menos una minúscula.
     * (?=.*[A-Z])   -> Debe contener al menos una mayúscula.
     * (?=.*[\W_])   -> Debe contener al menos un carácter especial (ej. !@#$...).
     * .{8,}         -> Longitud mínima de 8 caracteres.
     */
    $patronPassword = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Por favor, introduce un correo electrónico válido.";
        $tipoMensaje = "error";
    } elseif (!preg_match($patronPassword, $password)) {
        $mensaje = "La contraseña no cumple con los requisitos de seguridad.";
        $tipoMensaje = "error";
    } else {
        $usuario = new Usuario($email, $password);
        $resultado = $usuario->registrar();

        if ($resultado === true) {
            $mensaje = "¡Usuario registrado con éxito!";
            $tipoMensaje = "success";
            // Limpiamos el email para que no se quede en el input tras el éxito
            $_POST['email'] = ""; 
        } elseif ($resultado === "duplicado") {
            $mensaje = "Este correo electrónico ya está registrado.";
            $tipoMensaje = "error";
        } else {
            $mensaje = "Error interno al procesar el registro.";
            $tipoMensaje = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Seguro | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: Georgia, serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white p-10 rounded-2xl shadow-2xl border border-gray-100">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 border-b-4 border-gray-600 inline-block pb-2 italic">Registro</h1>
            <p class="text-gray-400 mt-4 text-xs uppercase tracking-widest">Crea tu cuenta de acceso</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="mb-6 p-4 rounded-lg text-sm flex items-start gap-3 border 
                <?= $tipoMensaje === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <span><?= htmlspecialchars($mensaje) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6" novalidate>
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Correo Electrónico</label>
                <input type="email" name="email" required
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                    placeholder="usuario@rutalarga.com"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Contraseña</label>
                <input type="password" id="password" name="password" required
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-gray-400 focus:bg-white outline-none transition-all">
                
                <div class="mt-3 bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Requisitos de seguridad:</p>
                    <ul class="text-[11px] text-gray-500 space-y-1 italic">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-gray-300 rounded-full"></span> Mínimo 8 caracteres
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-gray-300 rounded-full"></span> Al menos una Mayúscula y Minúscula
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-gray-300 rounded-full"></span> Al menos un carácter especial (@$!%*?&)
                        </li>
                    </ul>
                </div>
            </div>

            <button type="submit" name="guardar"
                class="w-full bg-[#666666] hover:bg-black text-white font-bold py-4 rounded-lg shadow-lg transform active:scale-95 transition-all duration-300 uppercase tracking-widest text-xs">
                Crear Mi Cuenta
            </button>
        </form>

        <div class="mt-8 text-center pt-6 border-t border-gray-100">
            <a href="login.php" class="text-xs text-gray-400 hover:text-gray-800 transition-colors uppercase tracking-widest italic">
                &larr; Volver al Inicio de Sesión
            </a>
        </div>
    </div>

    <footer class="fixed bottom-0 w-full py-4 text-center text-[9px] text-gray-400 uppercase tracking-[0.3em]">
        © 2026 Ruta Larga | Sistema de Gestión de Flota
    </footer>

</body>
</html>