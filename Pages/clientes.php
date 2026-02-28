<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD E INACTIVIDAD
$timeout = 600;
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
$_SESSION['ultima_actividad'] = time();

class Conexion
{
    protected $conexion;
    public function __construct()
    {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        $this->conexion->set_charset("utf8mb4");
    }
}

class Cliente extends Conexion
{
    private $id, $rif, $nombre, $telefono;
    public function setId($v)
    {
        $this->id = intval($v);
    }
    public function setRif($v)
    {
        $this->rif = substr(trim($v), 0, 12);
    }
    public function setNombre($v)
    {
        $this->nombre = substr(trim($v), 0, 40);
    }
    public function setTelefono($v)
    {
        $this->telefono = substr(trim($v), 0, 11);
    }

    public function listar()
    {
        return $this->conexion->query("SELECT * FROM clientes ORDER BY ID_cliente DESC");
    }
    public function insertar()
    {
        $stmt = $this->conexion->prepare("INSERT INTO clientes (RIF_cedula, nombre, profesional, telefono) VALUES (?, ?, 'Cliente', ?)");
        $stmt->bind_param("sss", $this->rif, $this->nombre, $this->telefono);
        return $stmt->execute();
    }
    public function modificar()
    {
        $stmt = $this->conexion->prepare("UPDATE clientes SET RIF_cedula=?, nombre=?, telefono=? WHERE ID_cliente=?");
        $stmt->bind_param("sssi", $this->rif, $this->nombre, $this->telefono, $this->id);
        return $stmt->execute();
    }
    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM clientes WHERE ID_cliente = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$clienteObj = new Cliente();
$msg_js = "";

// PROCESAMIENTO
if (isset($_POST['registrar']) || isset($_POST['editar'])) {
    // Unir RIF (Tipo + Número)
    $tipo_doc = $_POST['tipo_doc'] ?? 'V';
    $num_rif = preg_replace('/[^0-9]/', '', $_POST['RIF_cedula']);
    $rif_final = $tipo_doc . $num_rif;

    // Unir Teléfono (Operadora + Número)
    $operadora = $_POST['operadora'] ?? '0414';
    $num_telf = preg_replace('/[^0-9]/', '', $_POST['telefono_num']);
    $telf_final = $operadora . $num_telf;

    $nombre = trim($_POST['nombre']);

    if (strlen($num_rif) < 6) {
        $msg_js = "swalError('El documento es muy corto.');";
    } elseif (strlen($num_telf) != 7) {
        $msg_js = "swalError('El número de teléfono debe tener 7 dígitos después de la operadora.');";
    } else {
        $clienteObj->setRif($rif_final);
        $clienteObj->setNombre($nombre);
        $clienteObj->setTelefono($telf_final);

        if (isset($_POST['registrar'])) {
            $clienteObj->insertar();
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
        } else {
            $clienteObj->setId($_POST['ID_cliente']);
            $clienteObj->modificar();
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
        }
        exit();
    }
}

if (isset($_GET['delete'])) {
    $clienteObj->eliminar(intval($_GET['delete']));
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
    exit();
}
$result = $clienteObj->listar();
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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
        }

        .navbar-custom {
            background-color: #08082c;
        }

        .modal-header {
            background-color: #08082c;
            color: white;
        }

        .badge-tel {
            background: #e3f2fd;
            color: #0d47a1;
            font-weight: bold;
            border: 1px solid #bbdefb;
        }
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
            <span class="navbar-brand font-weight-bold">RUTA LARGA - CLIENTES</span>
            <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
        </div>
    </nav>

    <div class="container bg-white p-4 shadow rounded">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Panel de Clientes</h4>
            <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">+ Nuevo
                Cliente</button>
        </div>

        <table id="tablaClientes" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Cédula / RIF</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['RIF_cedula']) ?></td>
                        <td class="font-weight-bold"><?= htmlspecialchars($fila['nombre']) ?></td>
                        <td><span class="badge badge-tel p-2"><?= htmlspecialchars($fila['telefono']) ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm btnEditar" data-id="<?= $fila['ID_cliente'] ?>"
                                data-rif="<?= htmlspecialchars($fila['RIF_cedula']) ?>"
                                data-nombre="<?= htmlspecialchars($fila['nombre']) ?>"
                                data-tel="<?= htmlspecialchars($fila['telefono']) ?>" data-toggle="modal"
                                data-target="#modalEditar">Editar</button>
                            <button class="btn btn-danger btn-sm"
                                onclick="confirmarEliminar(<?= $fila['ID_cliente'] ?>, '<?= $fila['nombre'] ?>')">Borrar</button>
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
                    <div class="modal-header">
                        <h5>Registrar Cliente</h5>
                    </div>
                    <div class="modal-body p-4">
                        <div class="form-group">
                            <label>Identificación</label>
                            <div class="input-group">
                                <select name="tipo_doc" class="form-control col-3">
                                    <option value="V">V-</option>
                                    <option value="J">J-</option>
                                    <option value="E">E-</option>
                                    <option value="G">G-</option>
                                </select>
                                <input type="text" name="RIF_cedula" class="form-control" placeholder="Número" required>
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
                                    <option value="0212">0212</option>
                                    <option value="0412">0412</option>
                                    <option value="0422">0422</option>
                                    <option value="0416">0416</option>
                                    <option value="0426">0426</option>
                                </select>
                                <input type="text" name="telefono_num" class="form-control" placeholder="1234567"
                                    required maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="registrar"
                            class="btn btn-success btn-block">Guardar</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <form method="POST">
                    <div class="modal-header">
                        <h5>Editar Cliente</h5>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="ID_cliente" id="edit_id">
                        <div class="form-group">
                            <label>Documento</label>
                            <div class="input-group">
                                <select name="tipo_doc" id="edit_tipo" class="form-control col-3">
                                    <option value="V">V-</option>
                                    <option value="J">J-</option>
                                    <option value="E">E-</option>
                                    <option value="G">G-</option>
                                </select>
                                <input type="text" name="RIF_cedula" id="edit_rif_num" class="form-control" required>
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
                                    <option value="0212">0212</option>
                                    <option value="0412">0412</option>
                                    <option value="0422">0422</option>
                                    <option value="0416">0416</option>
                                    <option value="0426">0426</option>
                                </select>
                                <input type="text" name="telefono_num" id="edit_tel_num" class="form-control" required
                                    maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="editar"
                            class="btn btn-info btn-block">Actualizar</button></div>
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
        $(document).ready(function () {
            $('#tablaClientes').DataTable({ language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } });

            $('.btnEditar').on('click', function () {
                // Desglosar RIF
                let fullRif = $(this).data('rif');
                $('#edit_id').val($(this).data('id'));
                $('#edit_tipo').val(fullRif.charAt(0));
                $('#edit_rif_num').val(fullRif.substring(1));
                $('#edit_nombre').val($(this).data('nombre'));

                // Desglosar Teléfono
                let fullTel = $(this).data('tel').toString();
                $('#edit_operadora').val(fullTel.substring(0, 4));
                $('#edit_tel_num').val(fullTel.substring(4));
            });

            const status = new URLSearchParams(window.location.search).get('status');
            if (status === 'reg') Swal.fire({ icon: 'success', title: 'Registrado', showConfirmButton: false, timer: 1500 });
            if (status === 'edit') Swal.fire({ icon: 'info', title: 'Actualizado', showConfirmButton: false, timer: 1500 });
            if (status === 'del') Swal.fire({ icon: 'error', title: 'Eliminado', showConfirmButton: false, timer: 1500 });
            <?= $msg_js ?>
        });

        function confirmarEliminar(id, nombre) {
            Swal.fire({
                title: '¿Eliminar a ' + nombre + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, borrar'
            }).then((result) => { if (result.isConfirmed) window.location.href = `?delete=${id}`; });
        }
    </script>
</body>

</html>