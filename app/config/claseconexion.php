<?php
class Conexion {
    private $servidor = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $bd = "proyecto";
    protected $conexion;

    public function __construct() {}

    public function conectar() {
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->bd);
        if($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        $this->conexion->set_charset("utf8");
        return $this->conexion;
    }
}
?>