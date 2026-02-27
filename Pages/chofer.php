<?php
session_start();

// 1. SEGURIDAD E INACTIVIDAD (3 minutos)
$timeout = 180;
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > $timeout)) {
    session_unset(); session_destroy();
    header("Location: login.php?mensaje=sesion_caducada");
    exit();
}
$_SESSION['ultima_actividad'] = time();

// 2. CLASES DE CONEXIÓN Y LÓGICA
class Conexion {
    protected $conexion;
    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        if ($this->conexion->connect_error) { die("Error: " . $this->conexion->connect_error); }
        $this->conexion->set_charset("utf8mb4");
    }
}

class Chofer extends Conexion {
    private $id, $rif, $nombre, $telefono;

    public function setId($v) { $this->id = intval($v); }
    public function setRif($v) { $this->rif = strtoupper(substr(trim($v), 0, 12)); } 
    public function setNombre($v) { $this->nombre = substr(trim($v), 0, 40); } 
    public function setTelefono($v) { $this->telefono = substr(trim($v), 0, 11); } 

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
        $stmt = $this->conexion->prepare("DELETE FROM choferes WHERE ID_chofer = ?");
        $stmt->bind_param("i", $id_recibido);
        return $stmt->execute();
    }
}

$choferObj = new Chofer();

// 3. PROCESAMIENTO DE ACCIONES
if (isset($_POST['registrar']) || isset($_POST['editar'])) {
    $tipo = $_POST['tipo_doc'] ?? '';
    $num_rif = preg_replace('/[^0-9]/', '', $_POST['RIF_cedula']); 
    $rif_final = $tipo . $num_rif;
    
    $nombre = trim($_POST['nombre']);
    $telf = preg_replace('/[^0-9]/', '', $_POST['telefono']); 

    // Validaciones
    if (strlen($num_rif) < 6 || strlen($num_rif) > 10) {
        $error = "El número de documento debe tener entre 6 y 10 dígitos.";
    } elseif (strlen($nombre) > 40) {
        $error = "El nombre no puede exceder los 40 caracteres.";
    } elseif (strlen($telf) != 11) {
        $error = "El teléfono debe tener exactamente 11 números.";
    } else {
        if (isset($_POST['registrar'])) {
            $choferObj->setRif($rif_final);
            $choferObj->setNombre($nombre);
            $choferObj->setTelefono($telf);
            $choferObj->insertar();
        } else {
            $choferObj->setId($_POST['ID_chofer']);
            $choferObj->setRif($rif_final);
            $choferObj->setNombre($nombre);
            $choferObj->setTelefono($telf);
            $choferObj->modificar();
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if (isset($_GET['delete'])) {
    $choferObj->eliminar(intval($_GET['delete']));
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = $choferObj->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Choferes | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
</head>
<body class="bg-light">

<div class="navbar navbar-dark bg-dark mb-4 shadow">
    <div class="container">
        <span class="navbar-brand italic font-weight-bold">RUTA LARGA</span>
        <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</div>

<div class="container bg-white p-4 shadow-sm rounded">
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-4">
        <h4>Gestión de Choferes</h4>
        <button class="btn btn-success" data-toggle="modal" data-target="#modalRegistro">+ Nuevo Chofer</button>
    </div>

    <table id="tablaChoferes" class="table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>RIF / Cédula</th>
                <th>Nombre Completo</th>
                <th>Teléfono</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = $result->fetch_assoc()): ?>
            <tr>
                <td class="font-weight-bold"><?= htmlspecialchars($fila['RIF_cedula']) ?></td>
                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                <td><?= htmlspecialchars($fila['telefono']) ?></td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm btnEditar" 
                            data-id="<?= $fila['ID_chofer'] ?>"
                            data-rif="<?= htmlspecialchars($fila['RIF_cedula']) ?>"
                            data-nombre="<?= htmlspecialchars($fila['nombre']) ?>"
                            data-tel="<?= htmlspecialchars($fila['telefono']) ?>"
                            data-toggle="modal" data-target="#modalEditar">Editar</button>
                    <a href="?delete=<?= $fila['ID_chofer'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar chofer?')">Borrar</a>
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
          <div class="modal-header"><h5>Registrar Chofer</h5></div>
          <div class="modal-body">
              <div class="form-group">
                  <label>Documento de Identidad</label>
                  <div class="input-group">
                      <select name="tipo_doc" class="form-control col-3">
                          <option value="V">V-</option><option value="E">E-</option><option value="J">J-</option>
                      </select>
                      <input type="text" name="RIF_cedula" class="form-control" placeholder="Solo números" required maxlength="10">
                  </div>
              </div>
              <div class="form-group">
                  <label>Nombre Completo</label>
                  <input type="text" name="nombre" class="form-control" maxlength="40" required>
              </div>
              <div class="form-group">
                  <label>Teléfono (11 números)</label>
                  <input type="text" name="telefono" class="form-control" placeholder="04XXXXXXXXX" required 
                         oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);">
              </div>
          </div>
          <div class="modal-footer"><button type="submit" name="registrar" class="btn btn-primary">Guardar Chofer</button></div>
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
              <div class="form-group">
                  <label>Documento</label>
                  <div class="input-group">
                      <select name="tipo_doc" id="edit_tipo" class="form-control col-3">
                          <option value="V">V-</option><option value="E">E-</option><option value="J">J-</option>
                      </select>
                      <input type="text" name="RIF_cedula" id="edit_rif_num" class="form-control" required maxlength="10">
                  </div>
              </div>
              <div class="form-group">
                  <label>Nombre Completo</label>
                  <input type="text" name="nombre" id="edit_nombre" class="form-control" maxlength="40" required>
              </div>
              <div class="form-group">
                  <label>Teléfono</label>
                  <input type="text" name="telefono" id="edit_telefono" class="form-control" required 
                         oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);">
              </div>
          </div>
          <div class="modal-footer"><button type="submit" name="editar" class="btn btn-info">Actualizar Datos</button></div>
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
    $('#tablaChoferes').DataTable({ language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } });

    $('.btnEditar').on('click', function() {
        let fullRif = $(this).data('rif');
        let tipo = fullRif.charAt(0);
        let num = fullRif.substring(1);
        $('#edit_id').val($(this).data('id'));
        $('#edit_tipo').val(tipo);
        $('#edit_rif_num').val(num);
        $('#edit_nombre').val($(this).data('nombre'));
        $('#edit_telefono').val($(this).data('tel'));
    });
});
</script>
</body>
</html>