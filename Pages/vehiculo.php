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

class Vehiculos extends Conexion {

    private $id;
    private $placa;
    private $marca;
    private $modelo;


    public function __construct() {
        parent::__construct();
    }

    public function getId() { return $this->id; }
    public function getPlaca() { return $this->placa; }
    public function getMarca() { return $this->marca; }
    public function getModelo() { return $this->modelo; }

    public function setId($v) { $this->id = $v; }
    public function setPlaca($v) { $this->placa = $v; }
    public function setMarca($v) { $this->marca = $v; }
    public function setModelo($v) { $this->modelo = $v; }

    public function mostrar() {
        return $this->placa . " - " . $this->marca . " - " . $this->modelo;
    }
    
    public function listar() {
        return $this->conexion->query("SELECT * FROM vehiculos");
    }
}

$vehiculo = new Vehiculos();
$result = $vehiculo->listar();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vehículos</title>

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
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($fila = $result->fetch_assoc()): ?>

            <tr>
                <td><?= $fila['ID_vehiculo'] ?></td>
                <td><?= $fila['placa'] ?></td>
                <td><?= $fila['marca'] ?></td>
                <td><?= $fila['modelo'] ?></td>
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