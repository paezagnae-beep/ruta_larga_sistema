<?php
session_start();

// 1. SEGURIDAD: Si no hay sesión iniciada, al login
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

// 2. CONTROL DE INACTIVIDAD (3 minutos = 180 segundos)
$timeout = 180; 

if (isset($_SESSION['ultima_actividad'])) {
    $segundos_inactivo = time() - $_SESSION['ultima_actividad'];
    
    if ($segundos_inactivo >= $timeout) {
        session_unset();
        session_destroy();
        header("Location: login.php?mensaje=sesion_caducada");
        exit();
    }
}
$_SESSION['ultima_actividad'] = time();

/**
 * 3. PROCESAMIENTO DEL NOMBRE DE USUARIO
 */
$usuario_raw = $_SESSION["usuario"];
$nombre_partido = explode('@', $usuario_raw)[0];
$nombre_limpio = str_replace(['.', '_', '-'], ' ', $nombre_partido);
$nombre_final = ucwords($nombre_limpio);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control | Ruta Larga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: Georgia, 'Times New Roman', Times, serif; }
        .card-glow:hover { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <header class="fixed top-0 w-full px-10 py-5 flex justify-between items-center z-50 bg-[rgba(8,8,44,0.96)] shadow-2xl border-b border-gray-700 backdrop-blur-md">
        <h2 class="text-white text-2xl font-bold tracking-wider uppercase">Ruta Larga</h2>
        
        <nav class="flex items-center gap-6">
            <div class="hidden lg:flex flex-col items-end border-r border-gray-600 pr-6">
                <span class="text-gray-400 text-[10px] uppercase tracking-widest">Operador Activo</span>
                <span class="text-white font-medium italic text-sm"><?= htmlspecialchars($nombre_final) ?></span>
            </div>
            
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-lg flex items-center gap-2">
                <i class="ph ph-power-bold"></i> Salir
            </a>
        </nav>
    </header>

    <main class="flex-grow pt-32 pb-20 px-6 max-w-7xl mx-auto w-full">
        
        <div id="session-alert" class="hidden mb-8 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-700 rounded-lg shadow-md flex items-center justify-between animate-bounce">
            <div class="flex items-center gap-3">
                <i class="ph ph-timer text-2xl"></i>
                <p class="text-sm font-bold uppercase">Aviso: Tu sesión expirará pronto por falta de actividad.</p>
            </div>
        </div>

        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Módulos Administrativos</h1>
            <div class="h-1.5 w-24 bg-gray-700 mx-auto rounded-full mb-6"></div>
            <p class="text-gray-400 uppercase tracking-[0.25em] text-[10px] font-bold">Gestión de Logística y Transporte Pesado</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <a href="flete.php" class="group bg-white p-8 rounded-[2rem] border border-gray-100 card-glow transition-all duration-500 hover:-translate-y-2">
                <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 transition-all duration-500">
                    <i class="ph ph-truck text-4xl text-orange-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Registro Fletes</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Control de despachos, guías y rastreo de carga.</p>
                <div class="mt-8 text-orange-600 font-bold text-[10px] uppercase tracking-widest flex items-center gap-2 group-hover:gap-4 transition-all">
                    Entrar al módulo <i class="ph ph-arrow-right"></i>
                </div>
            </a>

            <a href="clientes.php" class="group bg-white p-8 rounded-[2rem] border border-gray-100 card-glow transition-all duration-500 hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-all duration-500">
                    <i class="ph ph-users-three text-4xl text-blue-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Clientes</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Base de datos centralizada y facturación.</p>
                <div class="mt-8 text-blue-600 font-bold text-[10px] uppercase tracking-widest flex items-center gap-2 group-hover:gap-4 transition-all">
                    Entrar al módulo <i class="ph ph-arrow-right"></i>
                </div>
            </a>

            <a href="vehiculo.php" class="group bg-white p-8 rounded-[2rem] border border-gray-100 card-glow transition-all duration-500 hover:-translate-y-2">
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-600 transition-all duration-500">
                    <i class="ph ph-car-profile text-4xl text-green-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2 italic">Flota Vehicular</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Estado técnico y mantenimientos preventivos.</p>
                <div class="mt-8 text-green-600 font-bold text-[10px] uppercase tracking-widest flex items-center gap-2 group-hover:gap-4 transition-all">
                    Entrar al módulo <i class="ph ph-arrow-right"></i>
                </div>
            </a>

        </div>
    </main>

    <footer class="bg-[rgb(8,8,44)] text-gray-500 py-8 text-center text-[10px] tracking-[0.3em] uppercase mt-auto border-t border-gray-800">
        <p>&copy; 2026 RUTA LARGA | Operaciones Blindadas</p>
    </footer>

    <script>
        let tiempoInactividad = 0;
        const limiteSegundos = 180; 

        const intervalo = setInterval(() => {
            tiempoInactividad++;
            if (tiempoInactividad >= (limiteSegundos - 30)) {
                document.getElementById('session-alert').classList.remove('hidden');
            }
            if (tiempoInactividad >= limiteSegundos) {
                window.location.href = "logout.php?causa=inactividad";
            }
        }, 1000);

        function resetearContador() {
            tiempoInactividad = 0;
            document.getElementById('session-alert').classList.add('hidden');
        }

        const eventos = ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'];
        eventos.forEach(evt => window.addEventListener(evt, resetearContador));
    </script>
</body>
</html>