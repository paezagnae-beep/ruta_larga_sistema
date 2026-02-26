<?php
$mensaje_status = "";
$color_status = "";

if (isset($_POST['enviar_recuperacion'])) {
    $emailDestino = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
        $mensaje_status = "Por favor, ingresa un correo válido.";
        $color_status = "bg-red-100 text-red-700";
    } else {
        // CONFIGURACIÓN DEL CORREO
        $asunto = "Recuperar Contrasena - Ruta Larga";
        
        // El cuerpo del mensaje (puedes usar HTML)
        $cuerpo = "
        <html>
        <head>
          <title>Recuperar Contrasena</title>
        </head>
        <body>
          <h2>Hola,</h2>
          <p>Has solicitado restablecer tu acceso al sistema <strong>Ruta Larga</strong>.</p>
          <p>Haz clic en el enlace de abajo para cambiar tu clave:</p>
          <p><a href='http://tusitio.com/restablecer.php?email=$emailDestino' style='color:blue; text-decoration:underline;'>Restablecer mi clave</a></p>
          <br>
          <p>Si no fuiste tú, ignora este mensaje.</p>
        </body>
        </html>
        ";

        // Cabeceras para que el correo acepte HTML y se vea profesional
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Sistema Ruta Larga <soporte@tusitio.com>" . "\r\n";

        // INTENTO DE ENVÍO
        if (mail($emailDestino, $asunto, $cuerpo, $headers)) {
            $mensaje_status = "Instrucciones enviadas a tu correo.";
            $color_status = "bg-green-100 text-green-700";
        } else {
            $mensaje_status = "Error del servidor al enviar el correo.";
            $color_status = "bg-red-100 text-red-700";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Recuperar Contraseña</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex items-center justify-center">

  <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg border-t-4 border-blue-500">
    <h1 class="text-2xl font-bold mb-4 text-center text-gray-800">Recuperar Contraseña</h1>
    
    <?php if ($mensaje_status != ""): ?>
        <div class="mb-6 p-3 text-sm text-center rounded-lg <?= $color_status ?>">
            <?= $mensaje_status ?>
        </div>
    <?php endif; ?>

    <p class="text-gray-600 text-sm text-center mb-6">
      Ingresa tu email para enviarte un enlace de recuperación.
    </p>

    <form method="POST" action="" class="space-y-4">
      <div>
        <input type="email" name="email" placeholder="Correo electrónico" required
          class="w-full px-4 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"/>
      </div>
      
      <button type="submit" name="enviar_recuperacion"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition-all">
        Enviar enlace
      </button>
    </form>

    <div class="text-center mt-6">
      <a href="login.php" class="text-sm text-gray-500 hover:underline">Volver al inicio de sesión</a>
    </div>
  </div>

</body>
</html>