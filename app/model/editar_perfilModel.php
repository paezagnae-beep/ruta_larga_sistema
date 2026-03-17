<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class EditarPerfilModel extends Conexion
{
    private $email;
    private $lastError = "";

    public function __construct()
    {
        parent::__construct();
        $this->conectar();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // El email se obtiene de la sesión como identificador único
        $this->email = $_SESSION["usuario"] ?? null;
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function obtenerUsuarioActual()
    {
        $stmt = $this->conexion->prepare("SELECT Email, Nombre, Apellido, Contraseña FROM usuarios WHERE Email = ?");
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    /**
     * Actualiza los datos del perfil.
     */
    public function actualizar($nuevoNombre, $nuevoApellido, $nuevaClave = '')
    {
        if (!$this->email) {
            $this->lastError = "Sesión no válida.";
            return false;
        }

        if (!empty($nuevaClave)) {
            // 1. Obtener la clave actual para comparar
            $usuario = $this->obtenerUsuarioActual();
            
            // 2. Verificar si la nueva clave es igual a la actual
            if (password_verify($nuevaClave, $usuario['Contraseña'])) {
                $this->lastError = "La nueva clave no puede ser igual a la clave actual.";
                return false;
            }

            // 3. Si es diferente, procedemos a actualizar con el nuevo Hash
            $hash = password_hash($nuevaClave, PASSWORD_DEFAULT);
            $stmt = $this->conexion->prepare("UPDATE usuarios SET Nombre = ?, Apellido = ?, Contraseña = ? WHERE Email = ?");
            $stmt->bind_param("ssss", $nuevoNombre, $nuevoApellido, $hash, $this->email);
        } else {
            // Actualización solo de nombre y apellido
            $stmt = $this->conexion->prepare("UPDATE usuarios SET Nombre = ?, Apellido = ? WHERE Email = ?");
            $stmt->bind_param("sss", $nuevoNombre, $nuevoApellido, $this->email);
        }
        
        $ejecucion = $stmt->execute();
        
        if (!$ejecucion) {
            $this->lastError = "Error al actualizar la base de datos.";
        }
        
        return $ejecucion;
    }
}