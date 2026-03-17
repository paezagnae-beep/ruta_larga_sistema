<?php
class SessionManager {
    private $timeout;
    private $loginPage;

    public function __construct($timeoutSeconds = 600, $loginPage = "loginView.php") {
        $this->timeout = $timeoutSeconds;
        $this->loginPage = $loginPage;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // --- GETTERS ---
    public function getTimeout() {
        return $this->timeout;
    }

    public function getLoginPage() {
        return $this->loginPage;
    }

    // --- SETTERS ---
    public function setTimeout($seconds) {
        $this->timeout = (int)$seconds;
    }

    public function setLoginPage($path) {
        $this->loginPage = $path;
    }

    // --- LÓGICA PRINCIPAL ---
    public function validarSesion() {
        if (!isset($_SESSION["usuario"])) {
            $this->redireccionar();
        }

        if (isset($_SESSION['ultima_actividad'])) {
            $inactividad = time() - $_SESSION['ultima_actividad'];
            if ($inactividad > $this->timeout) {
                $this->cerrarSesion("sesion_caducada");
            }
        }
        $_SESSION['ultima_actividad'] = time();
    }

    public function cerrarSesion($motivo = "") {
        session_unset();
        session_destroy();
        $url = $this->loginPage . ($motivo ? "?mensaje=$motivo" : "");
        $this->redireccionar($url);
    }

    private function redireccionar($url = null) {
        $destino = $url ?? $this->loginPage;
        header("Location: $destino");
        exit();
    }
}