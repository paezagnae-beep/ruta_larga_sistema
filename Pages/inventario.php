<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. SEGURIDAD E INACTIVIDAD
$tiempo_espera = 600;
if (!isset($_SESSION["usuario"])) { header("Location: login.php"); exit(); }
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > $tiempo_espera)) {
    session_unset(); session_destroy();
    header("Location: login.php?mensaje=sesion_caducada");
    exit();
}
$_SESSION['ultima_actividad'] = time();

// 2. CLASES
class Conexion {
    protected $conexion;
    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");
        $this->conexion->set_charset("utf8mb4");
        if ($this->conexion->connect_error) { die("Error: " . $this->conexion->connect_error); }
    }
}

class Inventario extends Conexion {
    public function listarProductos() {
        return $this->conexion->query("SELECT * FROM inventario ORDER BY id_producto DESC");
    }

    public function insertarProducto($codigo, $nombre, $descripcion, $cantidad, $precio) {
        $sentencia = $this->conexion->prepare("INSERT INTO inventario (codigo, nombre, descripcion, cantidad, precio_unidad, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, NOW())");
        $sentencia->bind_param("sssid", $codigo, $nombre, $descripcion, $cantidad, $precio);
        return $sentencia->execute();
    }

    public function modificarProducto($id, $codigo, $nombre, $descripcion, $cantidad, $precio) {
        $sentencia = $this->conexion->prepare("UPDATE inventario SET codigo=?, nombre=?, descripcion=?, cantidad=?, precio_unidad=?, fecha_actualizacion=NOW() WHERE id_producto=?");
        $sentencia->bind_param("sssidi", $codigo, $nombre, $descripcion, $cantidad, $precio, $id);
        return $sentencia->execute();
    }

    public function procesarMovimiento($id, $cantidad, $tipo) {
        $operador = ($tipo == 'entrada') ? "+" : "-";
        $sql = "UPDATE inventario SET cantidad = cantidad $operador ?, fecha_actualizacion=NOW() WHERE id_producto = ?";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("ii", $cantidad, $id);
        return $sentencia->execute();
    }

    public function eliminarProducto($id) {
        $sentencia = $this->conexion->prepare("DELETE FROM inventario WHERE id_producto = ?");
        $sentencia->bind_param("i", $id);
        return $sentencia->execute();
    }
}

$objetoInventario = new Inventario();

