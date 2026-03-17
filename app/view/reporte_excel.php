<?php
require_once dirname(__DIR__) . '/controller/reporteController.php';

if (ob_get_length()) ob_end_clean();

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_RutaLarga_" . date('d-m-Y') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$controller = new ReporteController();
$data = $controller->generarDatosExcel();
$tipo = $data['tipo'];

function procesarDato($dato) {
    if ($dato === null) return '';
    return mb_convert_encoding($dato, 'ISO-8859-1', 'UTF-8');
}
?>

<?php if (($tipo == 'todo' || $tipo == 'fletes') && $data['fletes']): ?>
<table border="1">
    <tr style="background-color: #08082c; color: white;"><th colspan="8">REPORTE DE FLETES</th></tr>
    <tr style="background-color: #cccccc;">
        <th>Fecha</th><th>Cliente</th><th>Chofer</th><th>Vehiculo</th><th>Origen</th><th>Destino</th><th>Valor</th><th>Pago</th>
    </tr>
    <?php while ($f = $data['fletes']->fetch_assoc()): ?>
    <tr>
        <td><?= !empty($f['fecha']) ? date("d/m/Y", strtotime($f['fecha'])) : '' ?></td>
        <td><?= procesarDato($f['cliente_nom'] ?? 'N/A') ?></td>
        <td><?= procesarDato($f['chofer_nom'] ?? 'N/A') ?></td>
        <td><?= $f['vehiculo_placa'] ?? '' ?></td>
        <td><?= procesarDato($f['origen'] ?? '') ?></td>
        <td><?= procesarDato($f['destino'] ?? '') ?></td>
        <td><?= number_format($f['valor'] ?? 0, 2) ?></td>
        <td><?= ($f['cancelado'] == 1) ? 'Pagado' : 'Pendiente' ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<br>
<?php endif; ?>

<?php if (($tipo == 'todo' || $tipo == 'clientes') && $data['clientes']): ?>
<table border="1">
    <tr style="background-color: #28a745; color: white;"><th colspan="3">DIRECTORIO DE CLIENTES</th></tr>
    <tr style="background-color: #cccccc;"><th>ID</th><th>Nombre</th><th>Telefono</th></tr>
    <?php while ($c = $data['clientes']->fetch_assoc()): ?>
    <tr>
        <td><?= $c['ID_cliente'] ?></td>
        <td><?= procesarDato($c['nombre'] ?? '') ?></td>
        <td><?= $c['telefono'] ?? '' ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<br>
<?php endif; ?>

<?php if (($tipo == 'todo' || $tipo == 'choferes') && $data['choferes']): ?>
<table border="1">
    <tr style="background-color: #17a2b8; color: white;"><th colspan="3">PERSONAL DE CHOFERES</th></tr>
    <tr style="background-color: #cccccc;"><th>ID</th><th>Nombre</th><th>Cedula</th></tr>
    <?php while ($ch = $data['choferes']->fetch_assoc()): ?>
    <tr>
        <td><?= $ch['ID_chofer'] ?></td>
        <td><?= procesarDato($ch['nombre'] ?? '') ?></td>
        <td><?= $ch['cedula'] ?? '' ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<br>
<?php endif; ?>

<?php if (($tipo == 'todo' || $tipo == 'inventario') && $data['vehiculos']): ?>
<table border="1">
    <tr style="background-color: #ffc107; color: black;"><th colspan="3">INVENTARIO DE VEHICULOS</th></tr>
    <tr style="background-color: #cccccc;"><th>ID</th><th>Placa</th><th>Modelo/Tipo</th></tr>
    <?php while ($v = $data['vehiculos']->fetch_assoc()): ?>
    <tr>
        <td><?= $v['id_vehiculo'] ?></td>
        <td><?= $v['placa'] ?></td>
        <td><?= procesarDato($v['modelo'] ?? 'Furgon') ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php endif; ?>