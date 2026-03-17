<?php
// Ajusta la ruta según donde tengas tu archivo de conexión
require_once dirname(__DIR__) . "/config/claseconexion.php";

class EstadisticasModel
{
    private $db;

    public function __construct()
    {
        /** * Usamos el método estático de tu clase conexión.
         * Si tu clase se llama distinto (ej: Database), cámbialo aquí.
         */
        $c = new Conexion();
        $this->db = $c->conectar();

        if ($this->db->connect_error) {
            die("Error de conexión en el Modelo: " . $this->db->connect_error);
        }
    }

    public function getKPIs()
    {
        return [
            'totalFletes' => $this->qCount("SELECT COUNT(*) as total FROM fletes"),
            'totalClientes' => $this->qCount("SELECT COUNT(*) as total FROM clientes"),
            'alertasStock' => $this->qCount("SELECT COUNT(*) as total FROM inventario WHERE cantidad <= 5")
        ];
    }

    public function getDatosGraficoAnual()
    {
        $datos = array_fill(0, 12, 0);
        $sql = "SELECT MONTH(fecha) as mes, COUNT(*) as total 
                FROM fletes 
                WHERE YEAR(fecha) = YEAR(CURDATE()) 
                GROUP BY MONTH(fecha)";

        $res = $this->db->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $indice = (int) $row['mes'] - 1;
                $datos[$indice] = (int) $row['total'];
            }
        }
        return $datos;
    }

    private function qCount($sql)
    {
        try {
            $res = $this->db->query($sql);
            if ($res) {
                $f = $res->fetch_assoc();
                return $f['total'] ?? 0;
            }
        } catch (Exception $e) {
            return 0;
        }
        return 0;
    }
}
