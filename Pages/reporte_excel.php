<?php
// 1. SEGURIDAD
session_start();
if (!isset($_SESSION["usuario"])) {
    exit("Acceso denegado");
}

// 2. CONEXIÓN
$mysqli = new mysqli("localhost", "root", "", "proyecto");
$mysqli->set_charset("utf8");

// 3. DETERMINAR TABLA Y NOMBRE DE ARCHIVO
$tipo = $_GET['tipo'] ?? 'fletes';
$nombre_archivo = "Reporte_" . ucfirst($tipo) . "_" . date('Ymd') . ".csv";

// 4. CABECERAS PARA DESCARGA DE CSV (Sin errores de seguridad)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '";');

// Crear un puntero de salida para PHP
$salida = fopen('php://output', 'w');

// Añadir el BOM para que Excel reconozca los acentos correctamente en Windows
fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

// 5. CONFIGURAR CONSULTA SEGÚN EL TIPO
switch($tipo) {
    case 'inventario':
        $sql = "SELECT codigo AS 'CODIGO', nombre AS 'PRODUCTO', cantidad AS 'STOCK', precio_unidad AS 'PRECIO' FROM inventario";
        break;
    case 'clientes':
        $sql = "SELECT RIF_cedula AS 'IDENTIFICACION', nombre AS 'NOMBRE', telefono AS 'TELEFONO' FROM clientes";
        break;
    default:
        $sql = "SELECT * FROM fletes";
        break;
}

$resultado = $mysqli->query($sql);

if ($resultado) {
    // 6. INSERTAR ENCABEZADOS DE COLUMNA
    $columnas = [];
    $info_campos = $resultado->fetch_fields();
    foreach ($info_campos as $campo) {
        $columnas[] = $campo->name;
    }
    fputcsv($salida, $columnas, ";"); // Usamos punto y coma para mejor compatibilidad con Excel en español

    // 7. INSERTAR DATOS
    while ($fila = $resultado->fetch_assoc()) {
        fputcsv($salida, $fila, ";");
    }
}

fclose($salida);
exit;