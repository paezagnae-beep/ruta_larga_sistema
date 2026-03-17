<?php
require_once dirname(__DIR__) . "/model/contrasenaModel.php";

class ContrasenaController {
    public function procesarCambio() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $modelo = new ContrasenaModel();

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['guardar_clave'])) {
            $pass1 = $_POST['nueva_pass'];
            $pass2 = $_POST['confirmar_pass'];
            $id_usuario = $_SESSION['id_usuario_recu'] ?? null;

            if (!$id_usuario) {
                return ['mensaje' => 'Sesión de recuperación inválida.', 'error' => true];
            }

            if ($pass1 !== $pass2) {
                return ['mensaje' => 'Las contraseñas no coinciden.', 'error' => true];
            }

            // Encriptamos la clave antes de guardarla
            $hash = password_hash($pass1, PASSWORD_DEFAULT);

            if ($modelo->actualizarPassword($id_usuario, $hash)) {
                // Destruimos la sesión de recuperación para finalizar el proceso
                session_destroy();
                
                // Redirección limpia al login
                echo "<script>
                        alert('Contraseña actualizada. Ya puede ingresar al sistema.');
                        window.location.href = '/app/view/loginView.php';
                      </script>";
                exit();
            } else {
                return ['mensaje' => 'Error al actualizar en la base de datos.', 'error' => true];
            }
        }
        return ['mensaje' => '', 'error' => false];
    }
}