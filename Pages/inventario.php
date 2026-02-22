<?php
class Conexion {

    private $servidor = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $bd = "proyecto";

    protected $conexion;

    public function __construct() {
        $this->conectar();
    }

    protected function conectar() {
        $this->conexion = new mysqli(
            $this->servidor,
            $this->usuario,
            $this->clave,
            $this->bd
        );

        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }
}

class Inventario extends Conexion {

    private $id;
    private $codigo;
    private $nombre;
    private $descripcion;
    private $cantidad;
    private $precio;
    private $fecha_actualizacion;

    public function __construct() {
        parent::__construct();
    }

    // GET
    public function getId() { return $this->id; }
    public function getCodigo() { return $this->codigo; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getCantidad() { return $this->cantidad; }
    public function getPrecio() { return $this->precio; }
    public function getFechaActualizacion() { return $this->fecha_actualizacion; }

    // SET
    public function setId($v) { $this->id = $v; }
    public function setCodigo($v) { $this->codigo = $v; }
    public function setNombre($v) { $this->nombre = $v; }
    public function setDescripcion($v) { $this->descripcion = $v; }
    public function setCantidad($v) { $this->cantidad = $v; }
    public function setPrecio($v) { $this->precio = $v; }
    public function setFechaActualizacion($v) { $this->fecha_actualizacion = $v; }

    // MOSTRAR
    public function mostrar() {
        return $this->codigo . " - " . $this->nombre . " - " . $this->descripcion;
    }

    // LISTAR
    public function listar() {
        return $this->conexion->query("SELECT * FROM inventario");
    }
}

$inventario = new Inventario();
$result = $inventario->listar();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Inventario</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
</head>

<body>

<div class="container mt-4">

    <h3 class="text-center mb-4">Listado de Choferes</h3>
    <a href="menu.php" class="text-blue-600 hover:underline">← Volver al Menú</a>

    <table id="example" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Fecha de Actualización</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($fila = $result->fetch_assoc()): ?>

            <tr>
                <td><?= $fila['ID_inventario'] ?></td>
                <td><?= $fila['codigo'] ?></td>
                <td><?= $fila['nombre'] ?></td>
                <td><?= $fila['descripcion'] ?></td>
                <td><?= $fila['cantidad'] ?></td>
                <td><?= $fila['precio'] ?></td>
                <td><?= $fila['fecha_actualizacion'] ?></td>
            </tr>

        <?php endwhile; ?>

        </tbody>
    </table>

</div>

<script>
$(document).ready(function() {
    $('#example').DataTable({
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "No hay datos disponibles",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    });
});
</script>

</body>
</html>