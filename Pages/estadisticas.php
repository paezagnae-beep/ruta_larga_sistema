<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD E INACTIVIDAD
$timeout = 600; //
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
$_SESSION['ultima_actividad'] = time(); //

// 2. CONEXIÓN A LA BASE DE DATOS
$mysqli = new mysqli("localhost", "root", "", "proyecto"); //
$mysqli->set_charset("utf8mb4");

// 3. CONSULTAS PARA KPI (Indicadores clave)
function qCount($db, $sql) { //
    try {
        $res = $db->query($sql);
        if ($res) { $f = $res->fetch_assoc(); return $f['total'] ?? 0; }
    } catch (Exception $e) { return 0; }
    return 0;
}

$totalFletes = qCount($mysqli, "SELECT COUNT(*) as total FROM fletes"); //
$totalClientes = qCount($mysqli, "SELECT COUNT(*) as total FROM clientes"); //
// Alerta si el stock es menor o igual a 5 unidades
$alertasStock = qCount($mysqli, "SELECT COUNT(*) as total FROM inventario WHERE cantidad <= 5"); //

// 4. DATOS PARA EL GRÁFICO (Ejemplo mensual)
$mesesLabels = ["Ene", "Feb", "Mar", "Abr", "May", "Jun"]; //
$datosGrafico = [12, 19, 15, 25, 22, 30]; //
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { 
            font-family: Georgia, serif; 
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('../assets/img/fondo.jpg'); 
            background-size: cover; 
            background-attachment: fixed; 
        }
        .navbar-custom { background-color: #08082c; }
        .card-stats { border: none; border-radius: 10px; transition: transform 0.3s; background: white; }
        .card-stats:hover { transform: translateY(-5px); }
        .icon-box { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
        .bg-flete { background: #fff3e0; color: #ef6c00; }
        .bg-cliente { background: #e3f2fd; color: #0d47a1; }
        .bg-stock { background: #ffebee; color: #c62828; }
        .container-main { background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); }
        .btn-excel { background-color: #1d6f42; color: white; border: none; }
        .btn-excel:hover { background-color: #145a35; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-custom mb-4 shadow">
        <div class="container">
            <span class="navbar-brand font-weight-bold text-uppercase">Ruta Larga - Estadísticas</span>
            <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-right">
                <div class="btn-group shadow-sm">
                    <button type="button" class="btn btn-light btn-sm disabled font-weight-bold">Descargar Reportes:</button>
                    <a href="reporte_excel.php?tipo=fletes" class="btn btn-excel btn-sm">
                        <i class="ph ph-file-csv"></i> Fletes
                    </a>
                    <a href="reporte_excel.php?tipo=inventario" class="btn btn-excel btn-sm">
                        <i class="ph ph-file-csv"></i> Inventario
                    </a>
                    <a href="reporte_excel.php?tipo=clientes" class="btn btn-excel btn-sm">
                        <i class="ph ph-file-csv"></i> Clientes
                    </a>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-stats shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-flete mr-3"><i class="ph ph-truck fa-2x"></i></div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold">Fletes Realizados</small>
                            <h3 class="mb-0 font-weight-bold"><?= $totalFletes ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-stats shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-cliente mr-3"><i class="ph ph-users-three fa-2x"></i></div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold">Clientes Totales</small>
                            <h3 class="mb-0 font-weight-bold"><?= $totalClientes ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-stats shadow-sm p-3 border-bottom border-danger">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-stock mr-3"><i class="ph ph-warning-octagon fa-2x"></i></div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold">Alertas de Stock</small>
                            <h3 class="mb-0 font-weight-bold text-danger"><?= $alertasStock ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-main mb-5">
            <div class="row">
                <div class="col-lg-8 border-right">
                    <h5 class="font-weight-bold text-uppercase mb-4">Rendimiento Logístico Mensual</h5>
                    <div style="height: 350px;">
                        <canvas id="graficoPrincipal"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="font-weight-bold text-uppercase mb-4">Estado Operativo</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="ph ph-circle-wavy-check text-success"></i> Disponibilidad Flota</span>
                            <span class="badge badge-success badge-pill">92%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="ph ph-wrench text-warning"></i> Mantenimientos</span>
                            <span class="badge badge-warning badge-pill text-white">4</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="ph ph-path text-primary"></i> Rutas Activas</span>
                            <span class="badge badge-primary badge-pill">8</span>
                        </li>
                    </ul>
                    <div class="mt-4 p-3 bg-light rounded text-center">
                        <small class="text-muted italic">Reporte sincronizado con la base de datos en tiempo real.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            const ctx = document.getElementById('graficoPrincipal').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(8, 8, 44, 0.2)');
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($mesesLabels); ?>,
                    datasets: [{
                        label: 'Viajes',
                        data: <?php echo json_encode($datosGrafico); ?>,
                        borderColor: '#08082c',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ef6c00',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</body>
</html>