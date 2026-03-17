<?php
require_once dirname(__DIR__) . "/model/inventarioModel.php";

class InventarioController {
    private $model;

    public function __construct() {
        $this->model = new Inventario();
    }

    public function manejarPeticiones() {
        // 1. Lógica para Procesar Movimientos (Suma/Resta)
        if (isset($_POST['movimiento'])) {
            $id = intval($_POST['id_mov']);
            $cantidad_cambio = intval($_POST['cant_mov']);
            $tipo = $_POST['tipo_mov']; // 'entrada' o 'salida'
            $nombre_prod = $_POST['nom_mov'];

            // Obtenemos los datos actuales del producto desde el modelo
            $producto = $this->model->obtenerPorId($id);
            
            if ($producto) {
                $cantidad_actual = intval($producto['cantidad']);

                // Calculamos la nueva cantidad basándonos en el tipo de movimiento
                if ($tipo === 'entrada') {
                    $nueva_cantidad = $cantidad_actual + $cantidad_cambio;
                } else {
                    $nueva_cantidad = $cantidad_actual - $cantidad_cambio;
                    // Seguridad: No permitir que baje de cero
                    if ($nueva_cantidad < 0) $nueva_cantidad = 0;
                }

                // Guardamos el cambio usando el método actualizado del modelo
                if ($this->model->actualizarCantidad($id, $nueva_cantidad)) {
                    // Redireccionamos enviando el estado y el nombre del producto para la alerta
                    header("Location: inventarioView.php?estado=mov&p=" . urlencode($nombre_prod));
                    exit();
                }
            }
        }

        // 2. Lógica para Eliminar (si la necesitas procesar aquí)
        if (isset($_GET['eliminar'])) {
            $id_el = intval($_GET['eliminar']);
            // Supongamos que tienes un método eliminar en tu modelo
            // $this->model->eliminar($id_el);
            header("Location: inventarioView.php?estado=del");
            exit();
        }

        // 3. Retornamos los datos necesarios para llenar la vista
        return [
            'result' => $this->model->listar(),
            'vehiculos' => $this->model->obtenerVehiculos()
        ];
    }
}