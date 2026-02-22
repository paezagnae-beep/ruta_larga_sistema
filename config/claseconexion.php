<?php
class Conexion {

    // ATRIBUTOS
    private $servidor;
    private $usuario;
    private $clave;
    private $bd;

    protected $conexion;

    // CONSTRUCTOR
    public function __construct(
        $servidor = "localhost",
        $usuario = "root",
        $clave = "",
        $bd = "proyecto"
    ) {
        $this->servidor = $servidor;
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->bd = $bd;
    }

    // ====================
    // GETTERS
    // ====================

    public function getServidor() { return $this->servidor; }
    public function getUsuario() { return $this->usuario; }
    public function getClave() { return $this->clave; }
    public function getBd() { return $this->bd; }

    // ====================
    // SETTERS
    // ====================

    public function setServidor($valor) { $this->servidor = $valor; }
    public function setUsuario($valor) { $this->usuario = $valor; }
    public function setClave($valor) { $this->clave = $valor; }
    public function setBd($valor) { $this->bd = $valor; }

    // ====================
    // MÉTODO CONEXIÓN
    // ====================

    protected function conectar() {
        $this->conexion = new mysqli(
            $this->getServidor(),
            $this->getUsuario(),
            $this->getClave(),
            $this->getBd()
        );

        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }

        return $this->conexion;
    }

    // ====================
    // MOSTRAR
    // ====================

    public function mostrar() {
        return "Servidor: " . $this->servidor . 
               " | Usuario: " . $this->usuario . 
               " | Base de datos: " . $this->bd;
    }
}


// =====================
// CLASE LOGIN
// =====================

class Login extends Conexion {

    private $email;
    private $password;

    // CONSTRUCTOR
    public function __construct($email = "", $password = "") {
        parent::__construct();
        $this->email = $email;
        $this->password = $password;
    }

    // GETTERS
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }

    // SETTERS
    public function setEmail($valor) { $this->email = $valor; }
    public function setPassword($valor) { $this->password = $valor; }

    // MOSTRAR
    public function mostrar() {
        return "Email: " . $this->email;
    }

    // ====================
    // MÉTODO PRINCIPAL
    // ====================

    public function validar() {

        $this->conectar();

        $sql = "SELECT * FROM usuarios WHERE Email = ? AND Contraseña = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $this->email, $this->password);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            header("Location: menu.php");
            exit;
        }
        else {
            header("Location: login.php?mensaje=1");
            exit;
    }
}
}