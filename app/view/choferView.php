<?php
require_once dirname(__DIR__) . '/config/session.php';

$sesion = new SessionManager();
$sesion->validarSesion();

require_once dirname(__DIR__) . "/controller/choferController.php";
$controller = new ChoferController();
$data = $controller->manejarPeticiones();

$result = $data['result'];
$msg_js = $data['msg_js'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Choferes | Ruta Larga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
        
        .badge-rif {
            background: #e8f5e9;
            color: #28a745;
            font-weight: bold;
            border: 1px solid #c8e6c9;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-custom mb-4 shadow">
        <div class="container">
            <span class="navbar-brand font-weight-bold">RUTA LARGA - CHOFERES</span>
            <a href="menuView.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
        </div>
    </nav>

    <div class="container glass-card p-4 shadow-lg">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Gestión de Choferes</h4>
            <div>
                <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">
                    <i class="fas fa-plus"></i> Registrar
                </button>
            </div>
        </div>

        <table id="tablaChoferes" class="table table-striped table-bordered w-100 bg-white">
            <thead>
                <tr>
                    <th>RIF / Cédula</th>
                    <th>Nombre Completo</th>
                    <th>Teléfono</th>
                    <th>Fecha de Registro</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result): ?>
                    <?php while ($fila = $result->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge badge-rif p-2"><?= htmlspecialchars($fila['RIF_cedula']) ?></span></td>
                            <td class="font-weight-bold"><?= htmlspecialchars($fila['nombre']) ?></td>
                            <td><?= htmlspecialchars($fila['telefono']) ?></td>
                            <td><?= date('d/m/Y', strtotime($fila['fecha_registro'])) ?></td>
                            <td class="text-center">
                                <button class="btn btn-info btn-sm btnEditar" 
                                    data-id="<?= $fila['ID_chofer'] ?>"
                                    data-rif="<?= htmlspecialchars($fila['RIF_cedula']) ?>"
                                    data-nombre="<?= htmlspecialchars($fila['nombre']) ?>"
                                    data-tel="<?= htmlspecialchars($fila['telefono']) ?>" 
                                    data-toggle="modal"
                                    data-target="#modalEditar">Editar</button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="confirmarEliminar(<?= $fila['ID_chofer'] ?>, '<?= $fila['nombre'] ?>')">Borrar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form action="" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Nuevo Chofer</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Tipo</label>
                                <select name="tipo_doc" class="form-control" required>
                                    <option value="V">V</option>
                                    <option value="E">E</option>
                                    <option value="J">J</option>
                                </select>
                            </div>
                            <div class="col-md-8 form-group">
                                <label>Cédula / RIF</label>
                                <input type="text" name="RIF_cedula" class="form-control" placeholder="12345678" required pattern="[0-9]+">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Operadora</label>
                                <select name="operadora" class="form-control" required>
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0412">0412</option>
                                    <option value="0416">0416</option>
                                    <option value="0426">0426</option>
                                </select>
                            </div>
                            <div class="col-md-8 form-group">
                                <label>Número (7 dígitos)</label>
                                <input type="text" name="telefono_num" class="form-control" maxlength="7" placeholder="1234567" required pattern="[0-9]{7}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="registrar" class="btn btn-success btn-block">Guardar Chofer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form action="" method="POST">
                    <input type="hidden" name="ID_chofer" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Datos de Chofer</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Tipo</label>
                                <select name="tipo_doc" id="edit_tipo" class="form-control" required>
                                    <option value="V">V</option>
                                    <option value="E">E</option>
                                    <option value="J">J</option>
                                </select>
                            </div>
                            <div class="col-md-8 form-group">
                                <label>Cédula / RIF</label>
                                <input type="text" name="RIF_cedula" id="edit_rif_num" class="form-control" required pattern="[0-9]+">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Operadora</label>
                                <select name="operadora" id="edit_operadora" class="form-control" required>
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0412">0412</option>
                                    <option value="0416">0416</option>
                                    <option value="0426">0426</option>
                                </select>
                            </div>
                            <div class="col-md-8 form-group">
                                <label>Número</label>
                                <input type="text" name="telefono_num" id="edit_tel_num" class="form-control" maxlength="7" required pattern="[0-9]{7}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="editar" class="btn btn-primary btn-block">Actualizar Cambios</button>
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
        $(document).ready(function () {
            $('#tablaChoferes').DataTable({ 
                language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" },
                order: [[3, "desc"]] 
            });

            $('.btnEditar').on('click', function () {
                const fullRif = $(this).data('rif');
                const fullTel = String($(this).data('tel'));
                
                $('#edit_id').val($(this).data('id'));
                $('#edit_tipo').val(fullRif.charAt(0));
                $('#edit_rif_num').val(fullRif.substring(1));
                $('#edit_nombre').val($(this).data('nombre'));
                
                if(fullTel.length >= 11) {
                    $('#edit_operadora').val(fullTel.substring(0, 4));
                    $('#edit_tel_num').val(fullTel.substring(4));
                }
            });

            <?= $msg_js ?>
        });

        function confirmarEliminar(id, nombre) {
            Swal.fire({
                title: '¿Eliminar a ' + nombre + '?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?delete=${id}`;
                }
            });
        }
    </script>
</body>
</html>