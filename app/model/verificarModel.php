<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Verificar extends Conexion {
    
    public function __construct() {
        parent::__construct();
        $this->conectar();
    }

    /**
     * Comprueba si el código es correcto y si se generó hace menos de 15 minutos.
     */
    public function validarCodigo($id_usuario, $codigo) {
        // Limpiamos los datos
        $id_usuario = intval($id_usuario);
        $codigo = trim($codigo);

        // La consulta verifica:
        // 1. Que el ID coincida.
        // 2. Que el token coincida.
        // 3. Que la fecha del token no sea mayor a 15 minutos atrás.
        $query = "SELECT ID FROM usuarios 
                  WHERE ID = ? 
                  AND token_recuperacion = ? 
                  AND fecha_token >= DATE_SUB(NOW(), INTERVAL 15 MINUTE) 
                  LIMIT 1";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("is", $id_usuario, $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Si existe un registro, el código es válido
        if ($resultado->num_rows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Opcional: Limpia el token después de usarlo para que no se pueda reutilizar.
     */
    public function invalidarToken($id_usuario) {
        $query = "UPDATE usuarios SET token_recuperacion = NULL, fecha_token = NULL WHERE ID = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }
}