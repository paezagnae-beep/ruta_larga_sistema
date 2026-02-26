<?php
require_once dirname(__DIR__) . "/model/LoginModel.php";

class LoginPresenter {
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
                header("Location: menu.php");
                exit();
            }
            return "Credenciales incorrectas";
        }
        return null;
    }
}