// 3. PROCESAMIENTO DE ACCIONES
if (isset($_POST['registrar'])) {
    $codigo = trim($_POST['codigo']);
    $nombre = trim($_POST['nombre']);
    $objetoInventario->insertarProducto($codigo, $nombre, $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_unidad']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?estado=reg&nom=" . urlencode($nombre)); 
    exit();
}

if (isset($_POST['editar'])) {
    $nombre = trim($_POST['nombre']);
    $objetoInventario->modificarProducto($_POST['id_producto'], $_POST['codigo'], $nombre, $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_unidad']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?estado=edit&nom=" . urlencode($nombre)); 
    exit();
}

if (isset($_POST['movimiento'])) {
    $id = $_POST['id_mov'];
    $cantidad = $_POST['cant_mov'];
    $tipo = $_POST['tipo_mov'];
    $nombre = $_POST['nom_mov']; 
    $objetoInventario->procesarMovimiento($id, $cantidad, $tipo);
    header("Location: " . $_SERVER['PHP_SELF'] . "?estado=mov&t=$tipo&p=" . urlencode($nombre) . "&n=$cantidad");
    exit();
}

if (isset($_GET['eliminar'])) {
    $objetoInventario->eliminarProducto(intval($_GET['eliminar']));
    header("Location: " . $_SERVER['PHP_SELF'] . "?estado=del"); 
    exit();
}

$resultado = $objetoInventario->listarProductos();
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
        body { 
            font-family: Georgia, serif; 
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/fondo.jpg');
            background-size: cover; background-attachment: fixed;
        }
        .navbar-custom { background-color: #08082c; }
        .modal-header { background-color: #08082c; color: white; }
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
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
            <tr>
                <td class="font-weight-bold text-primary"><?= htmlspecialchars($fila['codigo']) ?></td>
                
                <td>
                    <strong><?= htmlspecialchars($fila['nombre']) ?></strong>
                </td>
                
                <td>
                    <span class="badge badge-pill <?= ($fila['cantidad'] > 5) ? 'badge-success' : 'badge-danger' ?> p-2">
                        <?= $fila['cantidad'] ?> unid.
                    </span>
                </td>
                <td>$<?= number_format($fila['precio_unidad'], 2) ?></td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm btnMovimiento" 
                            data-id="<?= $fila['id_producto'] ?>"
                            data-nom="<?= htmlspecialchars($fila['nombre']) ?>"
                            title="Entrada/Salida">⇅</button>
                    
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
                <div class="modal-header"><h5>Registrar Producto</h5></div>
                <div class="modal-body p-4">
                    <div class="form-group"><label>Nombre del Producto</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="form-row">
                        <div class="form-group col-md-6"><label>Código</label><input type="text" name="codigo" class="form-control" required></div>
                        <div class="form-group col-md-6"><label>Precio ($)</label><input type="number" step="0.01" name="precio_unidad" class="form-control" required></div>
                    </div>
                    <div class="form-group"><label>Descripción</label><textarea name="descripcion" class="form-control" rows="2"></textarea></div>
                    <div class="form-group"><label>Cantidad</label><input type="number" name="cantidad" class="form-control" required></div>
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
                <div class="modal-header"><h5>Editar Producto</h5></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id_producto" id="edit_id">
                    <div class="form-group"><label>Nombre</label><input type="text" name="nombre" id="edit_nom" class="form-control" required></div>
                    <div class="form-row">
                        <div class="form-group col-md-6"><label>Código</label><input type="text" name="codigo" id="edit_cod" class="form-control" required></div>
                        <div class="form-group col-md-6"><label>Precio</label><input type="number" step="0.01" name="precio_unidad" id="edit_pre" class="form-control" required></div>
                    </div>
                    <div class="form-group"><label>Descripción</label><textarea name="descripcion" id="edit_des" class="form-control" rows="2"></textarea></div>
                    <div class="form-group"><label>Cantidad Actual</label><input type="number" name="cantidad" id="edit_can" class="form-control" required></div>
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
                <div class="modal-header bg-warning"><h5 class="text-dark">Movimiento de Stock</h5></div>
                <div class="modal-body p-4 text-center">
                    <input type="hidden" name="id_mov" id="mov_id">
                    <input type="hidden" name="nom_mov" id="mov_nom_hidden">
                    <p><strong id="mov_nom_display"></strong></p>
                    <div class="form-group">
                        <select name="tipo_mov" class="form-control" required>
                            <option value="entrada">📥 Entrada (+)</option>
                            <option value="salida">📤 Salida (-)</option>
                        </select>
                    </div>
                    <div class="form-group"><input type="number" name="cant_mov" class="form-control" min="1" value="1" required></div>
                </div>
                <div class="modal-footer"><button type="submit" name="movimiento" class="btn btn-warning btn-block">Procesar</button></div>
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

    // Llenar Editar
    $('#tablaInventario').on('click', '.btnEditar', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_cod').val($(this).data('cod'));
        $('#edit_nom').val($(this).data('nom'));
        $('#edit_des').val($(this).data('des'));
        $('#edit_can').val($(this).data('can'));
        $('#edit_pre').val($(this).data('pre'));
        $('#modalEditar').modal('show');
    });

    // Llenar Movimiento
    $('#tablaInventario').on('click', '.btnMovimiento', function() {
        $('#mov_id').val($(this).data('id'));
        $('#mov_nom_hidden').val($(this).data('nom'));
        $('#mov_nom_display').text($(this).data('nom'));
        $('#modalMov').modal('show');
    });

    const parametros = new URLSearchParams(window.location.search);
    if (parametros.get('estado') === 'reg') Swal.fire('Éxito', 'Registrado: ' + parametros.get('nom'), 'success');
    if (parametros.get('estado') === 'edit') Swal.fire('Éxito', 'Actualizado: ' + parametros.get('nom'), 'info');
    if (parametros.get('estado') === 'mov') Swal.fire('Éxito', 'Movimiento para ' + parametros.get('p'), 'success');
});

function confirmarEliminar(id, nombre) {
    Swal.fire({
        title: '¿Eliminar ' + nombre + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Borrar'
    }).then((resultado) => { if (resultado.isConfirmed) window.location.href = `?eliminar=${id}`; });
}
</script>
</body>
</html>