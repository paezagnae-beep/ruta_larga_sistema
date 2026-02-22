<?php

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contacto | Ruta Larga Furgones Unidos</title>
  <link rel="stylesheet" href="../CSS/contacto.css">
</head>

<body>

  <header>
    <h2 class="logo">LOGO</h2>
    <nav class="navigation">
      <a href="menu.php">Inicio</a>
    </nav>
  </header>

  <section class="contact-hero">
    <h1>CONTÁCTANOS</h1>
  </section>

  <section class="contact-section">
    <div class="contact-container">

    </div>

    <div class="contact-form-wrapper">
      <h2>Envíanos un Mensaje</h2>
      <form class="contact-form" action="#" method="POST">
        <div class="form-group">
          <label for="nombre">Nombre Completo</label>
          <input type="text" id="nombre" name="nombre" required placeholder="Ej: Luis Galindez">
        </div>
        <div class="form-group">
          <label for="email">Correo Electrónico</label>
          <input type="email" id="email" name="email" required placeholder="Ej: tu.correo@empresa.com">
        </div>
        <div class="form-group">
          <label for="asunto">Asunto</label>
          <input type="text" id="asunto" name="asunto" required placeholder="Ej: Consulta sobre servicios">
        </div>
        <div class="form-group">
          <label for="mensaje">Mensaje</label>
          <textarea id="mensaje" name="mensaje" rows="5" required placeholder="Escribe tu consulta aquí..."></textarea>
        </div>
        <button type="submit" class="submit-btn">Enviar Mensaje</button>
      </form>
    </div>

    </div>
  </section>

  <footer>
    <p>© 2025 Ruta Larga Furgones Unidos Carrizal. Todos los derechos reservados.</p>
    <div class="social-links">
      <a href="#">Privacidad</a> | <a href="#">Términos de Servicio</a> | <a href="#">Aviso Legal</a>
    </div>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>

</html>