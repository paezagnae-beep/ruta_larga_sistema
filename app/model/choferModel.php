<?php

require_once dirname(__DIR__) . "/config/claseconexion.php";

class Chofer extends Conexion
{
    private $id, $rif, $nombre, $telefono;

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
        $this->rif = strtoupper(substr(trim($v), 0, 12));
    }
    public function setNombre($v)
    {
        $this->nombre = substr(trim($v), 0, 40);
    }
    public function setTelefono($v)
    {
        $this->telefono = substr(trim($v), 0, 11);
    }

    public function listar()
    {
        return $this->conexion->query("SELECT * FROM choferes ORDER BY ID_chofer DESC");
    }

    public function insertar()
    {
        $stmt = $this->conexion->prepare("INSERT INTO choferes (RIF_cedula, nombre, telefono) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->rif, $this->nombre, $this->telefono);
        return $stmt->execute();
    }

    public function modificar()
    {
        $stmt = $this->conexion->prepare("UPDATE choferes SET RIF_cedula=?, nombre=?, telefono=? WHERE ID_chofer=?");
        $stmt->bind_param("sssi", $this->rif, $this->nombre, $this->telefono, $this->id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM choferes WHERE ID_chofer = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}