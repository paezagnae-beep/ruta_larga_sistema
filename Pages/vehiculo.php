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

// 2. CONEXIÓN Y CLASE VEHÍCULO
class Conexion
{
    protected $conexion;
    public function __construct()
    {
        // Verifica que estos datos sean correctos para tu servidor local
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        $this->conexion->set_charset("utf8mb4");
    }
}

class Vehiculo extends Conexion
{
    private $id, $placa, $modelo, $marca;

    public function setId($v) { $this->id = intval($v); }
    public function setPlaca($v) { $this->placa = strtoupper(substr(trim($v), 0, 15)); }
    public function setModelo($v) { $this->modelo = substr(trim($v), 0, 50); }
    public function setMarca($v) { $this->marca = substr(trim($v), 0, 50); }

    public function listar()
    {
        // Usamos id_vehiculo en minúsculas como en tu SQL
        return $this->conexion->query("SELECT * FROM vehiculos ORDER BY id_vehiculo DESC");
    }

    public function insertar()
    {
        // Se añade cliente_id con valor 0 por defecto para evitar error de NOT NULL
        $stmt = $this->conexion->prepare("INSERT INTO vehiculos (placa, modelo, marca, cliente_id) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $this->placa, $this->modelo, $this->marca);
        return $stmt->execute();
    }

    public function modificar()
    {
        // Corregido: id_vehiculo en minúsculas
        $stmt = $this->conexion->prepare("UPDATE vehiculos SET placa=?, modelo=?, marca=? WHERE id_vehiculo=?");
        $stmt->bind_param("sssi", $this->placa, $this->modelo, $this->marca, $this->id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        // Corregido: id_vehiculo en minúsculas
        $stmt = $this->conexion->prepare("DELETE FROM vehiculos WHERE id_vehiculo = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$vehiculoObj = new Vehiculo();

// 3. PROCESAMIENTO DE ACCIONES (Antes de cualquier HTML)

// ACCIÓN: ELIMINAR
if (isset($_GET['delete'])) {
    $id_borrar = intval($_GET['delete']);
    if ($id_borrar > 0) {
        $vehiculoObj->eliminar($id_borrar);
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
        exit();
    }
}

// ACCIÓN: REGISTRAR O EDITAR
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehiculoObj->setPlaca($_POST['placa']);
    $vehiculoObj->setModelo($_POST['modelo']);
    $vehiculoObj->setMarca($_POST['marca']);

    if (isset($_POST['registrar'])) {
        $vehiculoObj->insertar();
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
        exit();
    } 
    
    if (isset($_POST['editar'])) {
        $vehiculoObj->setId($_POST['id_vehiculo_post']); // Usamos un nombre de campo claro
        $vehiculoObj->modificar();
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
        exit();
    }
}

// Obtener resultados para la tabla
$result = $vehiculoObj->listar();
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
        body {
            font-family: Georgia, serif;
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('../assets/img/fondo.jpg');
            background-size: cover; background-attachment: fixed;
        }
        .navbar-custom { background-color: #08082c; }
        .modal-header { background-color: #08082c; color: white; }
        .placa-badge { background: #fff3e0; color: #e65100; font-weight: bold; border: 1px solid #ffe0b2; font-family: monospace; }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(5px); }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-custom mb-4 shadow">
        <div class="container">
            <span class="navbar-brand font-weight-bold">RUTA LARGA - VEHÍCULOS</span>
            <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
        </div>
    </nav>

    <div class="container glass-card p-4 shadow rounded">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Listado de Flota</h4>
            <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">+ Nuevo Vehículo</button>
        </div>

        <table id="tablaVehiculos" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge placa-badge p-2 text-uppercase"><?= htmlspecialchars($fila['placa']) ?></span></td>
                        <td class="font-weight-bold"><?= htmlspecialchars($fila['marca']) ?></td>
                        <td><?= htmlspecialchars($fila['modelo']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm btnEditar" 
                                data-id="<?= $fila['id_vehiculo'] ?>"
                                data-placa="<?= htmlspecialchars($fila['placa']) ?>"
                                data-marca="<?= htmlspecialchars($fila['marca']) ?>"
                                data-modelo="<?= htmlspecialchars($fila['modelo']) ?>">Editar</button>

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
            <div class="modal-content border-0">
                <form method="POST">
                    <div class="modal-header"><h5>Registrar Vehículo</h5></div>
                    <div class="modal-body p-4">
                        <div class="form-group"><label>Placa</label><input type="text" name="placa" class="form-control text-uppercase" required></div>
                        <div class="form-group">
                            <label>Marca</label>
                            <select name="marca" class="form-control" required>
                                <option value="Chevrolet">Chevrolet</option>
                                <option value="Iveco">Iveco</option>
                                <option value="Ford">Ford</option>
                                <option value="Mack">Mack</option>
                                <option value="Kenworth">Kenworth</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Modelo</label><input type="text" name="modelo" class="form-control" required></div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="registrar" class="btn btn-success btn-block">Guardar Vehículo</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0">
                <form method="POST">
                    <div class="modal-header"><h5>Editar Vehículo</h5></div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_vehiculo_post" id="edit_id">
                        
                        <div class="form-group"><label>Placa</label><input type="text" name="placa" id="edit_placa" class="form-control text-uppercase" required></div>
                        <div class="form-group">
                            <label>Marca</label>
                            <select name="marca" id="edit_marca" class="form-control" required>
                                <option value="Chevrolet">Chevrolet</option>
                                <option value="Iveco">Iveco</option>
                                <option value="Ford">Ford</option>
                                <option value="Mack">Mack</option>
                                <option value="Kenworth">Kenworth</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Modelo</label><input type="text" name="modelo" id="edit_modelo" class="form-control" required></div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="editar" class="btn btn-info btn-block">Actualizar Cambios</button></div>
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
            var table = $('#tablaVehiculos').DataTable({ 
                language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } 
            });

            // Delegación de eventos para el botón editar
            $('#tablaVehiculos').on('click', '.btnEditar', function () {
                const id = $(this).data('id');
                const placa = $(this).data('placa');
                const marca = $(this).data('marca');
                const modelo = $(this).data('modelo');

                $('#edit_id').val(id);
                $('#edit_placa').val(placa);
                $('#edit_marca').val(marca);
                $('#edit_modelo').val(modelo);
                
                $('#modalEditar').modal('show');
            });

            // Mostrar alertas según el status de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            if (status === 'reg') Swal.fire({ icon: 'success', title: 'Vehículo Registrado', showConfirmButton: false, timer: 1500 });
            if (status === 'edit') Swal.fire({ icon: 'info', title: 'Vehículo Actualizado', showConfirmButton: false, timer: 1500 });
            if (status === 'del') Swal.fire({ icon: 'error', title: 'Vehículo Eliminado', showConfirmButton: false, timer: 1500 });
        });

        function confirmarEliminar(id, placa) {
            Swal.fire({
                title: '¿Eliminar vehículo ' + placa + '?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirección para ejecutar el borrado en PHP
                    window.location.href = window.location.pathname + "?delete=" + id;
                }
            });
        }
    </script>
</body>
</html>