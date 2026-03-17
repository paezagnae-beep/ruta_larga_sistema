<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once dirname(__DIR__) . "/config/session.php";
$sesion = new SessionManager();
$sesion->validarSesion();

require_once dirname(__DIR__) . "/controller/clientesController.php";
$controller = new ClienteController();
$data = $controller->manejarPeticiones();
$result = $data['result'];
$msg_js = $data['msg_js'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Clientes | Ruta Larga</title>
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
        .modal-header { background-color: #08082c; color: white; border-bottom: none; }
        .badge-tel { background: #e3f2fd; color: #0d47a1; font-weight: bold; border: 1px solid #bbdefb; }
        .text-date { font-size: 0.9rem; color: #495057; font-weight: 500; }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-custom mb-4 shadow">
        <div class="container">
            <span class="navbar-brand font-weight-bold">RUTA LARGA - CLIENTES</span>
            <a href="menuView.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
        </div>
    </nav>

    <div class="container bg-white p-4 shadow rounded">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Gestión de Clientes</h4>
            <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">+ Registrar Cliente</button>
        </div>

        <table id="tablaClientes" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Cédula / RIF</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Fecha de Registro</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['RIF_cedula']) ?></td>
                        <td class="font-weight-bold"><?= htmlspecialchars($fila['nombre']) ?></td>
                        <td><span class="badge badge-tel p-2"><?= htmlspecialchars($fila['telefono']) ?></span></td>
                        <td class="text-date text-center">
                            <?= date('d-m-Y', strtotime($fila['fecha_registro'])) ?>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm btnEditar" 
                                data-id="<?= $fila['ID_cliente'] ?>"
                                data-rif="<?= htmlspecialchars($fila['RIF_cedula']) ?>"
                                data-nombre="<?= htmlspecialchars($fila['nombre']) ?>"
                                data-tel="<?= htmlspecialchars($fila['telefono']) ?>" 
                                data-fecha="<?= $fila['fecha_registro'] ?>">Editar</button>
                                
                            <button class="btn btn-danger btn-sm"
                                onclick="confirmarEliminar(<?= $fila['ID_cliente'] ?>, '<?= addslashes($fila['nombre']) ?>')">Borrar</button>
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
                    <div class="modal-header"><h5>Nuevo Cliente</h5></div>
                    <div class="modal-body p-4">
                        <div class="form-group">
                            <label>Identificación</label>
                            <div class="input-group">
                                <select name="tipo_doc" class="form-control col-3 select-tipo-doc">
                                    <option value="V">V-</option>
                                    <option value="J">J-</option>
                                    <option value="E">E-</option>
                                    <option value="G">G-</option>
                                </select>
                                <input type="text" name="RIF_cedula" class="form-control input-doc-num" placeholder="Número" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" required maxlength="40">
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <div class="input-group">
                                <select name="operadora" class="form-control col-4">
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0412">0412</option>
                                    <option value="0416">0416</option>
                                    <option value="0212">0212</option>
                                </select>
                                <input type="text" name="telefono_num" class="form-control" placeholder="1234567" required maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Registro</label>
                            <input type="date" name="fecha_registro" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="registrar" class="btn btn-success btn-block font-weight-bold">Guardar Cliente</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form method="POST">
                    <div class="modal-header bg-info text-white"><h5>Editar Información</h5></div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="ID_cliente" id="edit_id">
                        <div class="form-group">
                            <label>Documento</label>
                            <div class="input-group">
                                <select name="tipo_doc" id="edit_tipo" class="form-control col-3 select-tipo-doc">
                                    <option value="V">V-</option>
                                    <option value="J">J-</option>
                                    <option value="E">E-</option>
                                    <option value="G">G-</option>
                                </select>
                                <input type="text" name="RIF_cedula" id="edit_rif_num" class="form-control input-doc-num" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <div class="input-group">
                                <select name="operadora" id="edit_operadora" class="form-control col-4">
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0412">0412</option>
                                    <option value="0416">0416</option>
                                </select>
                                <input type="text" name="telefono_num" id="edit_tel_num" class="form-control" required maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Registro</label>
                            <input type="date" name="fecha_registro" id="edit_fecha" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="editar" class="btn btn-info btn-block font-weight-bold">Actualizar Datos</button></div>
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
        $(document).ready(function () {
            $('#tablaClientes').DataTable({ language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } });

            function ajustarValidacionDoc() {
                $('.select-tipo-doc').each(function () {
                    const select = $(this);
                    const input = select.siblings('.input-doc-num');
                    input.attr('maxlength', (select.val() === 'V' || select.val() === 'E') ? '8' : '10');
                });
            }

            $('.select-tipo-doc').on('change', ajustarValidacionDoc);

            // Al abrir el modal de editar
            $('.btnEditar').on('click', function () {
                const fullRif = $(this).data('rif');
                const fullTel = $(this).data('tel').toString();
                
                $('#edit_id').val($(this).data('id'));
                $('#edit_tipo').val(fullRif.charAt(0));
                $('#edit_rif_num').val(fullRif.substring(1));
                $('#edit_nombre').val($(this).data('nombre'));
                $('#edit_operadora').val(fullTel.substring(0, 4));
                $('#edit_tel_num').val(fullTel.substring(4));
                $('#edit_fecha').val($(this).data('fecha'));

                ajustarValidacionDoc();
                $('#modalEditar').modal('show');
            });

            const status = new URLSearchParams(window.location.search).get('status');
            if (status === 'reg') Swal.fire('¡Registrado!', 'El cliente ha sido guardado.', 'success');
            if (status === 'edit') Swal.fire('¡Actualizado!', 'Datos modificados correctamente.', 'info');
            if (status === 'del') Swal.fire('Eliminado', 'El registro ya no existe.', 'error');
            
            ajustarValidacionDoc();
        });

        function confirmarEliminar(id, nombre) {
            Swal.fire({
                title: '¿Eliminar cliente?',
                text: "Se borrará a: " + nombre,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => { if (result.isConfirmed) window.location.href = `?delete=${id}`; });
        }
    </script>
</body>
</html>