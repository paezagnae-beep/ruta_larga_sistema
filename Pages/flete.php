<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD E INACTIVIDAD (10 minutos)
$timeout = 600; 
if (!isset($_SESSION["usuario"])) { header("Location: login.php"); exit(); }
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > $timeout)) {
    session_unset(); session_destroy();
    header("Location: login.php?mensaje=sesion_caducada");
    exit();
}
$_SESSION['ultima_actividad'] = time();

// 2. CONEXIÓN Y LÓGICA DE NEGOCIO
class Conexion {
    protected $conexion;
    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        if ($this->conexion->connect_error) { die("Error conexión: " . $this->conexion->connect_error); }
        $this->conexion->set_charset("utf8mb4");
    }
}

class ReporteFlete extends Conexion {
    private $filtro;
    public function __construct($filtro = 'todo') {
        parent::__construct();
        $this->filtro = $filtro;
    }

    public function mostrar() {
        switch ($this->filtro) {
            case 'dia': $sql = "SELECT * FROM fletes WHERE fecha = CURDATE() ORDER BY fecha DESC"; break;
            case 'semana': $sql = "SELECT * FROM fletes WHERE YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1) ORDER BY fecha DESC"; break;
            case 'mes': $sql = "SELECT * FROM fletes WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) ORDER BY fecha DESC"; break;
            default: $sql = "SELECT * FROM fletes ORDER BY fecha DESC"; break;
        }
        return $this->conexion->query($sql);
    }

