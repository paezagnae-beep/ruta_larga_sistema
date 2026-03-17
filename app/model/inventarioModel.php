<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Inventario extends Conexion {
    public function __construct() {
        parent::__construct();
        $this->conectar();
    }

    // Listar todos los repuestos
    public function listar() {
        return $this->conexion->query("SELECT * FROM inventario ORDER BY nombre ASC");
    }

    // Obtener un producto específico para calcular su cantidad actual
    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM inventario WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Obtener vehículos para el select de la vista (Módulo Salida)
    public function obtenerVehiculos() {
        // Ajusta 'vehiculos' y 'placa' si tus nombres de tabla/columna son distintos
        return $this->conexion->query("SELECT id_vehiculo, placa, modelo FROM vehiculos ORDER BY placa ASC");
    }

    // Esta es la función que ejecuta el cambio real en la base de datos
    public function actualizarCantidad($id, $nueva_cantidad) {
        $stmt = $this->conexion->prepare("UPDATE inventario SET cantidad = ? WHERE id_producto = ?");
        $stmt->bind_param("ii", $nueva_cantidad, $id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error al actualizar inventario: " . $this->conexion->error);
            return false;
        }
    }
}