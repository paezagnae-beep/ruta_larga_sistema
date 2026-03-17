<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Inventario extends Conexion
{
    public function __construct()
    {
        parent::__construct();
        $this->conectar();
    }

    public function listar()
    {
        return $this->conexion->query("SELECT * FROM inventario ORDER BY id_producto DESC");
    }

    // Insertar ahora incluye el stock_minimo
    public function insertar($cod, $nom, $des, $can, $min, $pre)
    {
        $stmt = $this->conexion->prepare("INSERT INTO inventario (codigo, nombre, descripcion, cantidad, stock_minimo, precio_unidad, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssidi", $cod, $nom, $des, $can, $min, $pre);
        return $stmt->execute();
    }

    // Modificar ahora incluye el stock_minimo
    public function modificar($id, $cod, $nom, $des, $can, $min, $pre)
    {
        $stmt = $this->conexion->prepare("UPDATE inventario SET codigo=?, nombre=?, descripcion=?, cantidad=?, stock_minimo=?, precio_unidad=?, fecha_actualizacion=NOW() WHERE id_producto=?");
        $stmt->bind_param("sssidi i", $cod, $nom, $des, $can, $min, $pre, $id);
        return $stmt->execute();
    }

    /**
     * Registra un movimiento y actualiza el stock principal
     * @param int $id ID del producto
     * @param int $cantidad Cantidad a mover
     * @param string $tipo 'entrada' o 'salida'
     * @param string $placa Solo para salidas
     * @param string $fecha Fecha del movimiento
     */
    public function registrarMovimiento($id, $cantidad, $tipo, $placa, $fecha)
    {
        // 1. Insertar en tabla de historial (asegúrate de tener esta tabla)
        $stmtHist = $this->conexion->prepare("INSERT INTO historial_inventario (id_producto, cantidad, tipo, placa_vehiculo, fecha_movimiento) VALUES (?, ?, ?, ?, ?)");
        $stmtHist->bind_param("iisss", $id, $cantidad, $tipo, $placa, $fecha);
        
        if ($stmtHist->execute()) {
            // 2. Actualizar el stock en la tabla principal
            $operacion = ($tipo == 'entrada') ? "cantidad + ?" : "cantidad - ?";
            $sqlStock = "UPDATE inventario SET cantidad = $operacion, fecha_actualizacion = NOW() WHERE id_producto = ?";
            
            $stmtStock = $this->conexion->prepare($sqlStock);
            $stmtStock->bind_param("ii", $cantidad, $id);
            return $stmtStock->execute();
        }
        return false;
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM inventario WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}