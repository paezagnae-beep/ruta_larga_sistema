<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Recuperacion extends Conexion {
    public function __construct() {
        parent::__construct();
        $this->conectar();
    }

    public function buscarUsuarioPorEmail($email) {
        $stmt = $this->conexion->prepare("SELECT ID, nombre FROM usuarios WHERE Email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function actualizarToken($id, $codigo) {
        $stmt = $this->conexion->prepare("UPDATE usuarios SET token_recuperacion = ?, fecha_token = NOW() WHERE ID = ?");
        $stmt->bind_param("si", $codigo, $id);
        return $stmt->execute();
    }

    public function verificarCodigoValido($id, $codigo) {
        // Valida que el código coincida y que no tenga más de 15 minutos de antigüedad
        $stmt = $this->conexion->prepare("SELECT ID FROM usuarios 
                  WHERE ID = ? AND token_recuperacion = ? 
                  AND fecha_token >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->bind_param("is", $id, $codigo);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}