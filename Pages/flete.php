<?php

class Conexion {
    protected $conexion;

    public function __construct() {
        $this->conexion = new mysqli("localhost", "root", "", "proyecto");

        if ($this->conexion->connect_error) {
            die("Error conexión: " . $this->conexion->connect_error);
        }

        $this->conexion->set_charset("utf8");
    }
}

class ReporteFlete extends Conexion {

    private $filtro;

    public function __construct($filtro = 'todo') {
        parent::__construct();
        $this->filtro = $filtro;
    }

    public function mostrar() {

        switch ($this->filtro) {

            case 'dia':
                $sql = "SELECT * FROM fletes 
                        WHERE fecha = CURDATE() 
                        ORDER BY fecha DESC";
                break;

            case 'semana':
                $sql = "SELECT * FROM fletes 
                        WHERE YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)
                        ORDER BY fecha DESC";
                break;

            case 'mes':
                $sql = "SELECT * FROM fletes 
                        WHERE MONTH(fecha) = MONTH(CURDATE()) 
                        AND YEAR(fecha) = YEAR(CURDATE())
                        ORDER BY fecha DESC";
                break;

            default:
                $sql = "SELECT * FROM fletes ORDER BY fecha DESC";
                break;
        }

        return $this->conexion->query($sql);
    }

    public function cambiarCancelado($id, $valor) {
        $nuevo = ($valor == 1) ? 0 : 1;
        $sql = "UPDATE fletes SET cancelado = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $nuevo, $id);
        return $stmt->execute();
    }
}

if (isset($_GET['id']) && isset($_GET['valor'])) {
    $tmp = new ReporteFlete();
    $tmp->cambiarCancelado($_GET['id'], $_GET['valor']);

    $filtro = $_GET['filtro'] ?? 'todo';
    header("Location: ".$_SERVER['PHP_SELF']."?filtro=$filtro");
    exit;
}


$filtro = $_GET['filtro'] ?? 'todo';
$reporte = new ReporteFlete($filtro);
$result = $reporte->mostrar();

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Fletes</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-7xl mx-auto p-6">

<header class="bg-white rounded-xl shadow p-6 flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Reporte de Fletes</h1>
    <a href="menu.php" class="text-blue-600 hover:underline">← Volver</a>
</header>

<div class="bg-white rounded-xl shadow p-5 mb-6 flex flex-wrap gap-3">

    <a href="?filtro=dia" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Hoy</a>
    <a href="?filtro=semana" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">Esta Semana</a>
    <a href="?filtro=mes" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Este Mes</a>
    <a href="?filtro=todo" class="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700">Todos</a>

</div>

<div class="bg-white rounded-xl shadow p-6 overflow-x-auto">

<table class="min-w-full border text-sm">
    <thead class="bg-gray-200">
        <tr>
            <th class="p-2 border">Fecha</th>
            <th class="p-2 border">Cliente</th>
            <th class="p-2 border">Origen</th>
            <th class="p-2 border">Destino</th>
            <th class="p-2 border">Valor</th>
            <th class="p-2 border">Cancelado</th>
        </tr>
    </thead>
    <tbody>

    <?php if($result && $result->num_rows > 0): ?>
        <?php while($fila = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
                <td class="p-2 border text-center"><?= $fila['fecha'] ?></td>
                <td class="p-2 border text-center"><?= $fila['id_cliente'] ?></td>
                <td class="p-2 border"><?= $fila['origen'] ?></td>
                <td class="p-2 border"><?= $fila['destino'] ?></td>
                <td class="p-2 border text-right">$<?= number_format($fila['valor'],2) ?></td>

                <td class="p-2 border text-center">
                    <a onclick="return confirm('¿Deseas cambiar el estado de cancelado?')"
                       href="?id=<?= $fila['id'] ?>&valor=<?= $fila['cancelado'] ?>&filtro=<?= $filtro ?>"
                       class="px-3 py-1 rounded-full text-xs font-semibold
                       <?= $fila['cancelado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <?= $fila['cancelado'] == 1 ? 'SI' : 'NO' ?>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="text-center p-4 text-gray-500">
                No hay registros
            </td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

</div>

</div>

</body>
</html>