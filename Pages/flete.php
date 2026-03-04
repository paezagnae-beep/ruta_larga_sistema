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

// 2. CLASES DE CONEXIÓN Y MODELO
class Conexion {
    protected $conexion;
    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        $this->conexion->set_charset("utf8mb4");
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }
}

class Flete extends Conexion {
    private $id, $id_cliente, $id_chofer, $id_vehiculo, $origen, $destino, $estado, $valor, $cancelado, $fecha;

    public function setId($v) { $this->id = intval($v); }
    public function setIdCliente($v) { $this->id_cliente = intval($v); }
    public function setIdChofer($v) { $this->id_chofer = intval($v); }
    public function setIdVehiculo($v) { $this->id_vehiculo = intval($v); }
    public function setOrigen($v) { $this->origen = substr(trim($v), 0, 100); }
    public function setDestino($v) { $this->destino = substr(trim($v), 0, 100); }
    public function setEstado($v) { $this->estado = $v; }
    public function setValor($v) { $this->valor = floatval($v); }
    public function setCancelado($v) { $this->cancelado = intval($v); }
    public function setFecha($v) { $this->fecha = $v; }

    public function listar() {
        $sql = "SELECT f.*, c.nombre AS cliente_nom, ch.nombre AS chofer_nom, v.placa AS vehiculo_placa
                FROM fletes f
                LEFT JOIN clientes c ON f.id_cliente = c.ID_cliente
                LEFT JOIN choferes ch ON f.id_chofer = ch.ID_chofer
                LEFT JOIN vehiculos v ON f.id_vehiculo = v.id_vehiculo
                ORDER BY f.id DESC";
        return $this->conexion->query($sql);
    }

    public function insertar() {
        $stmt = $this->conexion->prepare("INSERT INTO fletes (id_cliente, id_chofer, id_vehiculo, origen, destino, estado, valor, cancelado, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisssiis", $this->id_cliente, $this->id_chofer, $this->id_vehiculo, $this->origen, $this->destino, $this->estado, $this->valor, $this->cancelado, $this->fecha);
        return $stmt->execute();
    }

    public function actualizar() {
        $stmt = $this->conexion->prepare("UPDATE fletes SET id_cliente=?, id_chofer=?, id_vehiculo=?, origen=?, destino=?, estado=?, valor=?, cancelado=?, fecha=? WHERE id=?");
        $stmt->bind_param("iiisssiisi", $this->id_cliente, $this->id_chofer, $this->id_vehiculo, $this->origen, $this->destino, $this->estado, $this->valor, $this->cancelado, $this->fecha, $this->id);
        return $stmt->execute();
    }

