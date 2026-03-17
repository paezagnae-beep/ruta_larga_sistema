<?php
require_once dirname(__DIR__) . '/controller/estadisticasController.php';

$controlador = new EstadisticasController(new EstadisticasModel());
$data = $controlador->getEstadisticas();

$kpis = $data['kpis'];
$datosGrafico = $data['datosGrafico'];
$mesesLabels = $data['mesesLabels'];
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
        *{
            font-family: Georgia, 'Times New Roman', Times, serif;
        }
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
            url('../../assets/img/fondo.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }

        .navbar-custom { background-color: #08082c; }

        .card-stats {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s;
            background: white;
        }

        .card-stats:hover { transform: translateY(-5px); }

        .icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .bg-flete { background: #fff3e0; color: #ef6c00; }
        .bg-cliente { background: #e3f2fd; color: #0d47a1; }
        .bg-stock { background: #ffebee; color: #c62828; }

        .container-main {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-custom mb-4 shadow">
        <div class="container">
            <span class="navbar-brand font-weight-bold text-uppercase">Ruta Larga - Estadísticas</span>
            <a href="menuView.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-stats shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-flete mr-3"><i class="ph ph-truck fa-2x"></i></div>
                        <div>
                            <small class="text-muted text-uppercase font-weight-bold">Fletes Realizados</small>
                            <h3 class="mb-0 font-weight-bold"><?= $kpis['totalFletes'] ?? '0'; ?></h3>
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
                            <h3 class="mb-0 font-weight-bold"><?= $kpis['totalClientes'] ?? '0'; ?></h3>
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
                            <h3 class="mb-0 font-weight-bold text-danger"><?= $kpis['alertasStock'] ?? '0'; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body bg-light rounded">
                <h6 class="font-weight-bold text-uppercase mb-3"><i class="ph ph-file-xls mr-2 text-success"></i>Centro de Descargas Excel</h6>
                <form action="reporte_excel.php" method="GET" target="_blank">
                    <div class="row align-items-end">
                        <div class="col-md-3 form-group mb-0">
                            <label class="small font-weight-bold">Tipo de Reporte:</label>
                            <select name="tipo_reporte" class="form-control form-control-sm" required>
                                <option value="todo">Todo (General)</option>
                                <option value="fletes">Solo Fletes</option>
                                <option value="clientes">Solo Clientes</option>
                                <option value="choferes">Solo Choferes</option>
                                <option value="inventario">Solo Vehículos</option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group mb-0">
                            <label class="small font-weight-bold">Desde:</label>
                            <input type="date" name="f_inicio" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2 form-group mb-0">
                            <label class="small font-weight-bold">Hasta:</label>
                            <input type="date" name="f_fin" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2 form-group mb-0">
                            <button type="submit" class="btn btn-success btn-sm btn-block">
                                <i class="ph ph-download"></i> Descargar
                            </button>
                        </div>
                        <div class="col-md-3 form-group mb-0 text-right">
                             <a href="reporte_excel.php?tipo_reporte=todo" target="_blank" class="btn btn-outline-dark btn-sm btn-block">Histórico Completo</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="container-main mb-5">
            <div class="row">
                <div class="col-lg-8 border-right">
                    <h5 class="font-weight-bold text-uppercase mb-4">Fletes por Mes (Año Actual)</h5>
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
                        <small class="text-muted italic">Reporte sincronizado en tiempo real.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            const ctx = document.getElementById('graficoPrincipal').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(8, 8, 44, 0.4)');
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($mesesLabels); ?>,
                    datasets: [{
                        label: 'Número de Fletes',
                        data: <?php echo json_encode($datosGrafico); ?>,
                        borderColor: '#08082c',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ef6c00',
                        pointRadius: 5,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    }
                }
            });
        });
    </script>
</body>
</html>