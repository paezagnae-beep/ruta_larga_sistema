<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD E INACTIVIDAD
$timeout = 600;
if (!isset($_SESSION["usuario"])) { header("Location: login.php"); exit(); }
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > $timeout)) {
    session_unset(); session_destroy();
    header("Location: login.php?mensaje=sesion_caducada");
    exit();
}
$_SESSION['ultima_actividad'] = time();

class Conexion {
    protected $conexion;
    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        $this->conexion->set_charset("utf8mb4");
    }
}

class Chofer extends Conexion {
    private $id, $rif, $nombre, $telefono;

    public function setId($v) { $this->id = intval($v); }
    public function setRif($v) { $this->rif = strtoupper(substr(trim($v), 0, 12)); } 
    public function setNombre($v) { $this->nombre = substr(trim($v), 0, 40); } 
    public function setTelefono($v) { $this->telefono = substr(trim($v), 0, 11); } 

    public function listar() { return $this->conexion->query("SELECT * FROM choferes ORDER BY ID_chofer DESC"); }

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

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM choferes WHERE ID_chofer = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$choferObj = new Chofer();
$msg_js = "";

// 3. PROCESAMIENTO
if (isset($_POST['registrar']) || isset($_POST['editar'])) {
    $tipo = $_POST['tipo_doc'] ?? 'V';
    $num_rif = preg_replace('/[^0-9]/', '', $_POST['RIF_cedula']); 
    $rif_final = $tipo . $num_rif;
    $nombre = trim($_POST['nombre']);
    $telf = preg_replace('/[^0-9]/', '', $_POST['telefono']); 

    if (strlen($num_rif) < 6) {
        $msg_js = "swalError('El documento es demasiado corto.');";
    } elseif (strlen($telf) != 11) {
        $msg_js = "swalError('El teléfono debe tener 11 dígitos.');";
    } else {
        $choferObj->setRif($rif_final);
        $choferObj->setNombre($nombre);
        $choferObj->setTelefono($telf);

        if (isset($_POST['registrar'])) {
            $choferObj->insertar();
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
        } else {
            $choferObj->setId($_POST['ID_chofer']);
            $choferObj->modificar();
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
        }
        exit();
    }
}

if (isset($_GET['delete'])) {
    $choferObj->eliminar(intval($_GET['delete']));
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
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
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; }
        .navbar-custom { background-color: #08082c; }
        .modal-header { background-color: #08082c; color: white; }
        .badge-rif { background: #e8f5e9; color: #2e7d32; font-weight: bold; border: 1px solid #c8e6c9; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-4 shadow">
    <div class="container">
        <span class="navbar-brand font-weight-bold">RUTA LARGA - CHOFERES</span>
        <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</nav>

<div class="container bg-white p-4 shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Gestión de Personal</h4>
        <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">+ Nuevo Chofer</button>
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
                <td><span class="badge badge-rif p-2"><?= htmlspecialchars($fila['RIF_cedula']) ?></span></td>
                <td class="font-weight-bold"><?= htmlspecialchars($fila['nombre']) ?></td>
                <td><?= htmlspecialchars($fila['telefono']) ?></td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm btnEditar" 
                            data-id="<?= $fila['ID_chofer'] ?>"
                            data-rif="<?= htmlspecialchars($fila['RIF_cedula']) ?>"
                            data-nombre="<?= htmlspecialchars($fila['nombre']) ?>"
                            data-tel="<?= htmlspecialchars($fila['telefono']) ?>"
                            data-toggle="modal" data-target="#modalEditar">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?= $fila['ID_chofer'] ?>, '<?= $fila['nombre'] ?>')">Borrar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <form method="POST">
                <div class="modal-header"><h5>Registrar Chofer</h5></div>
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label>Identificación</label>
                        <div class="input-group">
                            <select name="tipo_doc" class="form-control col-3">
                                <option value="V">V-</option><option value="E">E-</option><option value="J">J-</option>
                            </select>
                            <input type="text" name="RIF_cedula" class="form-control" placeholder="Número" required maxlength="10">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" required maxlength="40">
                    </div>
                    <div class="form-group">
                        <label>Teléfono (11 dígitos)</label>
                        <input type="text" name="telefono" class="form-control" placeholder="04141234567" required 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);">
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="registrar" class="btn btn-success btn-block">Guardar Chofer</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <form method="POST">
                <div class="modal-header"><h5>Editar Datos del Chofer</h5></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="ID_chofer" id="edit_id">
                    <div class="form-group">
                        <label>Identificación</label>
                        <div class="input-group">
                            <select name="tipo_doc" id="edit_tipo" class="form-control col-3">
                                <option value="V">V-</option><option value="E">E-</option><option value="J">J-</option>
                            </select>
                            <input type="text" name="RIF_cedula" id="edit_rif_num" class="form-control" required maxlength="10">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required maxlength="40">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="edit_tel" class="form-control" required 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);">
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="editar" class="btn btn-info btn-block">Actualizar Chofer</button></div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
function swalError(msg) { Swal.fire({ icon: 'error', title: 'Error', text: msg }); }

$(document).ready(function() {
    $('#tablaChoferes').DataTable({ language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } });

    $('.btnEditar').on('click', function() {
        let fullRif = $(this).data('rif');
        $('#edit_id').val($(this).data('id'));
        $('#edit_tipo').val(fullRif.charAt(0));
        $('#edit_rif_num').val(fullRif.substring(1));
        $('#edit_nombre').val($(this).data('nombre'));
        $('#edit_tel').val($(this).data('tel'));
    });

    const status = new URLSearchParams(window.location.search).get('status');
    if(status === 'reg') Swal.fire({icon:'success', title:'Chofer Guardado', showConfirmButton:false, timer:1500});
    if(status === 'edit') Swal.fire({icon:'info', title:'Datos Actualizados', showConfirmButton:false, timer:1500});
    if(status === 'del') Swal.fire({icon:'error', title:'Chofer Eliminado', showConfirmButton:false, timer:1500});
    <?= $msg_js ?>
});

function confirmarEliminar(id, nombre) {
    Swal.fire({
        title: '¿Eliminar a ' + nombre + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, borrar'
    }).then((result) => { if (result.isConfirmed) window.location.href = `?delete=${id}`; });
}
</script>
</body>
</html>