<?php
require_once dirname(__DIR__) . "/model/editar_perfilModel.php";

class Usuario {
    private $db;
    private $error;

    public function __construct($host, $user, $pass, $dbName) {
        $this->db = new mysqli($host, $user, $pass, $dbName);
        if ($this->db->connect_error) {
            die("Error de conexión: " . $this->db->connect_error);
        }
    }

    // Obtener datos del usuario por su email
    public function obtenerDatos($email) {
        $stmt = $this->db->prepare("SELECT Email, Nombre, Apellido FROM usuarios WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Procesar la actualización del perfil
    public function actualizarPerfil($emailActual, $datos) {
        $nombre = $datos['nombre'];
        $apellido = $datos['apellido'];
        $old_pass = $datos['old_password']; // Clave actual ingresada
        $new_pass = $datos['new_password']; // Clave nueva ingresada

        // 1. Si intenta cambiar la clave, primero validamos la actual
        if (!empty($old_pass) || !empty($new_pass)) {
            
            // Verificación: No pueden ser iguales
            if ($old_pass === $new_pass) {
                $this->error = "La nueva contraseña no puede ser igual a la contraseña actual.";
                return false;
            }

            $stmt = $this->db->prepare("SELECT Contraseña FROM usuarios WHERE Email = ?");
            $stmt->bind_param("s", $emailActual);
            $stmt->execute();
            $resultado = $stmt->get_result()->fetch_assoc();

            if (!$resultado || !password_verify($old_pass, $resultado['Contraseña'])) {
                $this->error = "La contraseña actual es incorrecta.";
                return false;
            }
        }

        // 2. Validar requisitos de seguridad de la nueva contraseña (si se proporcionó una)
        if (!empty($new_pass)) {
            $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?\":{}|<>]).{8,}$/";
            
            if (!preg_match($regex, $new_pass)) {
                $this->error = "La nueva contraseña no cumple con los requisitos de seguridad (mayúscula, minúscula, símbolo y 8 caracteres).";
                return false;
            }
            
            // Caso: Actualizar con nueva contraseña
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE usuarios SET Nombre = ?, Apellido = ?, Contraseña = ? WHERE Email = ?");
            $stmt->bind_param("ssss", $nombre, $apellido, $hash, $emailActual);
        } else {
            // Caso: Actualizar solo nombre y apellido
            $stmt = $this->db->prepare("UPDATE usuarios SET Nombre = ?, Apellido = ? WHERE Email = ?");
            $stmt->bind_param("sss", $nombre, $apellido, $emailActual);
        }

        if ($stmt->execute()) {
            return true;
        } else {
            $this->error = "Error técnico: No se pudo actualizar la base de datos.";
            return false;
        }
    }

    public function getLastError() {
        return $this->error ?? "Error desconocido al procesar la solicitud.";
    }
}