<?php

require_once dirname(__DIR__) . "/config/claseconexion.php";

class Chofer extends Conexion
{
    private $id, $rif, $nombre, $telefono, $fecha_registro;

    public function __construct()
    {
        parent::__construct();
        $this->conectar();
    }

    // --- Setters ---
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

    public function setFechaRegistro($v)
    {
        $this->fecha_registro = $v;
    }

    // --- Métodos de CRUD ---

    /**
     * Lista todos los choferes registrados
     */
    public function listar()
    {
        // Se mantiene el orden descendente para ver los registros más recientes primero
        return $this->conexion->query("SELECT ID_chofer, RIF_cedula, nombre, telefono, fecha_registro FROM choferes ORDER BY ID_chofer DESC");
    }

    /**
     * Registra un nuevo chofer
     */
    public function insertar()
    {
        // Se utiliza NOW() para que la base de datos asigne la hora del servidor
        $stmt = $this->conexion->prepare("INSERT INTO choferes (RIF_cedula, nombre, telefono, fecha_registro) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $this->rif, $this->nombre, $this->telefono);
        return $stmt->execute();
    }

    /**
     * Actualiza la información de un chofer existente
     */
    public function modificar()
    {
        $stmt = $this->conexion->prepare("UPDATE choferes SET RIF_cedula=?, nombre=?, telefono=? WHERE ID_chofer=?");
        $stmt->bind_param("sssi", $this->rif, $this->nombre, $this->telefono, $this->id);
        return $stmt->execute();
    }

    /**
     * Elimina un chofer por su ID
     */
    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM choferes WHERE ID_chofer = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}