<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. CLASE CONEXIÓN
class Conexion {
    private $servidor = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $bd = "proyecto";
    protected $conexion;

    public function __construct() { $this->conectar(); }

    protected function conectar() {
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->bd);
        if ($this->conexion->connect_error) { die("Error de conexión: " . $this->conexion->connect_error); }
        $this->conexion->set_charset("utf8mb4");
    }
}

// 2. CLASE CHOFERES CON CRUD COMPLETO
class Choferes extends Conexion {
    private $id, $rif, $nombre, $telefono;

    public function __construct() { parent::__construct(); }

    public function setId($v) { $this->id = intval($v); }
    public function setRif($v) { $this->rif = strtoupper(trim($v)); }
    public function setNombre($v) { $this->nombre = trim($v); }
    public function setTelefono($v) { $this->telefono = trim($v); }

    public function listar() {
        return $this->conexion->query("SELECT * FROM choferes");
    }

    public function insertar() {
        $stmt = $this->conexion->prepare("INSERT INTO choferes (RIF_cedula, nombre, telefono) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->rif, $this->nombre, $this->telefono);
        return $stmt->execute();
    }

    public function modificar() {
        $stmt = $this->conexion->prepare("UPDATE choferes SET RIF_cedula=?, nombre=?, telefono=? WHERE ID_chofer=?");
        $stmt->bind_param("sssi", $this->rif, $this->nombre, $this->telefono, $this->id);
        return $stmt->execute();
    }

    public function eliminar($id_recibido) {
        $id_recibido = intval($id_recibido);
        $stmt = $this->conexion->prepare("DELETE FROM choferes WHERE ID_chofer = ?");
        $stmt->bind_param("i", $id_recibido);
        return $stmt->execute();
    }
}

$chofer = new Choferes();

// 3. PROCESAMIENTO DE ACCIONES
if (isset($_POST['registrar'])) {
    $chofer->setRif($_POST['rif']);
    $chofer->setNombre($_POST['nombre']);
    $chofer->setTelefono($_POST['telefono']);
    $chofer->insertar();
    header("Location: " . $_SERVER['PHP_SELF']); exit();
}

if (isset($_POST['editar'])) {
    $chofer->setId($_POST['ID_chofer']);
    $chofer->setRif($_POST['rif']);
    $chofer->setNombre($_POST['nombre']);
    $chofer->setTelefono($_POST['telefono']);
    $chofer->modificar();
    header("Location: " . $_SERVER['PHP_SELF']); exit();
}

if (isset($_GET['delete'])) {
    $chofer->eliminar($_GET['delete']);
    header("Location: " . $_SERVER['PHP_SELF']); exit();
}

$result = $chofer->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Choferes | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Listado de Choferes</h4>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalRegistro">+ Nuevo Chofer</button>
        </div>
        <div class="card-body">
            <a href="menu.php" class="btn btn-outline-secondary btn-sm mb-3">← Volver al Menú</a>

            <table id="tablaChoferes" class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>RIF / Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Teléfono</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($fila = $result->fetch_assoc()): 
                    $id_actual = $fila['ID_chofer'] ?? $fila['id_chofer'];
                ?>
                    <tr>
                        <td><?= $id_actual ?></td>
                        <td class="font-weight-bold"><?= $fila['RIF_cedula'] ?></td>
                        <td><?= $fila['nombre'] ?></td>
                        <td><?= $fila['telefono'] ?></td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm btnEditar" 
                                    data-id="<?= $id_actual ?>"
                                    data-rif="<?= $fila['RIF_cedula'] ?>"
                                    data-nombre="<?= $fila['nombre'] ?>"
                                    data-telefono="<?= $fila['telefono'] ?>"
                                    data-toggle="modal" data-target="#modalEditar">Editar</button>

                            <a href="?delete=<?= $id_actual ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar chofer?')">Borrar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header"><h5>Registrar Chofer</h5></div>
        <div class="modal-body">
            <div class="form-group"><label>RIF / Cédula</label><input type="text" name="rif" class="form-control" required style="text-transform: uppercase;"></div>
            <div class="form-group"><label>Nombre Completo</label><input type="text" name="nombre" class="form-control" required></div>
            <div class="form-group"><label>Teléfono</label><input type="text" name="telefono" class="form-control" placeholder="04XX-XXXXXXX" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button><button type="submit" name="registrar" class="btn btn-primary">Guardar</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-info text-white"><h5>Editar Chofer</h5></div>
        <div class="modal-body">
            <input type="hidden" name="ID_chofer" id="edit_id">
            <div class="form-group"><label>RIF / Cédula</label><input type="text" name="rif" id="edit_rif" class="form-control" required style="text-transform: uppercase;"></div>
            <div class="form-group"><label>Nombre Completo</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required></div>
            <div class="form-group"><label>Teléfono</label><input type="text" name="telefono" id="edit_telefono" class="form-control" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button type="submit" name="editar" class="btn btn-info">Actualizar</button></div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaChoferes').DataTable({
        language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" }
    });

    $(document).on('click', '.btnEditar', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_rif').val($(this).data('rif'));
        $('#edit_nombre').val($(this).data('nombre'));
        $('#edit_telefono').val($(this).data('telefono'));
    });
});
</script>

</body>
</html>