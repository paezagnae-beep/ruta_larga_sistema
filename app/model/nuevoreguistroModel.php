<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Usuario
{
    private $nombre;
    private $apellido;
    private $email;
    private $password;
    private $conexion;

    public function __construct($nombre, $apellido, $email, $password)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->password = $password;
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    public function existeEmail()
    {
        $sql = "SELECT Email FROM usuarios WHERE Email = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function registrar()
    {
        $hash = password_hash($this->password, PASSWORD_DEFAULT);
        // Se agregan los campos Nombre y Apellido al INSERT
        $sql = "INSERT INTO usuarios (Nombre, Apellido, Email, Contraseña) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssss", $this->nombre, $this->apellido, $this->email, $hash);
        return $stmt->execute();
    }
}

$mensaje = "";
$esError = false;

if (isset($_POST["guardar"])) {
    $nombre = $_POST["nombre"] ?? '';
    $apellido = $_POST["apellido"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';

    $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?\":{}|<>]).{8,}$/";

    if (!$nombre || !$apellido || !$email || !$password || !$confirm_password) {
        $mensaje = "Todos los campos son obligatorios.";
        $esError = true;
    } elseif ($password !== $confirm_password) {
        $mensaje = "Las contraseñas no coinciden.";
        $esError = true;
    } elseif (!preg_match($regex, $password)) {
        $mensaje = "La contraseña no cumple con los requisitos de seguridad.";
        $esError = true;
    } else {
        $usuario = new Usuario($nombre, $apellido, $email, $password);

        if ($usuario->existeEmail()) {
            $mensaje = "El correo electrónico ya está registrado.";
            $esError = true;
        } else {
            if ($usuario->registrar()) {
                header("Location: loginView.php?mensaje=registro_exitoso");
                exit();
            } else {
                $mensaje = "Ocurrió un error inesperado.";
                $esError = true;
            }
        }
    }
}