<?php
require_once dirname(__DIR__) . "/model/choferModel.php";

class ChoferPresenter
{
    public function manejarPeticiones()
    {
        $choferObj = new Chofer();
        $msg_js = "";

        if (isset($_POST['registrar']) || isset($_POST['editar'])) {
            $tipo = $_POST['tipo_doc'] ?? 'V';
            $num_rif = preg_replace('/[^0-9]/', '', $_POST['RIF_cedula']);
            $rif_final = $tipo . $num_rif;

            $operadora = $_POST['operadora'] ?? '0414';
            $num_telf = preg_replace('/[^0-9]/', '', $_POST['telefono_num']);
            $telf_final = $operadora . $num_telf;
            $nombre = trim($_POST['nombre']);
            if (strlen($num_rif) < 6) {
                $msg_js = "swalError('El documento es demasiado corto.');";
            } elseif (strlen($num_telf) != 7) {
                $msg_js = "swalError('El número de teléfono debe tener 7 dígitos después de la operadora.');";
            } else {
                $choferObj->setRif($rif_final);
                $choferObj->setNombre($nombre);
                $choferObj->setTelefono($telf_final);

                if (isset($_POST['registrar'])) {
                    $choferObj->insertar();
                    header("Location: " . $_SERVER['PHP_SELF'] . "?status=reg");
                } else {
                    $choferObj->setId($_POST['ID_chofer']);
                    $choferObj->modificar();
                    header("Location: " . $_SERVER['PHP_SELF'] . "?status=edit");
                }
                exit();
            }
        }

        if (isset($_GET['delete'])) {
            $choferObj->eliminar(intval($_GET['delete']));
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=del");
            exit();
        }

        $result = $choferObj->listar();

        return [
            'msg_js' => $msg_js,
            'result' => $result
        ];
    }
}