    public function insertar($fecha, $origen, $destino, $valor) {
        $stmt = $this->conexion->prepare("INSERT INTO fletes (fecha, origen, destino, valor, cancelado) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("sssd", $fecha, $origen, $destino, $valor);
        return $stmt->execute();
    }

    public function actualizar($id, $fecha, $origen, $destino, $valor) {
        $stmt = $this->conexion->prepare("UPDATE fletes SET fecha=?, origen=?, destino=?, valor=? WHERE id=?");
        $stmt->bind_param("sssdi", $fecha, $origen, $destino, $valor, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM fletes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function cambiarCancelado($id, $valorActual) {
        $nuevoEstado = ($valorActual == 1) ? 0 : 1;
        $stmt = $this->conexion->prepare("UPDATE fletes SET cancelado = ? WHERE id = ?");
        $stmt->bind_param("ii", $nuevoEstado, $id);
        return $stmt->execute();
    }
}

$filtro_actual = $_GET['filtro'] ?? 'todo';
$reporte = new ReporteFlete($filtro_actual);

// 3. PROCESAMIENTO DE ACCIONES
if (isset($_POST['registrar_flete'])) {
    $reporte->insertar($_POST['fecha'], $_POST['origen'], $_POST['destino'], $_POST['valor']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=success&filtro=" . $filtro_actual);
    exit();
}
if (isset($_POST['editar_flete'])) {
    $reporte->actualizar($_POST['id_flete'], $_POST['fecha'], $_POST['origen'], $_POST['destino'], $_POST['valor']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=updated&filtro=" . $filtro_actual);
    exit();
}
if (isset($_GET['delete_id'])) {
    $reporte->eliminar($_GET['delete_id']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=deleted&filtro=" . $filtro_actual);
    exit();
}
if (isset($_GET['id_cambio']) && isset($_GET['valor'])) {
    $reporte->cambiarCancelado($_GET['id_cambio'], $_GET['valor']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=updated&filtro=" . $filtro_actual);
    exit();
}
$result = $reporte->mostrar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reportes | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../assets/img/fondo.jpg');
            background-size: cover; background-attachment: fixed;
        }
        .navbar-custom { background-color: #08082c; }
        .glass-card { background: rgba(255, 255, 255, 0.98); border-radius: 12px; border: none; }
        .btn-estado { cursor: pointer; border: none; transition: 0.3s; }
        .btn-estado:hover { opacity: 0.8; transform: scale(1.05); }
        
        @media (max-width: 768px) {
            .h2-title { font-size: 1.4rem; text-align: center; color: white; }
            .header-actions { flex-direction: column; }
            .btn-new { width: 100%; margin-top: 15px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-4 shadow">
    <div class="container">
        <span class="navbar-brand font-weight-bold">RUTA LARGA</span>
        <a href="menu.php" class="btn btn-outline-light btn-sm shadow-sm">Volver al Menú</a>
    </div>
</nav>

<div class="container-fluid px-3 px-md-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <h2 class="font-weight-bold text-white h2-title">Monitor de Operaciones</h2>
            <div class="btn-group shadow-sm bg-white p-1 rounded mt-2">
                <a href="?filtro=dia" class="btn btn-<?= $filtro_actual == 'dia' ? 'primary' : 'light' ?> btn-sm">Hoy</a>
                <a href="?filtro=semana" class="btn btn-<?= $filtro_actual == 'semana' ? 'primary' : 'light' ?> btn-sm">Semana</a>
                <a href="?filtro=mes" class="btn btn-<?= $filtro_actual == 'mes' ? 'primary' : 'light' ?> btn-sm">Mes</a>
                <a href="?filtro=todo" class="btn btn-<?= $filtro_actual == 'todo' ? 'primary' : 'light' ?> btn-sm">Todo</a>
            </div>
        </div>
        <div class="col-md-5 text-md-right">
            <button class="btn btn-success px-4 font-weight-bold shadow btn-new" data-toggle="modal" data-target="#modalFlete">+ Nuevo Flete</button>
        </div>
    </div>

    <div class="card shadow glass-card">
        <div class="card-body p-3">
            <table id="tablaFletes" class="table table-hover w-100 dt-responsive nowrap">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Ruta (Origen → Destino)</th>
                        <th>Monto</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="align-middle font-weight-bold"><?= date("d/m/Y", strtotime($fila['fecha'])) ?></td>
                        <td class="align-middle">
                            <span class="text-primary"><?= htmlspecialchars($fila['origen']) ?></span> 
                            <small class="text-muted">➔</small> 
                            <span class="text-success"><?= htmlspecialchars($fila['destino']) ?></span>
                        </td>
                        <td class="align-middle font-weight-bold text-dark">$<?= number_format($fila['valor'], 2) ?></td>
                        <td class="text-center align-middle">
                            <button onclick="confirmarCambio(<?= $fila['id'] ?>, <?= $fila['cancelado'] ?>, '<?= $filtro_actual ?>')"
                                    class="badge badge-pill p-2 btn-estado <?= $fila['cancelado'] == 1 ? 'badge-success' : 'badge-danger' ?>">
                                <?= $fila['cancelado'] == 1 ? 'PAGADO' : 'PENDIENTE' ?>
                            </button>
                        </td>
                        <td class="text-center align-middle">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-outline-info btn-sm btnEditar" 
                                        data-id="<?= $fila['id'] ?>"
                                        data-fecha="<?= $fila['fecha'] ?>"
                                        data-origen="<?= htmlspecialchars($fila['origen']) ?>"
                                        data-destino="<?= htmlspecialchars($fila['destino']) ?>"
                                        data-valor="<?= $fila['valor'] ?>">
                                    Editar
                                </button>
                                <button onclick="confirmarEliminar(<?= $fila['id'] ?>, '<?= $filtro_actual ?>')" class="btn btn-outline-danger btn-sm">
                                    Borrar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFlete" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">📝 Registrar Nuevo Flete</h5>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group"><label class="small font-weight-bold">FECHA</label><input type="date" name="fecha" class="form-control" required value="<?= date('Y-m-d') ?>"></div>
                    <div class="form-group"><label class="small font-weight-bold">ORIGEN</label><input type="text" name="origen" class="form-control" placeholder="Ciudad de salida" required></div>
                    <div class="form-group"><label class="small font-weight-bold">DESTINO</label><input type="text" name="destino" class="form-control" placeholder="Ciudad de llegada" required></div>
                    <div class="form-group"><label class="small font-weight-bold">MONTO PACTADO ($)</label><input type="number" step="0.01" name="valor" class="form-control form-control-lg text-primary font-weight-bold" placeholder="0.00" required></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="registrar_flete" class="btn btn-success px-4">Guardar Flete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarFlete" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold">✏️ Editar Información</h5>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id_flete" id="edit_id">
                    <div class="form-group"><label class="small font-weight-bold">FECHA</label><input type="date" name="fecha" id="edit_fecha" class="form-control" required></div>
                    <div class="form-group"><label class="small font-weight-bold">ORIGEN</label><input type="text" name="origen" id="edit_origen" class="form-control" required></div>
                    <div class="form-group"><label class="small font-weight-bold">DESTINO</label><input type="text" name="destino" id="edit_destino" class="form-control" required></div>
                    <div class="form-group"><label class="small font-weight-bold">MONTO ($)</label><input type="number" step="0.01" name="valor" id="edit_valor" class="form-control form-control-lg font-weight-bold" required></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="editar_flete" class="btn btn-info px-4">Actualizar Datos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.5/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Inicialización de Tabla Responsive
    $('#tablaFletes').DataTable({
        responsive: true,
        "order": [[ 0, "desc" ]],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" }
    });

    // Cargar datos en Modal Editar
    $('#tablaFletes').on('click', '.btnEditar', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_fecha').val($(this).data('fecha'));
        $('#edit_origen').val($(this).data('origen'));
        $('#edit_destino').val($(this).data('destino'));
        $('#edit_valor').val($(this).data('valor'));
        $('#modalEditarFlete').modal('show');
    });

    // Alertas de confirmación visual
    const status = new URLSearchParams(window.location.search).get('status');
    if (status === 'success') Swal.fire({ icon: 'success', title: 'Registrado', text: 'El flete se guardó correctamente', timer: 2000, showConfirmButton: false });
    if (status === 'updated') Swal.fire({ icon: 'info', title: 'Actualizado', text: 'Los cambios han sido aplicados', timer: 1500, showConfirmButton: false });
    if (status === 'deleted') Swal.fire({ icon: 'error', title: 'Eliminado', text: 'Registro borrado permanentemente', timer: 1500, showConfirmButton: false });
});

function confirmarEliminar(id, filtro) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, borrar flete',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `?delete_id=${id}&filtro=${filtro}`;
        }
    });
}

function confirmarCambio(id, valorActual, filtro) {
    const nuevoEstado = (valorActual == 1) ? 'PENDIENTE' : 'PAGADO';
    Swal.fire({
        title: '¿Cambiar estado?',
        text: `El flete se marcará como ${nuevoEstado}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `?id_cambio=${id}&valor=${valorActual}&filtro=${filtro}`;
        }
    });
}
</script>
</body>
</html>