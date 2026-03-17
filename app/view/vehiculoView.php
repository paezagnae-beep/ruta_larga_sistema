<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once dirname(__DIR__) . "/config/session.php";
$sesion = new SessionManager();
$sesion->validarSesion();

require_once dirname(__DIR__) . "/controller/vehiculoController.php";
$presenter = new VehiculoController();
$data = $presenter->manejarPeticiones();

$result = $data['result'];
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
        *{
            font-family: Georgia, 'Times New Roman', Times, serif;
        }
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../../assets/img/fondo.jpg');
            background-size: cover;
            background-attachment: fixed;
        }
        .navbar-custom { background-color: #08082c; }
        .modal-header { background-color: #08082c; color: white; }
        .placa-badge {
            background: #fff3e0;
            color: #e65100;
            font-weight: bold;
            border: 1px solid #ffe0b2;
            font-family: monospace;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
        }
        .text-date { font-size: 0.85rem; color: #6c757d; font-weight: bold; }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-custom mb-4 shadow">
        <div class="container">
            <span class="navbar-brand font-weight-bold">RUTA LARGA - VEHÍCULOS</span>
            <a href="menuView.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
        </div>
    </nav>

    <div class="container glass-card p-4 shadow rounded">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Listado de Vehículos</h4>
            <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">+ Nuevo Vehículo</button>
        </div>

        <table id="tablaVehiculos" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Fecha de Registro</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge placa-badge p-2 text-uppercase"><?= htmlspecialchars($fila['placa']) ?></span></td>
                        <td class="font-weight-bold"><?= htmlspecialchars($fila['marca']) ?></td>
                        <td><?= htmlspecialchars($fila['modelo']) ?></td>
                        <td class="text-date text-center">
                            <?= date('d-m-Y', strtotime($fila['fecha_registro'])) ?>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm btnEditar" 
                                data-id="<?= $fila['id_vehiculo'] ?>"
                                data-placa="<?= htmlspecialchars($fila['placa']) ?>"
                                data-marca="<?= htmlspecialchars($fila['marca']) ?>"
                                data-modelo="<?= htmlspecialchars($fila['modelo']) ?>"
                                data-fecha="<?= $fila['fecha_registro'] ?>">Editar</button>

                            <button class="btn btn-danger btn-sm"
                                onclick="confirmarEliminar(<?= $fila['id_vehiculo'] ?>, '<?= $fila['placa'] ?>')">Borrar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form method="POST">
                    <div class="modal-header"><h5>Registrar Vehículo</h5></div>
                    <div class="modal-body p-4">
                        <div class="form-group">
                            <label>Placa</label>
                            <input type="text" name="placa" class="form-control text-uppercase" required placeholder="ABC-123">
                        </div>
                        <div class="form-group">
                            <label>Marca</label>
                            <select name="marca" id="reg_marca" class="form-control select-marca" required>
                                <option value="" disabled selected>Seleccione Marca</option>
                                <option value="Chevrolet">Chevrolet</option>
                                <option value="Iveco">Iveco</option>
                                <option value="Ford">Ford</option>
                                <option value="Mack">Mack</option>
                                <option value="Kenworth">Kenworth</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Modelo</label>
                            <select name="modelo" id="reg_modelo" class="form-control select-modelo" required disabled>
                                <option value="">Primero elija una marca</option>
                            </select>
                            <input type="text" name="modelo_otro" id="reg_modelo_otro" class="form-control mt-2 d-none" placeholder="Especifique el modelo personalizado">
                        </div>
                        <div class="form-group">
                            <label>Fecha de Ingreso</label>
                            <input type="date" name="fecha_registro" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="registrar" class="btn btn-success btn-block font-weight-bold">Guardar Vehículo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form method="POST">
                    <div class="modal-header"><h5>Editar Vehículo</h5></div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_vehiculo_post" id="edit_id">
                        <div class="form-group">
                            <label>Placa</label>
                            <input type="text" name="placa" id="edit_placa" class="form-control text-uppercase" required>
                        </div>
                        <div class="form-group">
                            <label>Marca</label>
                            <select name="marca" id="edit_marca" class="form-control select-marca" required>
                                <option value="Chevrolet">Chevrolet</option>
                                <option value="Iveco">Iveco</option>
                                <option value="Ford">Ford</option>
                                <option value="Mack">Mack</option>
                                <option value="Kenworth">Kenworth</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Modelo</label>
                            <select name="modelo" id="edit_modelo" class="form-control select-modelo" required></select>
                            <input type="text" name="modelo_otro" id="edit_modelo_otro" class="form-control mt-2 d-none" placeholder="Especifique el modelo personalizado">
                        </div>
                        <div class="form-group">
                            <label>Fecha de Ingreso</label>
                            <input type="date" name="fecha_registro" id="edit_fecha" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="editar" class="btn btn-info btn-block font-weight-bold">Actualizar Cambios</button>
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
        const modelosPorMarca = {
            "Chevrolet": ["NPR", "NKR", "Kodiak", "Silverado 3500 HD"],
            "Iveco": ["Daily", "Eurocargo", "Trakker", "Stralis"],
            "Ford": ["F-350", "F-750", "Cargo 1721", "Cargo 815"],
            "Mack": ["Granite", "Vision", "Pinnacle", "R-Model"],
            "Kenworth": ["T800", "W900", "T680", "C500"]
        };

        function actualizarModelos(marcaSelect, modeloSelect, modeloSeleccionado = "") {
            const marca = $(marcaSelect).val();
            const $modelo = $(modeloSelect);
            const idInputOtro = (marcaSelect === '#reg_marca') ? '#reg_modelo_otro' : '#edit_modelo_otro';

            $modelo.empty().append('<option value="" disabled selected>Seleccione Modelo</option>');

            if (marca && modelosPorMarca[marca]) {
                modelosPorMarca[marca].forEach(function (mod) {
                    const selected = (mod === modeloSeleccionado) ? 'selected' : '';
                    $modelo.append(`<option value="${mod}" ${selected}>${mod}</option>`);
                });

                const esPersonalizado = (modeloSeleccionado !== "" && !modelosPorMarca[marca].includes(modeloSeleccionado));
                $modelo.append(`<option value="Otros" ${esPersonalizado ? 'selected' : ''}>Otros (Especificar)</option>`);
                $modelo.prop('disabled', false);

                if (esPersonalizado) {
                    $(idInputOtro).removeClass('d-none').val(modeloSeleccionado).prop('required', true);
                } else {
                    $(idInputOtro).addClass('d-none').prop('required', false);
                }
            } else {
                $modelo.prop('disabled', true);
                $(idInputOtro).addClass('d-none').prop('required', false);
            }
        }

        $(document).ready(function () {
            $('#tablaVehiculos').DataTable({ language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } });

            $('.select-modelo').on('change', function() {
                const idInputOtro = (this.id === 'reg_modelo') ? '#reg_modelo_otro' : '#edit_modelo_otro';
                if ($(this).val() === "Otros") {
                    $(idInputOtro).removeClass('d-none').focus().prop('required', true);
                } else {
                    $(idInputOtro).addClass('d-none').prop('required', false).val('');
                }
            });

            $('#reg_marca').on('change', function () { actualizarModelos('#reg_marca', '#reg_modelo'); });
            $('#edit_marca').on('change', function () { actualizarModelos('#edit_marca', '#edit_modelo'); });

            $('#tablaVehiculos').on('click', '.btnEditar', function () {
                $('#edit_id').val($(this).data('id'));
                $('#edit_placa').val($(this).data('placa'));
                $('#edit_marca').val($(this).data('marca'));
                $('#edit_fecha').val($(this).data('fecha'));

                actualizarModelos('#edit_marca', '#edit_modelo', $(this).data('modelo'));
                $('#modalEditar').modal('show');
            });

            const status = new URLSearchParams(window.location.search).get('status');
            if (status === 'reg') Swal.fire({ icon: 'success', title: 'Vehículo Registrado', showConfirmButton: false, timer: 1500 });
            if (status === 'edit') Swal.fire({ icon: 'info', title: 'Vehículo Actualizado', showConfirmButton: false, timer: 1500 });
            if (status === 'del') Swal.fire({ icon: 'error', title: 'Vehículo Eliminado', showConfirmButton: false, timer: 1500 });
        });

        function confirmarEliminar(id, placa) {
            Swal.fire({
                title: '¿Eliminar vehículo ' + placa + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => { if (result.isConfirmed) window.location.href = "?delete=" + id; });
        }
    </script>
</body>
</html>