<?php
require_once dirname(__DIR__) . "/model/clientesModel.php";

class ClienteController
{
    public function manejarPeticiones()
    {
        $clienteObj = new Cliente();
        $msg_js = "";

        // PROCESAMIENTO (REGISTRAR Y EDITAR)
        if (isset($_POST['registrar']) || isset($_POST['editar'])) {
            // Unir RIF (Tipo + Número)
            $tipo_doc = $_POST['tipo_doc'] ?? 'V';
            $num_rif = preg_replace('/[^0-9]/', '', $_POST['RIF_cedula']);
            $rif_final = $tipo_doc . $num_rif;

            // Unir Teléfono (Operadora + Número)
            $operadora = $_POST['operadora'] ?? '0414';
            $num_telf = preg_replace('/[^0-9]/', '', $_POST['telefono_num']);
            $telf_final = $operadora . $num_telf;

            $nombre = trim($_POST['nombre']);
            
            // CAPTURAR FECHA DE REGISTRO
            // Si por alguna razón no viene en el POST, usamos la fecha actual
            $fecha_registro = $_POST['fecha_registro'] ?? date('Y-m-d');

            // Validaciones básicas
            if (strlen($num_rif) < 6) {
                $msg_js = "Swal.fire('Error', 'El documento es muy corto.', 'error');";
            } elseif (strlen($num_telf) != 7) {
                $msg_js = "Swal.fire('Error', 'El teléfono debe tener 7 dígitos después de la operadora.', 'error');";
            } else {
                // Seteamos los valores en el objeto
                $clienteObj->setRif($rif_final);
                $clienteObj->setNombre($nombre);
                $clienteObj->setTelefono($telf_final);
                $clienteObj->setFecha($fecha_registro); // Asignación de la fecha

                if (isset($_POST['registrar'])) {
                    if ($clienteObj->insertar()) {
                        header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
                        exit();
                    }
                } else {
                    $clienteObj->setId($_POST['ID_cliente']);
                    if ($clienteObj->modificar()) {
                        header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
                        exit();
                    }
                }
            }
        }

        // ELIMINAR
        if (isset($_GET['delete'])) {
            $clienteObj->eliminar(intval($_GET['delete']));
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
            exit();
        }

        // OBTENER LISTA ACTUALIZADA
        $result = $clienteObj->listar();

        return [
            'result' => $result,
            'msg_js' => $msg_js
        ];
    }
}