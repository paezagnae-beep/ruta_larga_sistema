<?php
session_start();
// SEGURIDAD: Si no hay sesión iniciada, lo devolvemos al login
if (!isset($_SESSION["usuario"])) {
    header("Location: loginView.php");
    exit();
}

// 1. CONEXIÓN (Añadimos reporte de errores)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli("localhost", "root", "", "proyecto");

    $correoSesion = $_SESSION["usuario"] ?? 'invitado'; 
    $nombreMostrar = "Usuario";

    // 2. PREPARACIÓN
    $stmt = $mysqli->prepare("SELECT Nombre, Apellido FROM usuarios WHERE Email = ?");

    $stmt->bind_param("s", $correoSesion);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        if (!empty($fila['Nombre'])) {
            $nombreMostrar = $fila['Nombre'] . " " . $fila['Apellido'];
        } else {
            $nombreMostrar = $correoSesion;
        }
    }
} catch (mysqli_sql_exception $e) {
    echo "Error en la base de datos: " . $e->getMessage();
}
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
        *{
            font-family: Georgia, 'Times New Roman', Times, serif;
        }
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../../assets/img/fondo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }

        .logo-header {
            height: 50px;
            width: auto;
            object-fit: contain;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen text-gray-800">

    <header class="fixed top-0 w-full px-6 md:px-10 py-3 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.95)] shadow-xl border-b border-gray-700">
        <div class="flex items-center">
            <img src="../../assets/carrizal (1).png" alt="Logo Ruta Larga" class="logo-header">
        </div>

        <nav class="flex items-center gap-4 md:gap-6">
            <a href="editar_perfilView.php"
                class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg border border-gray-600 transition-all group">
                <div class="bg-orange-600 p-1.5 rounded-full group-hover:bg-orange-500 transition-colors">
                    <i class="ph ph-user text-sm"></i>
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-[9px] uppercase tracking-tighter text-gray-400 leading-none">Mi Perfil</p>
                    <p class="text-[11px] font-bold truncate max-w-[150px]">
                        <?php echo htmlspecialchars($nombreMostrar); ?>
                    </p>
                </div>
            </a>

            <a href="logoutView.php"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-lg flex items-center gap-2">
                <i class="ph ph-sign-out font-bold"></i>
                <span class="hidden md:inline">Salir</span>
            </a>
        </nav>
    </header>

    <main class="flex-grow pt-32 pb-20 px-6 max-w-7xl mx-auto w-full">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 drop-shadow-md">Módulos de Gestión</h1>
            <div class="h-1.5 w-32 bg-orange-600 mx-auto rounded-full mb-6"></div>
            <p class="text-gray-200 uppercase tracking-[0.2em] text-[10px] md:text-xs font-semibold drop-shadow-sm">
                Panel Administrativo de Control Logístico</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <a href="fleteView.php"
                class="group glass-card p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 transition-colors">
                    <i class="ph ph-truck text-4xl text-orange-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Registro de Fletes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Crea, rastrea y administra todos los viajes de carga y logística.</p>
                <div class="mt-6 text-orange-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="clientesView.php"
                class="group glass-card p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-colors">
                    <i class="ph ph-users-three text-4xl text-blue-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Clientes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Gestión de base de datos de clientes, contratos e historiales.</p>
                <div class="mt-6 text-blue-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="vehiculoView.php"
                class="group glass-card p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-600 transition-colors">
                    <i class="ph ph-car-profile text-4xl text-green-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Vehículos</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Registro de vehículos y seguimiento de mantenimiento.</p>
                <div class="mt-6 text-green-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="choferView.php"
                class="group glass-card p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-600 transition-colors">
                    <i class="ph ph-identification-card text-4xl text-purple-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Choferes</h3>
                <p class="text-gray-600 text-sm leading-relaxed"> Registro de personal.</p>
                <div class="mt-6 text-purple-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="inventarioView.php"
                class="group glass-card p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-500 transition-colors">
                    <i class="ph ph-package text-4xl text-amber-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Inventario de Stock</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Control de repuestos y auditoría de recursos de taller.</p>
                <div class="mt-6 text-amber-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

            <a href="estadisticasView.php"
                class="group glass-card p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-cyan-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-cyan-600 transition-colors">
                    <i class="ph ph-chart-line-up text-4xl text-cyan-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Estadísticas y Reportes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Visualización de rendimientos y generador de reportes.</p>
                <div class="mt-6 text-cyan-600 font-bold text-[10px] uppercase tracking-[0.2em] flex items-center gap-2">
                    Acceder Módulo <i class="ph ph-caret-double-right"></i>
                </div>
            </a>

        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-400 py-10 text-center text-[10px] tracking-[0.3em] uppercase mt-auto border-t border-gray-800">
        <p class="mb-2">&copy; 2026 RUTA LARGA FURGONES UNIDOS</p>
        <p class="opacity-50 font- italic text-white">Sistema de Gestión de Flota v2.0</p>
    </footer>

</body>

</html>