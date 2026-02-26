<?php
session_start();

// Evita que errores menores (Warnings) se inyecten en el HTML y rompan los enlaces
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD E INACTIVIDAD
$timeout = 600; 
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > $timeout)) {
    session_unset(); session_destroy();
    header("Location: login.php?mensaje=sesion_caducada");
    exit();
}
$_SESSION['ultima_actividad'] = time();

// 2. CLASE CONEXIÓN
class Conexion {
    private $servidor = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $bd = "proyecto";
    protected $conexion;

    public function __construct() {
        $this->conectar();
    }

    protected function conectar() {
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->bd);
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        $this->conexion->set_charset("utf8mb4");
    }
}

// 3. CLASE VEHÍCULO
class Vehiculo extends Conexion {
    private $id, $placa, $modelo, $marca;

    public function __construct() { parent::__construct(); }

    public function setId($v) { $this->id = intval($v); }
    public function setPlaca($v) { $this->placa = strtoupper(substr(trim($v), 0, 10)); }
    public function setModelo($v) { $this->modelo = substr(trim($v), 0, 40); } 
    public function setMarca($v) { $this->marca = $v; }

    public function listar() {
        return $this->conexion->query("SELECT * FROM vehiculos");
    }

    public function insertar() {
        $stmt = $this->conexion->prepare("INSERT INTO vehiculos (placa, modelo, marca) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->placa, $this->modelo, $this->marca);
        return $stmt->execute();
    }

    public function modificar() {
        $stmt = $this->conexion->prepare("UPDATE vehiculos SET placa=?, modelo=?, marca=? WHERE ID_vehiculo=?");
        $stmt->bind_param("sssi", $this->placa, $this->modelo, $this->marca, $this->id);
        return $stmt->execute();
    }

    public function eliminar($id_recibido) {
        $id_recibido = intval($id_recibido);
        $stmt = $this->conexion->prepare("DELETE FROM vehiculos WHERE ID_vehiculo = ?");
        $stmt->bind_param("i", $id_recibido);
        return $stmt->execute();
    }
}

$vehiculo = new Vehiculo();

// 4. PROCESAMIENTO
if (isset($_POST['registrar'])) {
    $vehiculo->setPlaca($_POST['placa']);
    $vehiculo->setModelo($_POST['modelo']);
    $vehiculo->setMarca($_POST['marca']);
    $vehiculo->insertar();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['editar'])) {
    $vehiculo->setId($_POST['ID_vehiculo']);
    $vehiculo->setPlaca($_POST['placa']);
    $vehiculo->setModelo($_POST['modelo']);
    $vehiculo->setMarca($_POST['marca']);
    $vehiculo->modificar();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $vehiculo->eliminar($_GET['delete']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = $vehiculo->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vehículos | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <style>
        .placa-mayus { text-transform: uppercase; font-weight: bold; font-family: monospace; }
        .navbar-dark { background-color: #1a1a1a; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark mb-4 shadow">
    <div class="container">
        <span class="navbar-brand font-weight-bold">RUTA LARGA - VEHÍCULOS</span>
        <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</nav>

<div class="container bg-white p-4 shadow-sm rounded">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Listado de Flota</h4>
        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalRegistro">
            + Nuevo Vehículo
        </a>
    </div>

    <table id="tablaData" class="table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>ID</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $result->fetch_assoc()): 
                // DETECCIÓN SEGURA DE ID (Evita el error que mencionaste)
                $id_actual = $fila['ID_vehiculo'] ?? $fila['id_vehiculo'] ?? 0;
            ?>
            <tr>
                <td><?= $id_actual ?></td>
                <td class="placa-mayus text-primary"><?= htmlspecialchars($fila['placa']) ?></td>
                <td><?= htmlspecialchars($fila['marca']) ?></td>
                <td><?= htmlspecialchars($fila['modelo']) ?></td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm btnEditar" 
                            data-id="<?= $id_actual ?>"
                            data-placa="<?= htmlspecialchars($fila['placa']) ?>"
                            data-marca="<?= htmlspecialchars($fila['marca']) ?>"
                            data-modelo="<?= htmlspecialchars($fila['modelo']) ?>"
                            data-toggle="modal" data-target="#modalEditar">Editar</button>
                    
                    <a href="?delete=<?= $id_actual ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('¿Eliminar vehículo con placa <?= $fila['placa'] ?>?')">Borrar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header"><h5>Registrar Nuevo Vehículo</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Placa</label>
                        <input type="text" name="placa" class="form-control placa-mayus" required maxlength="10" 
                               placeholder="ABC-123" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label>Marca</label>
                        <select name="marca" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Chevrolet">Chevrolet</option>
                            <option value="Ford">Ford</option>
                            <option value="Hyundai">Hyundai</option>
                            <option value="Mack">Mack</option>
                            <option value="Nissan">Nissan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" class="form-control" required maxlength="40" 
                               placeholder="Ej: Corolla, F-350, Silverado">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="registrar" class="btn btn-primary">Guardar Vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-info text-white"><h5>Editar Vehículo</h5></div>
                <div class="modal-body">
                    <input type="hidden" name="ID_vehiculo" id="edit_id">
                    <div class="form-group">
                        <label>Placa</label>
                        <input type="text" name="placa" id="edit_placa" class="form-control placa-mayus" required maxlength="10" 
                               oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label>Marca</label>
                        <select name="marca" id="edit_marca" class="form-control" required>
                            <option value="Toyota">Toyota</option>
                            <option value="Chevrolet">Chevrolet</option>
                            <option value="Ford">Ford</option>
                            <option value="Hyundai">Hyundai</option>
                            <option value="Mack">Mack</option>
                            <option value="Nissan">Nissan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" id="edit_modelo" class="form-control" required maxlength="40">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="editar" class="btn btn-info">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaData').DataTable({
        language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" }
    });

    $(document).on('click', '.btnEditar', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_placa').val($(this).data('placa'));
        $('#edit_marca').val($(this).data('marca'));
        $('#edit_modelo').val($(this).data('modelo'));
    });
});
</script>
</body>
</html>