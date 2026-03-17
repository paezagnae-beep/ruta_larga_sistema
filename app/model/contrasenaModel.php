<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class ContrasenaModel extends Conexion {
    public function __construct() {
        parent::__construct();
        $this->conectar();
    }

    public function actualizarPassword($id_usuario, $password_hash) {
        // Se ha modificado 'password' por 'contraseña' para que coincida con tu tabla
        $query = "UPDATE usuarios SET contraseña = ?, token_recuperacion = NULL, fecha_token = NULL WHERE ID = ?";
        
        $stmt = $this->conexion->prepare($query);
        
        if ($stmt === false) {
            die("Error en la preparación: " . $this->conexion->error);
        }

        $stmt->bind_param("si", $password_hash, $id_usuario);
        return $stmt->execute();
    }
}