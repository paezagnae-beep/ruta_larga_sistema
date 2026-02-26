<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class LoginModel extends Conexion {
    private $email;
    private $password;

    public function __construct($email, $password) {
        parent::__construct();
        $this->email = $email;
        $this->password = $password;
        $this->conectar();
    }

    public function validarUsuario() {
        $sql = "SELECT * FROM usuarios WHERE Email = ?";
        $stmt = $this->conexion->prepare($sql);
        if(!$stmt) return false;

        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            // Verifica el hash de 60 caracteres
            if(password_verify($this->password, $usuario["Contraseña"])) {
                return $usuario;
            }
        }
        return false;
    }
}