<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Vehiculo extends Conexion
{
    private $id, $placa, $modelo, $marca, $fecha;

    public function __construct()
    {
        parent::__construct();
        $this->conectar();
    }

    public function setId($v)
    {
        $this->id = intval($v);
    }

    public function setPlaca($v)
    {
        $this->placa = strtoupper(substr(trim($v), 0, 15));
    }

    public function setModelo($v)
    {
        $this->modelo = substr(trim($v), 0, 50);
    }

    public function setMarca($v)
    {
        $this->marca = substr(trim($v), 0, 50);
    }

    // Nuevo Setter para la fecha de registro
    public function setFecha($v)
    {
        $this->fecha = $v;
    }

    public function listar()
    {
        return $this->conexion->query("SELECT * FROM vehiculos ORDER BY id_vehiculo DESC");
    }

    public function insertar()
    {
        // Se agrega fecha_registro a la consulta. Se mantiene cliente_id en 0 por defecto.
        $stmt = $this->conexion->prepare("INSERT INTO vehiculos (placa, modelo, marca, fecha_registro, cliente_id) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssss", $this->placa, $this->modelo, $this->marca, $this->fecha);
        return $stmt->execute();
    }

    public function modificar()
    {
        // Se actualiza también la fecha_registro por si requiere corrección
        $stmt = $this->conexion->prepare("UPDATE vehiculos SET placa=?, modelo=?, marca=?, fecha_registro=? WHERE id_vehiculo=?");
        $stmt->bind_param("ssssi", $this->placa, $this->modelo, $this->marca, $this->fecha, $this->id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM vehiculos WHERE id_vehiculo = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}