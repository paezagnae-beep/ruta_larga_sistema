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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    <style>
        /* Forzamos Georgia en TODO el documento */
        * { font-family: 'Georgia', serif !important; }
        
        body { background-color: #f3f4f6; }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #08082c !important; 
            color: white !important; 
            border: none; 
            border-radius: 8px;
        }

        /* Estilo específico para los inputs del modal */
        input, textarea {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
        }
    </style>
</head>
<body class="text-gray-800">

<nav class="bg-[#08082c] text-white p-4 shadow-xl">
    <div class="container mx-auto flex justify-between items-center">
        <span class="text-xl font-bold italic tracking-wider">RUTA LARGA - INVENTARIO</span>
        <a href="menu.php" class="border border-white/30 px-4 py-1 rounded-lg hover:bg-white/10 transition text-sm">Menú Principal</a>
    </div>
</nav>

<div class="container mx-auto mt-8 px-4 pb-12">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold italic text-gray-700">Control de Repuestos</h2>
        <button onclick="openModal('modalRegistro')" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-lg transition transform hover:scale-105">
            + Nuevo Artículo
        </button>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-2xl border border-gray-100">
        <table id="tablaInventario" class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-400 uppercase text-xs tracking-widest border-b">
                    <th class="p-4">Código</th>
                    <th class="p-4">Producto</th>
                    <th class="p-4">Stock</th>
                    <th class="p-4">Precio Unit.</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php while ($fila = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 font-mono font-bold text-blue-900"><?= htmlspecialchars($fila['codigo']) ?></td>
                    <td class="p-4">
                        <div class="font-bold text-gray-800"><?= htmlspecialchars($fila['nombre']) ?></div>
                        <div class="text-xs text-gray-500 italic"><?= htmlspecialchars($fila['descripcion']) ?></div>
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full font-bold border <?= ($fila['cantidad'] > 5) ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100' ?>">
                            <?= $fila['cantidad'] ?> unid.
                        </span>
                    </td>
                    <td class="p-4 font-bold text-gray-700">$<?= number_format($fila['precio_unidad'], 2) ?></td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick='editarItem(<?= json_encode($fila) ?>)' class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition">
                                Editar
                            </button>
                            <button onclick="confirmarEliminar(<?= $fila['id_producto'] ?>, '<?= $fila['nombre'] ?>')" class="text-red-600 hover:bg-red-100 p-2 rounded-lg transition">
                                Borrar
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalRegistro" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="bg-[#08082c] p-6 text-white flex justify-between items-center">
            <h3 class="font-bold italic text-xl text-white uppercase">Registrar Producto</h3>
            <button onclick="closeModal('modalRegistro')" class="text-2xl">&times;</button>
        </div>
        <form method="POST" class="p-8 space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Nombre</label>
                <input type="text" name="nombre" class="w-full p-3 rounded-xl outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Código</label>
                    <input type="text" name="codigo" class="w-full p-3 rounded-xl outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Precio Unit.</label>
                    <input type="number" step="0.01" name="precio_unidad" class="w-full p-3 rounded-xl outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Descripción</label>
                <textarea name="descripcion" class="w-full p-3 rounded-xl outline-none" rows="2"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Stock Inicial</label>
                <input type="number" name="cantidad" class="w-full p-3 rounded-xl outline-none" required>
            </div>
            <button type="submit" name="registrar" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg uppercase tracking-widest mt-4 italic">Guardar en Base de Datos</button>
        </form>
    </div>
</div>

<div id="modalEditar" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="bg-[#08082c] p-6 text-white flex justify-between items-center">
            <h3 class="font-bold italic text-xl uppercase text-white">Editar Existencias</h3>
            <button onclick="closeModal('modalEditar')" class="text-2xl">&times;</button>
        </div>
        <form method="POST" class="p-8 space-y-4">
            <input type="hidden" name="id_producto" id="edit_id">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Producto</label>
                <input type="text" name="nombre" id="edit_nom" class="w-full p-3 rounded-xl outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Código</label>
                    <input type="text" name="codigo" id="edit_cod" class="w-full p-3 rounded-xl outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Precio Unit.</label>
                    <input type="number" step="0.01" name="precio_unidad" id="edit_pre" class="w-full p-3 rounded-xl outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Descripción</label>
                <textarea name="descripcion" id="edit_des" class="w-full p-3 rounded-xl outline-none" rows="2"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 italic">Cantidad Actual</label>
                <input type="number" name="cantidad" id="edit_can" class="w-full p-3 rounded-xl outline-none" required>
            </div>
            <button type="submit" name="editar" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg uppercase tracking-widest mt-4 italic">Actualizar Datos</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#tablaInventario').DataTable({ 
        language: { url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" } 
    });
    const status = new URLSearchParams(window.location.search).get('status');
    if(status) Swal.fire({icon:'success', title:'Operación Exitosa', showConfirmButton:false, timer:1500});
});

function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editarItem(data) {
    document.getElementById('edit_id').value = data.id_producto;
    document.getElementById('edit_cod').value = data.codigo;
    document.getElementById('edit_nom').value = data.nombre;
    document.getElementById('edit_des').value = data.descripcion;
    document.getElementById('edit_can').value = data.cantidad;
    document.getElementById('edit_pre').value = data.precio_unidad;
    openModal('modalEditar');
}

function confirmarEliminar(id, nombre) {
    Swal.fire({
        title: '¿Eliminar ' + nombre + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Sí, borrar'
    }).then((result) => { if (result.isConfirmed) window.location.href = `?delete=${id}`; });
}
</script>
</body>
</html>