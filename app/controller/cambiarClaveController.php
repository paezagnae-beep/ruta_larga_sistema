<?php
require_once dirname(__DIR__) . "/model/cambiarClaveModel.php";

class CambiarClaveController {
    public function manejarPeticion() {
        // Bloqueo de seguridad: Si no ha validado el código, fuera
        if (!isset($_SESSION['paso_verificado']) || !isset($_SESSION['id_usuario'])) {
            header("Location: recuperarcontrasena.php");
            exit();
        }

        $mensaje_status = "";
        $esError = false;
        $exito = false;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cambiar'])) {
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];

            if ($pass1 === $pass2 && strlen($pass1) >= 6) {
                $modelo = new CambiarClave();
                $hash = password_hash($pass1, PASSWORD_DEFAULT);
                $id = $_SESSION['id_usuario'];

                if ($modelo->actualizarPassword($id, $hash)) {
                    $exito = true;
                    $mensaje_status = "¡Contraseña actualizada con éxito!";
                    
                    // Limpiamos los rastros de la recuperación de la sesión
                    unset($_SESSION['paso_verificado']);
                    unset($_SESSION['codigo_verificacion']);
                    // No borramos id_usuario por si quieres loguearlo automáticamente, 
                    // aunque es mejor que vuelva a iniciar sesión.
                } else {
                    $mensaje_status = "Error técnico al actualizar la base de datos.";
                    $esError = true;
                }
            } else {
                $mensaje_status = "Las contraseñas no coinciden o son menores a 6 caracteres.";
                $esError = true;
            }
        }

        return [
            'mensaje' => $mensaje_status,
            'error' => $esError,
            'exito' => $exito
        ];
    }
}