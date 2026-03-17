<?php
require_once dirname(__DIR__) . "/model/vehiculoModel.php";

class VehiculoController
{
    public function manejarPeticiones()
    {
        $vehiculoObj = new Vehiculo();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Procesar la lógica del modelo (Select vs Input Manual)
            $modeloFinal = $_POST['modelo'];
            
            // Si seleccionó "Otros" en el select, tomamos el valor del campo de texto
            if ($modeloFinal === "Otros" && !empty($_POST['modelo_otro'])) {
                $modeloFinal = $_POST['modelo_otro'];
            }

            // 2. Capturar la fecha de registro
            // Si por algún motivo no llega, usamos la fecha actual por defecto
            $fechaRegistro = $_POST['fecha_registro'] ?? date('Y-m-d');

            // 3. Cargar los datos en el objeto modelo
            $vehiculoObj->setPlaca($_POST['placa']);
            $vehiculoObj->setMarca($_POST['marca']);
            $vehiculoObj->setModelo($modeloFinal);
            $vehiculoObj->setFecha($fechaRegistro); // Asignamos la fecha al objeto

            // Acción: Registrar
            if (isset($_POST['registrar'])) {
                if ($vehiculoObj->insertar()) {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
                    exit();
                }
            }

            // Acción: Editar
            if (isset($_POST['editar'])) {
                // Aseguramos que el ID se asigne correctamente antes de modificar
                $vehiculoObj->setId($_POST['id_vehiculo_post']);
                if ($vehiculoObj->modificar()) {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
                    exit();
                }
            }
        }

        // Acción: Eliminar (vía GET)
        if (isset($_GET['delete'])) {
            $id_borrar = intval($_GET['delete']);
            if ($id_borrar > 0) {
                $vehiculoObj->eliminar($id_borrar);
                header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
                exit();
            }
        }

        // Obtener resultados para la tabla de la flota
        $result = $vehiculoObj->listar();

        return [
            'result' => $result
        ];
    }
}