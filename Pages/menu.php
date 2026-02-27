<?php
session_start();

// 1. SEGURIDAD: Si no hay sesión iniciada, lo devolvemos al login
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

/**
 * 2. PROCESAMIENTO DEL NOMBRE DE USUARIO
 * Limpiamos el correo o nombre para que se vea profesional
 */
$usuario_raw = $_SESSION["usuario"];
$nombre_partido = explode('@', $usuario_raw)[0]; // Quitamos el @dominio si existe
$nombre_limpio = str_replace(['.', '_', '-'], ' ', $nombre_partido); // Quitamos puntos o guiones
$nombre_final = ucwords($nombre_limpio); // Ponemos en mayúscula cada palabra
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: Georgia, 'Times New Roman', Times, serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-10 py-5 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.95)] shadow-xl border-b border-gray-700">
        <h2 class="text-white text-2xl font-bold tracking-wider uppercase">Ruta Larga</h2>
        <nav class="flex items-center gap-6">
            <div class="hidden lg:flex flex-col items-end border-r border-gray-600 pr-6">
                <span class="text-gray-400 text-[10px] uppercase tracking-widest">Usuario Activo</span>
                <span class="text-white font-medium italic text-sm"><?= htmlspecialchars($nombre_final) ?></span>
            </div>
            
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-lg flex items-center gap-2">
                <i class="ph ph-power-bold"></i> Salir
            </a>
        </nav>
    </header>

    <main class="flex-grow pt-32 pb-20 px-6 max-w-7xl mx-auto w-full">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Módulos de Gestión</h1>
            <div class="h-1.5 w-32 bg-gray-600 mx-auto rounded-full mb-6"></div>
            <p class="text-gray-500 uppercase tracking-[0.2em] text-xs font-semibold">Seleccione una unidad administrativa para continuar</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <a href="flete.php" class="group bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 transition-colors">
                    <i class="ph ph-truck text-4xl text-orange-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Registro de Fletes</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Cree, rastree y administre todos los viajes de carga y logística.</p>
                <div class="mt-6 text-orange-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="clientes.php" class="group bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-colors">
                    <i class="ph ph-users-three text-4xl text-blue-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Clientes</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Gestión de base de datos de clientes, contratos e historiales.</p>
                <div class="mt-6 text-blue-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="vehiculo.php" class="group bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-600 transition-colors">
                    <i class="ph ph-car-profile text-4xl text-green-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Vehículos</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Administración técnica de la flota y mantenimiento preventivo.</p>
                <div class="mt-6 text-green-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="chofer.php" class="group bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-600 transition-colors">
                    <i class="ph ph-identification-card text-4xl text-purple-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Rutas de Choferes</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Asignación de personal a unidades y seguimiento de rutas logísticas.</p>
                <div class="mt-6 text-purple-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="inventario.php" class="group bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-yellow-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-yellow-500 transition-colors">
                    <i class="ph ph-package text-4xl text-yellow-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Inventario</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Control de repuestos, stock de flota y auditoría de recursos.</p>
                <div class="mt-6 text-yellow-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-500 py-10 text-center text-[10px] tracking-[0.3em] uppercase mt-auto border-t border-gray-800">
        <p class="mb-2">&copy; 2026 RUTA LARGA FURGONES UNIDOS</p>
        <p class="opacity-50 font-sans italic">Sistema de Gestión de Flota v2.0</p>
    </footer>

</body>
</html>