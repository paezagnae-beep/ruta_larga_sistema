<?php
require_once dirname(__DIR__) . "/config/claseconexion.php";

class Reporte extends Conexion {
    
    public function obtenerFletesReporte($f_inicio, $f_fin) {
        $this->conectar();
        $con = $this->conexion; 
        
        $sql = "SELECT f.*, c.nombre AS cliente_nom, ch.nombre AS chofer_nom, v.placa AS vehiculo_placa
                FROM fletes f
                LEFT JOIN clientes c ON f.id_cliente = c.ID_cliente
                LEFT JOIN choferes ch ON f.id_chofer = ch.ID_chofer
                LEFT JOIN vehiculos v ON f.id_vehiculo = v.id_vehiculo
                WHERE 1=1";

        if (!empty($f_inicio) && !empty($f_fin)) {
            $sql .= " AND f.fecha BETWEEN '$f_inicio' AND '$f_fin'";
        }

        $sql .= " ORDER BY f.fecha DESC";
        return $con->query($sql);
    }

    public function obtenerTodosClientes() {
        $this->conectar();
        return $this->conexion->query("SELECT * FROM clientes ORDER BY nombre ASC");
    }

    public function obtenerTodosChoferes() {
        $this->conectar();
        return $this->conexion->query("SELECT * FROM choferes ORDER BY nombre ASC");
    }

    public function obtenerTodosVehiculos() {
        $this->conectar();
        return $this->conexion->query("SELECT * FROM vehiculos ORDER BY placa ASC");
    }
}