<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD E INACTIVIDAD
$timeout = 600;
if (!isset($_SESSION["usuario"])) { 
    header("Location: login.php"); 
    exit(); 
}

if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > $timeout)) {
    session_unset(); 
    session_destroy();
    header("Location: login.php?mensaje=sesion_caducada");
    exit();
}
$_SESSION['ultima_actividad'] = time();

// 2. CONEXIÓN Y CLASE VEHÍCULO
class Conexion {
    protected $conexion;
    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        $this->conexion->set_charset("utf8mb4");
    }
}

class Vehiculo extends Conexion {
    private $id, $placa, $modelo, $marca;

    public function setId($v) { $this->id = intval($v); }
    public function setPlaca($v) { $this->placa = strtoupper(substr(trim($v), 0, 10)); }
    public function setModelo($v) { $this->modelo = substr(trim($v), 0, 40); }
    public function setMarca($v) { $this->marca = substr(trim($v), 0, 20); }

    public function listar() { 
        return $this->conexion->query("SELECT * FROM vehiculos ORDER BY ID_vehiculo DESC"); 
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
    
    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM vehiculos WHERE ID_vehiculo = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$vehiculoObj = new Vehiculo();

// 3. PROCESAMIENTO DE ACCIONES (LÓGICA ANTES DEL LISTADO)

// --- ACCIÓN: ELIMINAR ---
if (isset($_GET['delete'])) {
    $id_borrar = intval($_GET['delete']);
    if ($id_borrar > 0) {
        $vehiculoObj->eliminar($id_borrar);
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
        exit();
    }
}

// --- ACCIÓN: REGISTRAR O EDITAR ---
if (isset($_POST['registrar']) || isset($_POST['editar'])) {
    $vehiculoObj->setPlaca($_POST['placa']);
    $vehiculoObj->setModelo($_POST['modelo']);
    $vehiculoObj->setMarca($_POST['marca']);

    if (isset($_POST['registrar'])) {
        $vehiculoObj->insertar();
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
    } else {
        $vehiculoObj->setId($_POST['ID_vehiculo']);
        $vehiculoObj->modificar();
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
    }
    exit();
}

// 4. OBTENER RESULTADOS ACTUALIZADOS (DESPUÉS DE BORRAR/EDITAR)
$result = $vehiculoObj->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vehículos | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    
    <style>
        body { 
            font-family: Georgia, 'Times New Roman', Times, serif; 
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/fondo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .navbar-custom { background-color: #08082c; }
        .modal-header { background-color: #08082c; color: white; }
        .placa-badge { background: #fff3e0; color: #e65100; font-weight: bold; border: 1px solid #ffe0b2; font-family: monospace; letter-spacing: 1px; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-4 shadow">
    <div class="container">
        <span class="navbar-brand font-weight-bold">RUTA LARGA - VEHÍCULOS</span>
        <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</nav>

<div class="container glass-card p-4 shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Listado de Flota</h4>
        <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">+ Nuevo Vehículo</button>
    </div>

    <table id="tablaVehiculos" class="table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $result->fetch_assoc()): ?>
            <tr>
                <td><span class="badge placa-badge p-2 text-uppercase"><?= htmlspecialchars($fila['placa']) ?></span></td>
                <td class="font-weight-bold"><?= htmlspecialchars($fila['marca']) ?></td>
                <td><?= htmlspecialchars($fila['modelo']) ?></td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm btnEditar" 
                            data-id="<?= $fila['ID_vehiculo'] ?>"
                            data-placa="<?= htmlspecialchars($fila['placa']) ?>"
                            data-marca="<?= htmlspecialchars($fila['marca']) ?>"
                            data-modelo="<?= htmlspecialchars($fila['modelo']) ?>"
                            data-toggle="modal" data-target="#modalEditar">Editar</button>
                    
                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?= $fila['ID_vehiculo'] ?>, '<?= $fila['placa'] ?>')">Borrar</button>
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
                <div class="modal-header"><h5>Registrar Vehículo</h5></div>
                <div class="modal-body p-4">
                    <div class="form-group"><label>Placa</label><input type="text" name="placa" class="form-control text-uppercase" required></div>
                    <div class="form-group">
                        <label>Marca</label>
                        <select name="marca" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <option value="Iveco">Iveco</option>
                            <option value="Chevrolet">Chevrolet</option>
                            <option value="Ford">Ford</option>
                            <option value="Mack">Mack</option>
                            <option value="Kenworth">Kenworth</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Modelo</label><input type="text" name="modelo" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" name="registrar" class="btn btn-success btn-block">Guardar Vehículo</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <form method="POST">
                <div class="modal-header"><h5>Editar Vehículo</h5></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="ID_vehiculo" id="edit_id">
                    <div class="form-group"><label>Placa</label><input type="text" name="placa" id="edit_placa" class="form-control text-uppercase" required></div>
                    <div class="form-group">
                        <label>Marca</label>
                        <select name="marca" id="edit_marca" class="form-control" required>
                            <option value="Iveco">Iveco</option>
                            <option value="Chevrolet">Chevrolet</option>
                            <option value="Ford">Ford</option>
                            <option value="Mack">Mack</option>
                            <option value="Kenworth">Kenworth</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Modelo</label><input type="text" name="modelo" id="edit_modelo" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" name="editar" class="btn btn-info btn-block">Actualizar</button></div>
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
$(document).ready(function() {
    $('#tablaVehiculos').DataTable({ language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } });

    $('.btnEditar').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_placa').val($(this).data('placa'));
        $('#edit_marca').val($(this).data('marca'));
        $('#edit_modelo').val($(this).data('modelo'));
    });

    const status = new URLSearchParams(window.location.search).get('status');
    if(status === 'reg') Swal.fire({icon:'success', title:'Vehículo Registrado', showConfirmButton:false, timer:1500});
    if(status === 'edit') Swal.fire({icon:'info', title:'Vehículo Actualizado', showConfirmButton:false, timer:1500});
    if(status === 'del') Swal.fire({icon:'error', title:'Vehículo Eliminado', showConfirmButton:false, timer:1500});
});

function confirmarEliminar(id, placa) {
    Swal.fire({
        title: '¿Eliminar vehículo ' + placa + '?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Se usa el nombre del archivo actual dinámicamente o relativo
            window.location.href = window.location.pathname + `?delete=${id}`;
        }
    });
}
</script>
</body>
</html>