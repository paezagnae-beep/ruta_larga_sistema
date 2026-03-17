<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once dirname(__DIR__) . "/config/session.php";
$sesion = new SessionManager();
$sesion->validarSesion();

require_once dirname(__DIR__) . '/controller/fleteController.php'; 

$controller = new FleteController();
$data = $controller->manejarPeticiones();

$fleteObj = $data['fleteObj']; 
$result = $data['result']; 
$msg_js = $data['msg_js'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Fletes | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
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
        .glass-card { background: rgba(255, 255, 255, 0.95); border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-4 shadow">
    <div class="container">
        <span class="navbar-brand font-weight-bold">RUTA LARGA - FLETES</span>
        <a href="menuView.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</nav>

<div class="container-fluid px-5">
    <div class="glass-card p-4 shadow">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Listado de Fletes</h4>
            <button class="btn btn-success" data-toggle="modal" data-target="#modalFlete"> + Registrar Flete</button>
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
                        <div class="col-md-6 form-group">
                            <label>Origen</label>
                            <select name="origen" id="origen" class="form-control" required>
                                <option value="">Seleccione Origen...</option>
                                <optgroup label="Gran Caracas">
                                    <option value="Libertador (Ccs)">Libertador (Ccs)</option>
                                    <option value="Chacao">Chacao</option>
                                    <option value="Baruta">Baruta</option>
                                    <option value="Sucre (Petare)">Sucre (Petare)</option>
                                    <option value="El Hatillo">El Hatillo</option>
                                    <option value="Los Teques">Los Teques</option>
                                    <option value="San Antonio de los Altos">San Antonio de los Altos</option>
                                </optgroup>
                                <optgroup label="Valles del Tuy">
                                    <option value="Charallave (Cristóbal Rojas)">Charallave</option>
                                    <option value="Cúa (Urdaneta)">Cúa</option>
                                    <option value="Ocumare del Tuy (Lander)">Ocumare del Tuy</option>
                                    <option value="Santa Teresa (Independencia)">Santa Teresa</option>
                                    <option value="Santa Lucía (Paz Castillo)">Santa Lucía</option>
                                    <option value="Yare (Simón Bolívar)">Yare</option>
                                </optgroup>
                                <optgroup label="Maracay y Aragua">
                                    <option value="Maracay (Girardot)">Maracay (Girardot)</option>
                                    <option value="Turmero (Mariño)">Turmero (Mariño)</option>
                                    <option value="El Limón (Iragoary)">El Limón (Iragoary)</option>
                                    <option value="La Victoria (Ribas)">La Victoria (Ribas)</option>
                                    <option value="Cagua (Sucre)">Cagua (Sucre)</option>
                                    <option value="Villa de Cura (Zamora)">Villa de Cura (Zamora)</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Destino</label>
                            <select name="destino" id="destino" class="form-control" required>
                                <option value="">Seleccione Destino...</option>
                                <optgroup label="Gran Caracas">
                                    <option value="Libertador (Ccs)">Libertador (Ccs)</option>
                                    <option value="Chacao">Chacao</option>
                                    <option value="Baruta">Baruta</option>
                                    <option value="Sucre (Petare)">Sucre (Petare)</option>
                                    <option value="El Hatillo">El Hatillo</option>
                                    <option value="Los Teques">Los Teques</option>
                                    <option value="San Antonio de los Altos">San Antonio de los Altos</option>
                                </optgroup>
                                <optgroup label="Valles del Tuy">
                                    <option value="Charallave (Cristóbal Rojas)">Charallave</option>
                                    <option value="Cúa (Urdaneta)">Cúa</option>
                                    <option value="Ocumare del Tuy (Lander)">Ocumare del Tuy</option>
                                    <option value="Santa Teresa (Independencia)">Santa Teresa</option>
                                    <option value="Santa Lucía (Paz Castillo)">Santa Lucía</option>
                                    <option value="Yare (Simón Bolívar)">Yare</option>
                                </optgroup>
                                <optgroup label="Maracay y Aragua">
                                    <option value="Maracay (Girardot)">Maracay (Girardot)</option>
                                    <option value="Turmero (Mariño)">Turmero (Mariño)</option>
                                    <option value="El Limón (Iragoary)">El Limón (Iragoary)</option>
                                    <option value="La Victoria (Ribas)">La Victoria (Ribas)</option>
                                    <option value="Cagua (Sucre)">Cagua (Sucre)</option>
                                    <option value="Villa de Cura (Zamora)">Villa de Cura (Zamora)</option>
                                </optgroup>
                            </select>
                        </div>
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
        
        <?= $msg_js; ?>
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
            if (result.isConfirmed) window.location.href = 'fleteview.php?delete_id=' + id;
        });
    }
</script>
</body>
</html>