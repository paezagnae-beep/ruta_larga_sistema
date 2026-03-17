<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Flete extends Conexion
{
    private $id, $id_cliente, $id_chofer, $id_vehiculo, $origen, $destino, $estado, $valor, $cancelado, $fecha;
    
    public function __construct()
    {
        parent::__construct();
        $this->conectar();
    }

    // --- SETTERS ---
    public function setId($v) { $this->id = intval($v); }
    public function setIdCliente($v) { $this->id_cliente = intval($v); }
    public function setIdChofer($v) { $this->id_chofer = intval($v); }
    public function setIdVehiculo($v) { $this->id_vehiculo = intval($v); }
    
    public function setOrigen($v) 
    { 
        $this->origen = substr(trim($v), 0, 100); 
    }

    public function setDestino($v) 
    { 
        $this->destino = substr(trim($v), 0, 100); 
    }

    public function setEstado($v) { $this->estado = trim($v); }
    public function setValor($v) { $this->valor = floatval($v); }
    public function setCancelado($v) { $this->cancelado = intval($v); }
    public function setFecha($v) { $this->fecha = $v; }

    // --- MÉTODOS CRUD ---

    /**
     * Obtiene todos los fletes con los nombres de las relaciones vinculadas
     */
    public function listar()
    {
        $sql = "SELECT f.*, c.nombre AS cliente_nom, ch.nombre AS chofer_nom, v.placa AS vehiculo_placa
                FROM fletes f
                LEFT JOIN clientes c ON f.id_cliente = c.ID_cliente
                LEFT JOIN choferes ch ON f.id_chofer = ch.ID_chofer
                LEFT JOIN vehiculos v ON f.id_vehiculo = v.id_vehiculo
                ORDER BY f.id DESC";
        return $this->conexion->query($sql);
    }

    /**
     * Inserta un nuevo flete
     */
    public function insertar()
    {
        $stmt = $this->conexion->prepare("INSERT INTO fletes (id_cliente, id_chofer, id_vehiculo, origen, destino, estado, valor, cancelado, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Corregido: i=entero, s=cadena, d=decimal (sin espacios en el string de tipos)
        $stmt->bind_param("iiisssdis", 
            $this->id_cliente, 
            $this->id_chofer, 
            $this->id_vehiculo, 
            $this->origen, 
            $this->destino, 
            $this->estado, 
            $this->valor, 
            $this->cancelado, 
            $this->fecha
        );
        
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /**
     * Actualiza un flete existente
     */
    public function actualizar()
    {
        $stmt = $this->conexion->prepare("UPDATE fletes SET id_cliente=?, id_chofer=?, id_vehiculo=?, origen=?, destino=?, estado=?, valor=?, cancelado=?, fecha=? WHERE id=?");
        
        // Corregido string de tipos para bind_param
        $stmt->bind_param("iiisssdisi", 
            $this->id_cliente, 
            $this->id_chofer, 
            $this->id_vehiculo, 
            $this->origen, 
            $this->destino, 
            $this->estado, 
            $this->valor, 
            $this->cancelado, 
            $this->fecha, 
            $this->id
        );
        
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    /**
     * Elimina un flete por su ID
     */
    public function eliminar()
    {
        $stmt = $this->conexion->prepare("DELETE FROM fletes WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    // --- MÉTODOS PARA LLENAR SELECTS EN LA VISTA ---

    public function obtenerClientes()
    {
        return $this->conexion->query("SELECT ID_cliente, nombre FROM clientes ORDER BY nombre ASC");
    }

    public function obtenerChoferes()
    {
        return $this->conexion->query("SELECT ID_chofer, nombre FROM choferes ORDER BY nombre ASC");
    }

    public function obtenerVehiculos()
    {
        return $this->conexion->query("SELECT id_vehiculo, placa FROM vehiculos ORDER BY placa ASC");
    }
}