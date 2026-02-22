<?php

include("../config/claseconexion.php");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal | Gestión de Flota Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1/dist/index_rtl.umd.js"></script> 
    <link rel="stylesheet" href="../CSS/menu.css"> 
</head>
<body>

    <header>
        <h1 class="logo-admin">LOGO</h1>
        <ul class="main-nav flex items-center gap-6">
            <li><a href="./soporte.php" class="nav-item">Contacto Soporte</a></li>
        </ul>
        
    </header>

    <section class="hero" id="inicio">
        <div class="hero-content">
            <h2>Portal de Gestión de Flota</h2>
            <p>Acceda a los módulos para la administración completa de fletes, unidades y personal.</p>
        </div>
    </section>

    <section class="main-menu-section" id="modulos-gestion">
        <h2 class="section-title">Módulos de Gestión</h2>
        <p class="section-subtitle">Seleccione un módulo para comenzar su tarea administrativa.</p>

        <div class="menu-grid-container">
            
            <a href="flete.php" class="menu-card bg-orange-50 border-orange-500 hover:bg-orange-100">
                <i class="ph-bold ph-truck text-4xl text-orange-700"></i>
                <h3>Registro de Fletes</h3>
                <p>Cree, rastree y administre todos los viajes de carga.</p>
            </a>

            <a href="clientes.php" class="menu-card bg-green-50 border-green-500 hover:bg-green-100">
                <i class="ph-bold ph-users text-4xl text-green-700"></i>
                <h3>Clientes</h3>
                <p>Gestión de la base de datos de clientes y sus historiales.</p>
            </a>
            
            <a href="vehiculo.php" class="menu-card bg-yellow-50 border-yellow-500 hover:bg-yellow-100">
                <i class="ph-bold ph-person-gear text-4xl text-red-700"></i>
                <h3>Vehiculos</h3>
                <p>Administración de vehículos y su mantenimiento.</p>
            </a>

            <a href="chofer.php" class="menu-card bg-blue-50 border-blue-500 hover:bg-blue-100">
                <i class="ph-bold ph-map-trifold text-4xl text-blue-700"></i>
                <h3>Rutas de Choferes</h3>
                <p>Planificación, asignación y seguimiento de rutas logísticas.</p>
            </a>

            <a href="inventario.php" class="menu-card bg-purple-50 border-purple-500 hover:bg-purple-100">
                <i class="ph-bold ph-tire text-4xl text-purple-700"></i>
                <h3>Inventario</h3>
                <p>Agenda de revisiones, repuestos y estado de flota.</p>
            </a>
            
        </div>
    </section>

    <footer id="contacto">
        <p>&copy; 2025 Ruta Larga Furgones Unidos | Gestión de Flota.</p>
    </footer>

</body>
</html>