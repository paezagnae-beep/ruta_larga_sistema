<?php
require_once dirname(__DIR__) . "/model/LoginModel.php";

class LoginController {
    public function iniciarSesion() {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $correo = trim($_POST["correo"] ?? "");
            $clave = trim($_POST["clave"] ?? "");

            if(empty($correo) || empty($clave)) return "Campos vacíos";

            $modelo = new LoginModel($correo, $clave);
            $usuario = $modelo->validarUsuario();

            if($usuario) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION["usuario"] = $usuario["Email"];
                $_SESSION["id_usuario"] = $usuario["ID"];
                header("Location: menuView.php");
                exit();
            }
            return "Username o contraseña incorrectos";
        }
        return null;
    }
}

// --- INICIO LÓGICA DE BLOQUEO ---

class LoginBloqueo {
    private bool $esError = false;
    private bool $bloqueado = false;
    private string $mensaje = '';
    private int $max_intentos = 3;
    private int $tiempo_bloqueo = 5 * 60; // segundos

    private LoginController $loginController;

    public function __construct(LoginController $loginController) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->loginController = $loginController;
        $this->chequearBloqueo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$this->bloqueado) {
            $this->procesarPost();
        }

        $this->chequearLogout();
    }

    private function chequearBloqueo(): void {
        if (isset($_SESSION['bloqueo_expira']) && time() < $_SESSION['bloqueo_expira']) {
            $this->bloqueado = true;
            $segundos = $_SESSION['bloqueo_expira'] - time();
            $minutos = ceil($segundos / 60);
            $this->mensaje = "Acceso restringido. Por seguridad, espera $minutos minuto(s) para intentar de nuevo.";
            $this->esError = true;
        }
    }

    private function procesarPost(): void {
        $this->mensaje = $this->loginController->iniciarSesion();

        if ($this->mensaje) {
            $this->esError = true;
            $_SESSION['intentos_fallidos'] = ($_SESSION['intentos_fallidos'] ?? 0) + 1;

            if ($_SESSION['intentos_fallidos'] >= $this->max_intentos) {
                $_SESSION['bloqueo_expira'] = time() + $this->tiempo_bloqueo;
                $_SESSION['intentos_fallidos'] = 0;
                $this->bloqueado = true;
                $this->mensaje = "Has superado los 3 intentos. Acceso bloqueado por 5 minutos.";
            }
        } else {
            $_SESSION['intentos_fallidos'] = 0;
            unset($_SESSION['bloqueo_expira']);
        }
    }

    private function chequearLogout(): void {
        if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'sesion_cerrada') {
            $this->mensaje = "Has salido del sistema correctamente.";
            $this->esError = false;
        }
    }

    public function isError(): bool {
        return $this->esError;
    }

    public function isBloqueado(): bool {
        return $this->bloqueado;
    }

    public function getMensaje(): string {
        return $this->mensaje;
    }
}

class LogoutHandler {
    public function __construct() {
        $this->logout();
    }

    private function logout(): void {
        // 1. Iniciamos la sesión para poder acceder a ella
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Limpiamos todas las variables de sesión ($_SESSION)
        $_SESSION = array();

        // 3. Si se desea destruir la sesión completamente, borramos también la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 4. Finalmente, destruimos la sesión en el servidor
        session_destroy();

        // 5. Redirigimos al login con un mensaje (opcional)
        header("Location: loginView.php?mensaje=sesion_cerrada");
        exit();
    }
}
// --- FIN LÓGICA DE BLOQUEO ---

// Ejemplo de uso:
// include_once __DIR__ . "/loginController.php"; // ya dentro
// $loginCtrl = new LoginController();
// $bloqueo  = new LoginBloqueo($loginCtrl);
// $esError = $bloqueo->isError();
// $bloqueado = $bloqueo->isBloqueado();
// $mensaje = $bloqueo->getMensaje();