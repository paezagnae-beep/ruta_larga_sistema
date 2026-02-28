<?php
class Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        $this->conexion->set_charset("utf8");
    }

    public function getConexion() {
        return $this->conexion;
    }
}

class Usuario {
    private $email;
    private $password;
    private $conexion;

    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    public function registrar() {
        $hash = password_hash($this->password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (Email, Contraseña) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $this->email, $hash);
        return $stmt->execute();
    }
}

$mensaje = "";

if (isset($_POST["guardar"])) {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    if ($email && $password) {
        $usuario = new Usuario($email, $password);
        if ($usuario->registrar()) {
            $mensaje = "Usuario registrado correctamente";
        } else {
            $mensaje = "Error: el correo ya existe";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro</title>
  <script src="https://cdn.tailwindcss.com"></script>
    <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; }
    .navbar-custom { background-color: #08082c; }
    .modal-header { background-color: #08082c; color: white; }
    .badge-rif { background: #e8f5e9; color: #2e7d32; font-weight: bold; border: 1px solid #c8e6c9; }
</style>
    <style>
    body { 
        font-family: Georgia, 'Times New Roman', Times, serif; 
        /* Configuración de la imagen de fondo */
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/fondo.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
    /* Glassmorphism para las tarjetas si prefieres un estilo más moderno */
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
    }
</style>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex items-center justify-center">

  <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold mb-4 text-center">Registro Usuario</h1>

    <form method="POST" class="space-y-4">

      <?php if ($mensaje): ?>
        <p class="text-center text-green-500"><?= $mensaje ?></p>
      <?php endif; ?>

      <input type="email" name="email" placeholder="Correo electrónico" required
        class="w-full px-4 py-2 rounded border"/>

      <input type="password" name="password" placeholder="Contraseña" required
        class="w-full px-4 py-2 rounded border"/>

      <button type="submit" name="guardar"
        class="w-full bg-gray-500 hover:bg-gray-500 text-white py-2 rounded">
        Crear Cuenta
      </button>
    </form>

    <div class="text-center mt-6">
      <a href="login.php" class="text-gray-500 hover:underline">
        Volver al login
      </a>
    </div>

  </div>

</body>
</html>