    public function eliminar() {
        $stmt = $this->conexion->prepare("DELETE FROM fletes WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }

    public function obtenerClientes() { return $this->conexion->query("SELECT ID_cliente, nombre FROM clientes"); }
    public function obtenerChoferes() { return $this->conexion->query("SELECT ID_chofer, nombre FROM choferes"); }
    public function obtenerVehiculos() { return $this->conexion->query("SELECT id_vehiculo, placa FROM vehiculos"); }
}

$fleteObj = new Flete();
$msg_js = "";

// 3. PROCESAMIENTO
if (isset($_POST['registrar'])) {
    $fleteObj->setIdCliente($_POST['id_cliente']);
    $fleteObj->setIdChofer($_POST['id_chofer']);
    $fleteObj->setIdVehiculo($_POST['id_vehiculo']);
    $fleteObj->setOrigen($_POST['origen']);
    $fleteObj->setDestino($_POST['destino']);
    $fleteObj->setEstado($_POST['estado']);
    $fleteObj->setValor($_POST['valor']);
    $fleteObj->setCancelado($_POST['cancelado']);
    $fleteObj->setFecha($_POST['fecha']);

    if ($fleteObj->insertar()) {
        header("Location: flete.php?status=reg");
        exit();
    } else {
        $msg_js = "Swal.fire('Error', 'No se pudo guardar', 'error');";
    }
}

if (isset($_POST['editar'])) {
    $fleteObj->setId($_POST['id_flete']);
    $fleteObj->setIdCliente($_POST['id_cliente']);
    $fleteObj->setIdChofer($_POST['id_chofer']);
    $fleteObj->setIdVehiculo($_POST['id_vehiculo']);
    $fleteObj->setOrigen($_POST['origen']);
    $fleteObj->setDestino($_POST['destino']);
    $fleteObj->setEstado($_POST['estado']);
    $fleteObj->setValor($_POST['valor']);
    $fleteObj->setCancelado($_POST['cancelado']);
    $fleteObj->setFecha($_POST['fecha']);

    if ($fleteObj->actualizar()) {
        header("Location: flete.php?status=edit");
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    $fleteObj->setId($_GET['delete_id']);
    if ($fleteObj->eliminar()) {
        header("Location: flete.php?status=del");
        exit();
    }
}

$result = $fleteObj->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Fletes | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <style>
        body { background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('../assets/img/fondo.jpg'); background-size: cover; background-attachment: fixed; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background-color: #08082c; }
        .glass-card { background: rgba(255, 255, 255, 0.95); border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-4 shadow">
    <div class="container">
        <span class="navbar-brand font-weight-bold">RUTA LARGA - FLETES</span>
        <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</nav>

<div class="container-fluid px-5">
    <div class="glass-card p-4 shadow">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Listado de Fletes</h4>
            <button class="btn btn-success" data-toggle="modal" data-target="#modalFlete">Registrar Flete</button>
        </div>

        <table id="tablaFletes" class="table table-sm table-hover table-bordered w-100">
            <thead class="thead-light">
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Personal/Unidad</th>
                    <th>Ruta</th>
                    <th>Estado</th>
                    <th>Valor</th>
                    <th>Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($f = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= date("d/m/Y", strtotime($f['fecha'])) ?></td>
                    <td><?= htmlspecialchars($f['cliente_nom'] ?? 'N/A') ?></td>
                    <td>
                        <small>Chofer: <?= htmlspecialchars($f['chofer_nom'] ?? '---') ?></small><br>
                        <small>Placa: <?= htmlspecialchars($f['vehiculo_placa'] ?? '---') ?></small>
                    </td>
                    <td><small>De: <?= htmlspecialchars($f['origen']) ?><br>A: <?= htmlspecialchars($f['destino']) ?></small></td>
                    <td><span class="badge <?= $f['estado']=='Completado' ? 'badge-success' : 'badge-warning' ?>"><?= $f['estado'] ?></span></td>
                    <td>$<?= number_format($f['valor'], 2) ?></td>
                    <td><?= $f['cancelado'] ? 'Pagado' : 'Pendiente' ?></td>
                    <td class="text-center">
                        <button class="btn btn-info btn-sm btnEditar" 
                            data-id="<?= $f['id'] ?>" data-fecha="<?= $f['fecha'] ?>" 
                            data-cliente="<?= $f['id_cliente'] ?>" data-chofer="<?= $f['id_chofer'] ?>" 
                            data-vehiculo="<?= $f['id_vehiculo'] ?>" data-origen="<?= $f['origen'] ?>" 
                            data-destino="<?= $f['destino'] ?>" data-valor="<?= $f['valor'] ?>" 
                            data-estado="<?= $f['estado'] ?>" data-cancelado="<?= $f['cancelado'] ?>">
                            Editar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="borrarFlete(<?= $f['id'] ?>)">
                            Borrar
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalFlete" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="formFlete">
                <div class="modal-header bg-dark text-white"><h5 id="modalTitle">Nuevo Registro</h5></div>
                <div class="modal-body">
                    <input type="hidden" name="id_flete" id="id_flete">
                    <div class="row">
                        <div class="col-md-4 form-group"><label>Fecha</label><input type="date" name="fecha" id="fecha" class="form-control" required></div>
                        <div class="col-md-8 form-group">
                            <label>Cliente</label>
                            <select name="id_cliente" id="id_cliente" class="form-control" required>
                                <option value="">Seleccione Cliente...</option>
                                <?php $cts = $fleteObj->obtenerClientes(); while($c = $cts->fetch_assoc()) echo "<option value='{$c['ID_cliente']}'>{$c['nombre']}</option>"; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Chofer</label>
                            <select name="id_chofer" id="id_chofer" class="form-control" required>
                                <option value="">Seleccione Chofer...</option>
                                <?php $chs = $fleteObj->obtenerChoferes(); while($ch = $chs->fetch_assoc()) echo "<option value='{$ch['ID_chofer']}'>{$ch['nombre']}</option>"; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Vehículo</label>
                            <select name="id_vehiculo" id="id_vehiculo" class="form-control" required>
                                <option value="">Seleccione Unidad...</option>
                                <?php $vhs = $fleteObj->obtenerVehiculos(); while($v = $vhs->fetch_assoc()) echo "<option value='{$v['id_vehiculo']}'>{$v['placa']}</option>"; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Origen</label><input type="text" name="origen" id="origen" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>Destino</label><input type="text" name="destino" id="destino" class="form-control" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group"><label>Valor ($)</label><input type="number" name="valor" id="valor" class="form-control" step="0.01" required></div>
                        <div class="col-md-4 form-group">
                            <label>Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option>Pendiente</option><option>En Ruta</option><option>Completado</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>¿Pagado?</label>
                            <select name="cancelado" id="cancelado" class="form-control">
                                <option value="0">No</option><option value="1">Sí</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="registrar" id="btnSubmit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#tablaFletes').DataTable({ language: { url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } });

        $('.btnEditar').on('click', function() {
            $('#modalTitle').text('Editar Flete');
            $('#btnSubmit').attr('name', 'editar').text('Actualizar').removeClass('btn-primary').addClass('btn-info');
            
            $('#id_flete').val($(this).data('id'));
            $('#fecha').val($(this).data('fecha'));
            $('#id_cliente').val($(this).data('cliente'));
            $('#id_chofer').val($(this).data('chofer'));
            $('#id_vehiculo').val($(this).data('vehiculo'));
            $('#origen').val($(this).data('origen'));
            $('#destino').val($(this).data('destino'));
            $('#valor').val($(this).data('valor'));
            $('#estado').val($(this).data('estado'));
            $('#cancelado').val($(this).data('cancelado'));
            
            $('#modalFlete').modal('show');
        });

        $('#modalFlete').on('hidden.bs.modal', function () {
            $('#formFlete')[0].reset();
            $('#modalTitle').text('Nuevo Registro');
            $('#btnSubmit').attr('name', 'registrar').text('Guardar').removeClass('btn-info').addClass('btn-primary');
        });

        const status = new URLSearchParams(window.location.search).get('status');
        if(status === 'reg') Swal.fire({ icon: 'success', title: 'Guardado', showConfirmButton: false, timer: 1500 });
        if(status === 'edit') Swal.fire({ icon: 'info', title: 'Actualizado', showConfirmButton: false, timer: 1500 });
        if(status === 'del') Swal.fire({ icon: 'error', title: 'Borrado', showConfirmButton: false, timer: 1500 });
        
        <?= $msg_js ?>
    });

    function borrarFlete(id) {
        Swal.fire({
            title: '¿Desea borrar este registro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Borrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = 'flete.php?delete_id=' + id;
        });
    }
</script>
</body>
</html>