<?php
require_once dirname(__DIR__) . "/model/reporteModel.php";

class ReporteController {
    
    public function generarDatosExcel() {
        $reporteObj = new Reporte();
        $tipo = $_GET['tipo_reporte'] ?? 'todo';
        $f_inicio = $_GET['f_inicio'] ?? '';
        $f_fin = $_GET['f_fin'] ?? '';

        return [
            'tipo'      => $tipo,
            'fletes'    => ($tipo == 'todo' || $tipo == 'fletes') ? $reporteObj->obtenerFletesReporte($f_inicio, $f_fin) : null,
            'clientes'  => ($tipo == 'todo' || $tipo == 'clientes') ? $reporteObj->obtenerTodosClientes() : null,
            'choferes'  => ($tipo == 'todo' || $tipo == 'choferes') ? $reporteObj->obtenerTodosChoferes() : null,
            'vehiculos' => ($tipo == 'todo' || $tipo == 'inventario') ? $reporteObj->obtenerTodosVehiculos() : null
        ];
    }
}