<?php
require_once dirname(__DIR__) . "/model/nuevoreguistroModel.php";

class NuevoRegistroController
{
    public function manejarPeticiones()
    {
        $mensaje = "";

        if (isset($_POST["guardar"])) {
            $email = $_POST["email"] ?? '';
            $password = $_POST["password"] ?? '';

            if ($email && $password) {
                $usuario = new Usuario($email, $password);
                if ($usuario->registrar()) {
                    $mensaje = "Usuario registrado correctamente";
                } else {
                    $mensaje = "Error: el correo ya existe";
                }
            }
        }

        return [
            'mensaje' => $mensaje
        ];
    }
}