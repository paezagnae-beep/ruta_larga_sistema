<?php
require_once dirname(__DIR__) . "/model/estadisticasModel.php";

class estadisticasController {
    private $modelo;

    public function __construct($modelo) {
        // Inyectamos la conexión al modelo
        $this->modelo = $modelo;
    }

    public function getEstadisticas() {
        return [
            'kpis' => $this->modelo->getKPIs(),
            'datosGrafico' => $this->modelo->getDatosGraficoAnual(),
            'mesesLabels' => ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
        ];
    }
}

