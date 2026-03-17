<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Cliente extends Conexion
{
    private $id, $rif, $nombre, $telefono, $fecha;

    public function __construct()
    {
        parent::__construct();
        $this->conectar();
    }

    public function setId($v)
    {
        $this->id = intval($v);
    }
    public function setRif($v)
    {
        $this->rif = substr(trim($v), 0, 12);
    }
    public function setNombre($v)
    {
        $this->nombre = substr(trim($v), 0, 40);
    }
    public function setTelefono($v)
    {
        $this->telefono = substr(trim($v), 0, 11);
    }
    // Nuevo Setter para la fecha
    public function setFecha($v)
    {
        $this->fecha = $v; 
    }

    public function listar()
    {
        // Se mantiene el orden descendente para ver los más nuevos primero
        return $this->conexion->query("SELECT * FROM clientes ORDER BY ID_cliente DESC");
    }

    public function insertar()
    {
        // Se agrega fecha_registro a la consulta
        $stmt = $this->conexion->prepare("INSERT INTO clientes (RIF_cedula, nombre, telefono, fecha_registro) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $this->rif, $this->nombre, $this->telefono, $this->fecha);
        return $stmt->execute();
    }

    public function modificar()
    {
        // Se agrega fecha_registro a la actualización
        $stmt = $this->conexion->prepare("UPDATE clientes SET RIF_cedula=?, nombre=?, telefono=?, fecha_registro=? WHERE ID_cliente=?");
        $stmt->bind_param("ssssi", $this->rif, $this->nombre, $this->telefono, $this->fecha, $this->id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM clientes WHERE ID_cliente = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}