<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once dirname(__DIR__) . "/controller/inventarioController.php";

$controller = new InventarioController();
$data = $controller->manejarPeticiones();

$result = $data['result'];
$vehiculos = $data['vehiculos']; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Inventario | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
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
        .modal-header { background-color: #08082c; color: white; }
        .glass-card { background: rgba(255, 255, 255, 0.95); border-radius: 15px; }
        .badge-pill { padding: 0.5em 1em; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-4 shadow">
    <div class="container">
        <span class="navbar-brand font-weight-bold">RUTA LARGA - INVENTARIO</span>
        <a href="menuView.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</nav>

<div class="container glass-card p-4 shadow-lg">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Control de Repuestos</h4>
        <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">+ Registrar Artículo</button>
    </div>

    <table id="tablaInventario" class="table table-striped table-bordered w-100 bg-white">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $result->fetch_assoc()): ?>
            <tr>
                <td class="font-weight-bold text-primary"><?= htmlspecialchars($fila['codigo']) ?></td>
                <td><strong><?= htmlspecialchars($fila['nombre']) ?></strong></td>
                <td>
                    <span class="badge badge-pill <?= ($fila['cantidad'] > 5) ? 'badge-success' : 'badge-danger' ?>">
                        <?= $fila['cantidad'] ?> unid.
                    </span>
                </td>
                <td>$<?= number_format($fila['precio_unidad'], 2) ?></td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm btnMovimiento" 
                            data-id="<?= $fila['id_producto'] ?>"
                            data-nom="<?= htmlspecialchars($fila['nombre']) ?>"
                            title="Movimiento">⇅</button>
                    
                    <button class="btn btn-info btn-sm btnEditar" 
                            data-id="<?= $fila['id_producto'] ?>"
                            data-cod="<?= htmlspecialchars($fila['codigo']) ?>"
                            data-nom="<?= htmlspecialchars($fila['nombre']) ?>"
                            data-des="<?= htmlspecialchars($fila['descripcion']) ?>"
                            data-can="<?= $fila['cantidad'] ?>"
                            data-pre="<?= $fila['precio_unidad'] ?>">Editar</button>
                    
                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?= $fila['id_producto'] ?>, '<?= addslashes($fila['nombre']) ?>')">Borrar</button>
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
                <div class="modal-header"><h5>Nuevo Artículo</h5></div>
                <div class="modal-body p-4">
                    <div class="form-group"><label>Código</label><input type="text" name="codigo" class="form-control" required></div>
                    <div class="form-group"><label>Nombre</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="form-group"><label>Descripción</label><textarea name="descripcion" class="form-control"></textarea></div>
                    <div class="row">
                        <div class="col-6"><label>Cantidad</label><input type="number" name="cantidad" class="form-control" value="0"></div>
                        <div class="col-6"><label>Precio ($)</label><input type="number" name="precio" step="0.01" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="registrar" class="btn btn-success btn-block">Guardar</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <form method="POST">
                <div class="modal-header bg-info text-white"><h5>Editar Artículo</h5></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id_producto_edit" id="edit_id">
                    <div class="form-group"><label>Código</label><input type="text" name="codigo" id="edit_cod" class="form-control" required></div>
                    <div class="form-group"><label>Nombre</label><input type="text" name="nombre" id="edit_nom" class="form-control" required></div>
                    <div class="form-group"><label>Descripción</label><textarea name="descripcion" id="edit_des" class="form-control"></textarea></div>
                    <div class="form-group"><label>Precio Unitario ($)</label><input type="number" name="precio" id="edit_pre" step="0.01" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" name="editar" class="btn btn-info btn-block">Actualizar</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMov" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0">
            <form method="POST">
                <div class="modal-header bg-warning"><h5 class="text-dark">Movimiento</h5></div>
                <div class="modal-body p-4 text-center">
                    <input type="hidden" name="id_mov" id="mov_id">
                    <input type="hidden" name="nom_mov" id="mov_nom_hidden">
                    <p><strong id="mov_nom_display"></strong></p>
                    <div class="form-group">
                        <select name="tipo_mov" id="tipo_mov" class="form-control" required>
                            <option value="entrada">📥 Entrada (+)</option>
                            <option value="salida">📤 Salida (-)</option>
                        </select>
                    </div>
                    <div class="form-group"><input type="number" name="cant_mov" class="form-control" min="1" value="1" required></div>
                    <div class="form-group"><input type="date" name="fecha_mov" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                    <div class="form-group" id="div_vehiculo" style="display: none;">
                        <select name="vehiculo_mov" class="form-control">
                            <option value="N/A">Seleccionar Vehículo</option>
                            <?php if($vehiculos): mysqli_data_seek($vehiculos, 0); while($v = $vehiculos->fetch_assoc()): ?>
                                <option value="<?= $v['placa'] ?>"><?= $v['placa'] ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="movimiento" class="btn btn-warning btn-block font-weight-bold">Procesar</button></div>
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
    $('#tablaInventario').DataTable({ language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } });

    // Cargar datos en Modal Editar
    $('#tablaInventario').on('click', '.btnEditar', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_cod').val($(this).data('cod'));
        $('#edit_nom').val($(this).data('nom'));
        $('#edit_des').val($(this).data('des'));
        $('#edit_pre').val($(this).data('pre'));
        $('#modalEditar').modal('show');
    });

    // Cargar datos en Modal Movimiento
    $('#tablaInventario').on('click', '.btnMovimiento', function() {
        $('#mov_id').val($(this).data('id'));
        $('#mov_nom_hidden').val($(this).data('nom'));
        $('#mov_nom_display').text($(this).data('nom'));
        $('#tipo_mov').val('entrada');
        $('#div_vehiculo').hide();
        $('#modalMov').modal('show');
    });

    // Mostrar vehículo solo en Salida
    $('#tipo_mov').on('change', function() {
        if ($(this).val() === 'salida') {
            $('#div_vehiculo').fadeIn();
        } else {
            $('#div_vehiculo').fadeOut();
        }
    });

    // Alertas de estado
    const params = new URLSearchParams(window.location.search);
    if (params.get('estado') === 'reg') Swal.fire('Éxito', 'Artículo registrado', 'success');
    if (params.get('estado') === 'edit') Swal.fire('Actualizado', 'Datos actualizados', 'info');
});

function confirmarEliminar(id, nombre) {
    Swal.fire({
        title: '¿Eliminar ' + nombre + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Borrar'
    }).then((res) => { if (res.isConfirmed) window.location.href = `?eliminar=${id}`; });
}
</script>
</body>
</html>