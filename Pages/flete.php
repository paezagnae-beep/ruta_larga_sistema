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
        // Se eliminó id_cliente de la consulta
        $stmt = $this->conexion->prepare("INSERT INTO fletes (fecha, origen, destino, valor, cancelado) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("sssd", $fecha, $origen, $destino, $valor);
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
    if($reporte->insertar($_POST['fecha'], $_POST['origen'], $_POST['destino'], $_POST['valor'])) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=success&filtro=" . $filtro_actual);
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=error");
    }
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
    <title>Reportes | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    
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
<body>

<nav class="navbar navbar-dark navbar-custom mb-4 shadow">
    <div class="container">
        <span class="navbar-brand font-weight-bold">RUTA LARGA - REPORTES</span>
        <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</nav>

<div class="container-fluid px-5">
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h2 class="font-weight-bold text-dark mb-3">Monitor de Operaciones</h2>
            <div class="btn-group shadow-sm bg-white p-1 rounded">
                <a href="?filtro=dia" class="btn btn-<?= $filtro_actual == 'dia' ? 'primary' : 'light' ?> btn-sm">Hoy</a>
                <a href="?filtro=semana" class="btn btn-<?= $filtro_actual == 'semana' ? 'primary' : 'light' ?> btn-sm">Semana</a>
                <a href="?filtro=mes" class="btn btn-<?= $filtro_actual == 'mes' ? 'primary' : 'light' ?> btn-sm">Mes</a>
                <a href="?filtro=todo" class="btn btn-<?= $filtro_actual == 'todo' ? 'primary' : 'light' ?> btn-sm">Todo</a>
            </div>
        </div>
        <div class="col-md-6 text-right">
            <button class="btn btn-success px-4 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalFlete">+ Nuevo Flete</button>
        </div>
    </div>

    <div class="card shadow border-0 rounded-lg">
        <div class="card-body">
            <table id="tablaFletes" class="table table-striped w-100">
                <thead class="bg-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Ruta (Origen → Destino)</th>
                        <th>Monto Pactado</th>
                        <th class="text-center">Estado de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="align-middle"><?= date("d/m/Y", strtotime($fila['fecha'])) ?></td>
                        <td class="align-middle">
                            <?= htmlspecialchars($fila['origen']) ?> 
                            <i class="text-muted mx-2">→</i> 
                            <?= htmlspecialchars($fila['destino']) ?>
                        </td>
                        <td class="align-middle font-weight-bold text-dark">
                            $<?= number_format($fila['valor'], 2) ?>
                        </td>
                        <td class="text-center align-middle">
                            <button onclick="confirmarCambio(<?= $fila['id'] ?>, <?= $fila['cancelado'] ?>, '<?= $filtro_actual ?>')"
                                    class="badge badge-pill p-2 btn-estado <?= $fila['cancelado'] == 1 ? 'badge-success' : 'badge-danger' ?>">
                                <?= $fila['cancelado'] == 1 ? 'PAGADO' : 'PENDIENTE' ?>
                            </button>
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
        <div class="modal-content border-0">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">📝 Registrar Nuevo Flete</h5>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="small font-weight-bold">FECHA</label>
                        <input type="date" name="fecha" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">ORIGEN</label>
                        <input type="text" name="origen" class="form-control" placeholder="Punto de carga" required>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">DESTINO</label>
                        <input type="text" name="destino" class="form-control" placeholder="Punto de descarga" required>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">MONTO ($)</label>
                        <input type="number" step="0.01" name="valor" class="form-control form-control-lg" placeholder="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="registrar_flete" class="btn btn-success px-4 font-weight-bold">Guardar</button>
                </div>
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
    $('#tablaFletes').DataTable({
        "order": [[ 0, "desc" ]],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" }
    });

    const status = new URLSearchParams(window.location.search).get('status');
    if (status === 'success') Swal.fire({ icon: 'success', title: '¡Hecho!', text: 'Flete registrado exitosamente', timer: 2000, showConfirmButton: false });
    if (status === 'updated') Swal.fire({ icon: 'info', title: 'Actualizado', text: 'Estado de pago modificado', timer: 1500, showConfirmButton: false });
});

function confirmarCambio(id, valorActual, filtro) {
    const textoAccion = (valorActual == 1) ? 'marcar como PENDIENTE' : 'marcar como PAGADO';
    Swal.fire({
        title: '¿Actualizar pago?',
        text: `Deseas ${textoAccion} este flete.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: (valorActual == 1) ? '#d33' : '#28a745',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'No, cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `?id_cambio=${id}&valor=${valorActual}&filtro=${filtro}`;
        }
    });
}
</script>
</body>
</html>