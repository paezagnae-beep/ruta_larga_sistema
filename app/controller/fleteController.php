<?php
require_once dirname(__DIR__) . "/model/fleteModel.php";

class FleteController
{
    public function manejarPeticiones()
    {
        $fleteObj = new Flete(); 
        $msg_js = "";

        // --- 1. PROCESAR ACCIONES (POST/GET) ---
        
        // Registrar o Editar
        if (isset($_POST['registrar']) || isset($_POST['editar'])) {
            
            // Verificación de campos obligatorios
            if (!empty($_POST['origen']) && !empty($_POST['destino']) && !empty($_POST['fecha'])) {
                
                $fleteObj->setIdCliente($_POST['id_cliente']);
                $fleteObj->setIdChofer($_POST['id_chofer']);
                $fleteObj->setIdVehiculo($_POST['id_vehiculo']);
                $fleteObj->setOrigen($_POST['origen']);
                $fleteObj->setDestino($_POST['destino']);
                $fleteObj->setEstado($_POST['estado']);
                $fleteObj->setValor($_POST['valor']);
                $fleteObj->setCancelado($_POST['cancelado']);
                $fleteObj->setFecha($_POST['fecha']);

                if (isset($_POST['registrar'])) {
                    if ($fleteObj->insertar()) {
                        header("Location: fleteview.php?status=reg");
                        exit();
                    } else {
                        $msg_js = "Swal.fire('Error', 'No se pudo guardar el registro', 'error');";
                    }
                } else {
                    $fleteObj->setId($_POST['id_flete']);
                    if ($fleteObj->actualizar()) {
                        header("Location: fleteview.php?status=edit");
                        exit();
                    } else {
                        $msg_js = "Swal.fire('Error', 'No se pudo actualizar la información', 'error');";
                    }
                }
            } else {
                $msg_js = "Swal.fire('Atención', 'Todos los campos marcados son obligatorios', 'warning');";
            }
        }

        // Eliminar
        if (isset($_GET['delete_id'])) {
            $fleteObj->setId($_GET['delete_id']);
            if ($fleteObj->eliminar()) {
                header("Location: fleteview.php?status=del");
                exit();
            } else {
                $msg_js = "Swal.fire('Error', 'El registro no pudo ser eliminado', 'error');";
            }
        }

        // --- 2. OBTENER DATOS PARA LA VISTA ---
        
        // Obtenemos el listado para la tabla
        $result = $fleteObj->listar(); 

        return [
            'fleteObj' => $fleteObj,
            'result'   => $result,
            'msg_js'   => $msg_js
        ];
    }
}