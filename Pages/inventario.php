<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD E INACTIVIDAD
$timeout = 600;
if (!isset($_SESSION["usuario"])) { header("Location: login.php"); exit(); }
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > $timeout)) {
    session_unset(); session_destroy();
    header("Location: login.php?mensaje=sesion_caducada");
    exit();
}
$_SESSION['ultima_actividad'] = time();

// 2. CLASES DE CONEXIÓN E INVENTARIO
class Conexion {
    protected $conexion;
    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        $this->conexion->set_charset("utf8mb4");
        if ($this->conexion->connect_error) { die("Error: " . $this->conexion->connect_error); }
    }
}

class Inventario extends Conexion {
    public function listar() {
        return $this->conexion->query("SELECT * FROM inventario ORDER BY id_producto DESC");
    }

    public function insertar($cod, $nom, $des, $can, $pre) {
        $stmt = $this->conexion->prepare("INSERT INTO inventario (codigo, nombre, descripcion, cantidad, precio_unidad, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssid", $cod, $nom, $des, $can, $pre);
        return $stmt->execute();
    }

    public function modificar($id, $cod, $nom, $des, $can, $pre) {
        $stmt = $this->conexion->prepare("UPDATE inventario SET codigo=?, nombre=?, descripcion=?, cantidad=?, precio_unidad=?, fecha_actualizacion=NOW() WHERE id_producto=?");
        $stmt->bind_param("sssidi", $cod, $nom, $des, $can, $pre, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM inventario WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$objInv = new Inventario();

// 3. PROCESAMIENTO
if (isset($_POST['registrar'])) {
    $objInv->insertar($_POST['codigo'], $_POST['nombre'], $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_unidad']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg"); exit();
}

if (isset($_POST['editar'])) {
    $objInv->modificar($_POST['id_producto'], $_POST['codigo'], $_POST['nombre'], $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_unidad']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit"); exit();
}

if (isset($_GET['delete'])) {
    $objInv->eliminar(intval($_GET['delete']));
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=del"); exit();
}

$result = $objInv->listar();
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
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; }
    .navbar-custom { background-color: #08082c; }
    .modal-header { background-color: #08082c; color: white; }
    .badge-rif { background: #e8f5e9; color: #2e7d32; font-weight: bold; border: 1px solid #c8e6c9; }
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
        <span class="navbar-brand font-weight-bold">RUTA LARGA - INVENTARIO</span>
        <a href="menu.php" class="btn btn-outline-light btn-sm">Menú Principal</a>
    </div>
</nav>

<div class="container bg-white p-4 shadow rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Control de Repuestos</h4>
        <button class="btn btn-success px-4" data-toggle="modal" data-target="#modalRegistro">+ Nuevo Artículo</button>
    </div>

    <table id="tablaInventario" class="table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto / Descripción</th>
                <th>Stock</th>
                <th>Precio Unit.</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $result->fetch_assoc()): ?>
            <tr>
                <td class="font-weight-bold text-primary"><?= htmlspecialchars($fila['codigo']) ?></td>
                <td>
                    <div class="font-weight-bold"><?= htmlspecialchars($fila['nombre']) ?></div>
                    <small class="text-muted italic"><?= htmlspecialchars($fila['descripcion']) ?></small>
                </td>
                <td>
                    <?php if($fila['cantidad'] > 5): ?>
                        <span class="badge badge-pill badge-success p-2">In Stock: <?= $fila['cantidad'] ?></span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-danger p-2">Bajo: <?= $fila['cantidad'] ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-precio">$<?= number_format($fila['precio_unidad'], 2) ?></td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm btnEditar" 
                            data-id="<?= $fila['id_producto'] ?>"
                            data-cod="<?= htmlspecialchars($fila['codigo']) ?>"
                            data-nom="<?= htmlspecialchars($fila['nombre']) ?>"
                            data-des="<?= htmlspecialchars($fila['descripcion']) ?>"
                            data-can="<?= $fila['cantidad'] ?>"
                            data-pre="<?= $fila['precio_unidad'] ?>"
                            data-toggle="modal" data-target="#modalEditar">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?= $fila['id_producto'] ?>, '<?= $fila['nombre'] ?>')">Borrar</button>
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
                <div class="modal-header"><h5>Registrar Producto</h5></div>
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label>Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Código</label>
                            <input type="text" name="codigo" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Precio Unitario ($)</label>
                            <input type="number" step="0.01" name="precio_unidad" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Stock Inicial</label>
                        <input type="number" name="cantidad" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="registrar" class="btn btn-success btn-block">Guardar en Base de Datos</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <form method="POST">
                <div class="modal-header"><h5>Editar Existencias</h5></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id_producto" id="edit_id">
                    <div class="form-group">
                        <label>Producto</label>
                        <input type="text" name="nombre" id="edit_nom" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Código</label>
                            <input type="text" name="codigo" id="edit_cod" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Precio Unitario</label>
                            <input type="number" step="0.01" name="precio_unidad" id="edit_pre" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" id="edit_des" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Cantidad Actual</label>
                        <input type="number" name="cantidad" id="edit_can" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="editar" class="btn btn-info btn-block">Actualizar Datos</button></div>
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
    $('#tablaInventario').DataTable({ 
        language: { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } 
    });

    $('.btnEditar').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_cod').val($(this).data('cod'));
        $('#edit_nom').val($(this).data('nom'));
        $('#edit_des').val($(this).data('des'));
        $('#edit_can').val($(this).data('can'));
        $('#edit_pre').val($(this).data('pre'));
    });

    const status = new URLSearchParams(window.location.search).get('status');
    if(status === 'reg') Swal.fire({icon:'success', title:'Registrado', showConfirmButton:false, timer:1500});
    if(status === 'edit') Swal.fire({icon:'info', title:'Actualizado', showConfirmButton:false, timer:1500});
    if(status === 'del') Swal.fire({icon:'error', title:'Eliminado', showConfirmButton:false, timer:1500});
});

function confirmarEliminar(id, nombre) {
    Swal.fire({
        title: '¿Eliminar ' + nombre + '?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, borrar'
    }).then((result) => { if (result.isConfirmed) window.location.href = `?delete=${id}`; });
}
</script>
</body>
</html>