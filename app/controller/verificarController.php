<?php
// Usamos el modelo Verificar que acabas de crear
require_once dirname(__DIR__) . "/model/verificarModel.php"; 

class VerificarController {
    public function manejarPeticion() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $verificarObj = new Verificar(); // Instancia del modelo actualizado

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['validar'])) {
            $codigo = trim($_POST['codigo']);
            $id_usuario = $_SESSION['id_usuario_recu'] ?? null;

            if (!$id_usuario) {
                return ['mensaje' => 'Sesión expirada. Inicie el proceso de nuevo.', 'error' => true];
            }

            // Llamamos al método que actualizamos en el modelo
            if ($verificarObj->validarCodigo($id_usuario, $codigo)) {
                // Si es correcto, lo mandamos a cambiar la clave
                header("Location: /app/view/nueva_contrasena.php");
                exit();
            } else {
                return ['mensaje' => 'Código incorrecto o ha expirado.', 'error' => true];
            }
        }
        return ['mensaje' => '', 'error' => false];
    }
}