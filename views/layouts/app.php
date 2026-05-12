<?php
use App\Core\View;
use App\Core\Auth;

$currentUri = $_SERVER['REQUEST_URI'] ?? '/';

// Determinar sección activa
$sec = 'dashboard';
if (str_contains($currentUri, '/inventario') || str_contains($currentUri, '/categorias') || str_contains($currentUri, '/proveedores') || str_contains($currentUri, '/depositos') || str_contains($currentUri, '/compras') || str_contains($currentUri, '/traslados')) {
    $sec = 'inventario';
} elseif (str_contains($currentUri, '/ventas') || str_contains($currentUri, '/cotizaciones')) {
    $sec = 'ventas';
} elseif (str_contains($currentUri, '/clientes')) {
    $sec = 'clientes';
} elseif (str_contains($currentUri, '/reportes')) {
    $sec = 'reportes';
} elseif (str_contains($currentUri, '/configuracion') || str_contains($currentUri, '/usuarios')) {
    $sec = 'configuracion';
}

function navLink(string $href, string $icon, string $label, string $current): string {
    $active = $current === $href || str_starts_with($current, $href . '/') || str_starts_with($current, $href . '?');
    $cls = $active
        ? 'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-sky-600 bg-sky-50 border border-sky-100'
        : 'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors';
    return "<a href=\"{$href}\" class=\"{$cls}\"><i class=\"{$icon} w-4 text-center\"></i> {$label}</a>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($page_title ?? 'Sistema POS') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");
        body { font-family: "Inter", sans-serif; background: #f8fafc; }
        .form-input, .form-select, .form-textarea { width:100%; padding:.5rem .75rem; border:1px solid #d1d5db; border-radius:.5rem; transition:all .2s; outline:none; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { box-shadow:0 0 0 2px #0EA5E9; border-color:transparent; }
        .form-input:disabled, .form-select:disabled { background-color:#f3f4f6; cursor:not-allowed; }
        .form-checkbox { height:1rem; width:1rem; color:#0EA5E9; border-color:#d1d5db; border-radius:.25rem; }
        .form-textarea { resize:none; }
        .form-label { display:block; font-size:.875rem; font-weight:500; color:#374151; margin-bottom:.25rem; }
        .form-help { font-size:.75rem; color:#6b7280; margin-top:.25rem; }
        .form-error { font-size:.75rem; color:#EF4444; margin-top:.25rem; }
        .form-group { margin-bottom:1rem; }
        .errorlist { list-style:none; margin-top:.25rem; font-size:.75rem; color:#EF4444; }
        .nav-section { font-size:0.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#9ca3af; padding:0 .75rem; margin:1.25rem 0 .4rem; }
        .nav-section:first-child { margin-top:.5rem; }
    </style>
    <?= View::yield('extra_css') ?>
</head>

<body class="min-h-screen bg-gray-50 flex">

    <!-- ══ SIDEBAR ══ -->
    <aside class="w-60 bg-white border-r border-gray-100 shadow-sm flex flex-col flex-shrink-0 overflow-y-auto z-40">

        <!-- Logo / Empresa -->
        <div class="px-4 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-sky-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-cash-register text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800 leading-tight">Sistema POS</p>
                    <?php if (!empty($sucursal_actual)): ?>
                    <p class="text-xs text-gray-400 leading-none"><?= View::e($sucursal_actual['nombre']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Navegación -->
        <nav class="flex-1 px-2 pb-4">

            <!-- Dashboard -->
            <p class="nav-section">Principal</p>
            <?= navLink('/', 'fas fa-home', 'Dashboard', $currentUri) ?>

            <!-- Ventas -->
            <p class="nav-section">Ventas</p>
            <div class="space-y-0.5">
                <?= navLink('/ventas/pos', 'fas fa-cash-register', 'Punto de Venta', $currentUri) ?>
                <?= navLink('/ventas', 'fas fa-shopping-cart', 'Historial de Ventas', $currentUri) ?>
                <?= navLink('/ventas/cotizaciones', 'fas fa-file-invoice', 'Cotizaciones', $currentUri) ?>
            </div>

            <!-- Inventario -->
            <p class="nav-section">Inventario</p>
            <div class="space-y-0.5">
                <?= navLink('/inventario', 'fas fa-box', 'Productos', $currentUri) ?>
                <?= navLink('/inventario/categorias', 'fas fa-tags', 'Categorías', $currentUri) ?>
                <?= navLink('/inventario/marcas', 'fas fa-certificate', 'Marcas', $currentUri) ?>
                <?= navLink('/inventario/proveedores', 'fas fa-truck', 'Proveedores', $currentUri) ?>
                <?= navLink('/inventario/depositos', 'fas fa-warehouse', 'Depósitos', $currentUri) ?>
                <?= navLink('/compras', 'fas fa-file-invoice-dollar', 'Compras', $currentUri) ?>
                <?= navLink('/inventario/traslados', 'fas fa-exchange-alt', 'Traslados', $currentUri) ?>
                <?= navLink('/inventario/kardex', 'fas fa-stream', 'Kardex', $currentUri) ?>
                <?= navLink('/inventario/lotes', 'fas fa-boxes', 'Lotes', $currentUri) ?>
                <?= navLink('/inventario/alertas', 'fas fa-bell', 'Alertas', $currentUri) ?>
                <?= navLink('/inventario/conteos', 'fas fa-clipboard-check', 'Conteo Físico', $currentUri) ?>
                <?= navLink('/compras/sugerencias', 'fas fa-robot', 'OC Automáticas', $currentUri) ?>
            </div>

            <!-- Clientes -->
            <p class="nav-section">Clientes</p>
            <div class="space-y-0.5">
                <?= navLink('/clientes', 'fas fa-users', 'Clientes', $currentUri) ?>
                <?= navLink('/clientes/cuentas-por-cobrar', 'fas fa-hand-holding-usd', 'Cuentas por Cobrar', $currentUri) ?>
            </div>

            <!-- Reportes -->
            <p class="nav-section">Reportes</p>
            <div class="space-y-0.5">
                <?= navLink('/reportes', 'fas fa-chart-bar', 'Reportes de Ventas', $currentUri) ?>
            </div>

            <!-- Administración -->
            <p class="nav-section">Administración</p>
            <div class="space-y-0.5">
                <?= navLink('/configuracion', 'fas fa-cog', 'Configuración', $currentUri) ?>
                <?= navLink('/configuracion/sucursales', 'fas fa-store-alt', 'Sucursales', $currentUri) ?>
                <?php if (\App\Core\Auth::can('usuarios.ver')): ?>
                <?= navLink('/usuarios', 'fas fa-users-cog', 'Usuarios y Roles', $currentUri) ?>
                <?php endif; ?>
                <?php if (\App\Core\Auth::can('bitacora.ver')): ?>
                <?= navLink('/bitacora', 'fas fa-history', 'Bitácora', $currentUri) ?>
                <?php endif; ?>
            </div>

        </nav>

        <!-- User info at bottom -->
        <div class="px-3 py-3 border-t border-gray-100 bg-gray-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-sky-600 text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-800 truncate"><?= View::e($auth['name'] ?? 'Usuario') ?></p>
                    <p class="text-[0.65rem] text-gray-400"><?= ($auth['isSuperuser'] ?? false) ? 'Administrador' : 'Vendedor' ?></p>
                </div>
                <a href="/logout" class="text-gray-400 hover:text-red-500 transition-colors ml-1" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt text-sm"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- ══ CONTENIDO PRINCIPAL ══ -->
    <div class="flex-1 overflow-auto flex flex-col min-w-0">

        <!-- Header -->
        <header class="bg-white border-b border-gray-100 shadow-sm flex-shrink-0">
            <div class="px-6 py-3">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800"><?= View::e($page_title ?? 'Sistema POS') ?></h2>
                        <p class="text-gray-500 text-xs mt-0.5"><?= View::e($page_subtitle ?? 'Sistema de punto de venta e inventario') ?></p>
                    </div>

                    <div class="flex items-center space-x-3">

                        <!-- Acciones rápidas -->
                        <div class="hidden lg:flex items-center space-x-2 mr-2 border-r border-gray-100 pr-3">
                            <a href="/ventas/nueva" class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition">
                                <i class="fas fa-plus"></i> FACTURA
                            </a>
                            <a href="/clientes/nuevo" class="flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700 transition">
                                <i class="fas fa-user-plus"></i> CLIENTE
                            </a>
                            <a href="/ventas/pos" class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 text-white rounded-lg text-xs font-medium hover:bg-gray-900 transition">
                                <i class="fas fa-cash-register"></i> POS
                            </a>
                        </div>

                        <!-- Sucursal & Depósito -->
                        <?php if (!empty($sucursal_actual)): ?>
                        <div class="relative">
                            <button type="button" class="flex items-center space-x-2 px-3 py-1.5 bg-gray-50 hover:bg-gray-100 rounded-lg transition border border-gray-100" onclick="let d=document.getElementById('depositosDropdown');if(d)d.classList.toggle('hidden');event.stopPropagation();">
                                <i class="fas fa-store text-sky-500"></i>
                                <div class="flex flex-col items-start text-left">
                                    <span class="text-[0.6rem] text-gray-400 font-bold uppercase leading-none tracking-wider mb-0.5"><?= View::e($sucursal_actual['nombre']) ?></span>
                                    <span class="text-xs font-semibold text-gray-700 leading-none flex items-center">
                                        <?= !empty($deposito_actual) ? View::e($deposito_actual['nombre']) : 'Sin depósito' ?>
                                        <i class="fas fa-chevron-down text-[0.55rem] ml-1.5 text-gray-400"></i>
                                    </span>
                                </div>
                            </button>

                            <?php if (!empty($depositos_disponibles)): ?>
                            <div id="depositosDropdown" class="hidden absolute top-full left-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden" onclick="event.stopPropagation();">
                                <div class="py-2">
                                    <?php if (!empty($sucursales_disponibles) && count($sucursales_disponibles) > 1): ?>
                                    <div class="px-4 py-2 text-[0.6rem] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1 flex items-center">
                                        <i class="fas fa-store-alt mr-2"></i> Cambiar Sucursal
                                    </div>
                                    <div class="max-h-48 overflow-y-auto mb-2">
                                        <?php foreach ($sucursales_disponibles as $sucursal): ?>
                                        <a href="/configuracion/sucursal/<?= $sucursal['sucursal_id'] ?>"
                                           class="flex items-center px-4 py-2.5 text-sm <?= (!empty($sucursal_actual) && $sucursal_actual['sucursal_id'] == $sucursal['sucursal_id']) ? 'bg-blue-50 text-sky-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' ?> transition-colors">
                                            <i class="fas fa-map-marker-alt mr-3 text-xs text-gray-400"></i>
                                            <span class="truncate"><?= View::e($sucursal['nombre']) ?></span>
                                            <?php if (!empty($sucursal_actual) && $sucursal_actual['sucursal_id'] == $sucursal['sucursal_id']): ?>
                                                <i class="fas fa-check-circle ml-auto text-sky-500 text-xs"></i>
                                            <?php endif; ?>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="border-t border-gray-50 my-1"></div>
                                    <?php endif; ?>

                                    <div class="px-4 py-2 text-[0.6rem] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1 flex items-center">
                                        <i class="fas fa-warehouse mr-2"></i> Depósitos — <?= View::e($sucursal_actual['nombre'] ?? '') ?>
                                    </div>
                                    <div class="max-h-60 overflow-y-auto">
                                        <?php foreach ($depositos_disponibles as $deposito): ?>
                                        <a href="/configuracion/deposito/<?= $deposito['deposito_id'] ?>"
                                           class="flex items-center px-4 py-2.5 text-sm <?= (!empty($deposito_actual) && $deposito_actual['deposito_id'] == $deposito['deposito_id']) ? 'bg-blue-50 text-sky-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' ?> transition-colors">
                                            <i class="fas <?= $deposito['es_principal'] ? 'fa-star text-amber-400' : 'fa-box text-gray-400' ?> mr-3 text-xs"></i>
                                            <span class="truncate"><?= View::e($deposito['nombre']) ?></span>
                                            <?php if (!empty($deposito_actual) && $deposito_actual['deposito_id'] == $deposito['deposito_id']): ?>
                                                <i class="fas fa-check-circle ml-auto text-sky-500 text-xs"></i>
                                            <?php endif; ?>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Notificaciones -->
                        <div class="relative">
                            <button type="button" class="relative w-9 h-9 flex items-center justify-center rounded-lg bg-gray-50 text-gray-500 hover:bg-gray-100 transition border border-gray-100" onclick="let n=document.getElementById('notifDropdown');if(n)n.classList.toggle('hidden');event.stopPropagation();">
                                <i class="fas fa-bell text-sm"></i>
                                <span class="absolute top-2 right-2 bg-rose-500 w-2 h-2 rounded-full border-2 border-white"></span>
                            </button>
                            <div id="notifDropdown" class="hidden absolute top-full right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-50">
                                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Notificaciones</h3>
                                </div>
                                <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                    <i class="fas fa-bell-slash text-2xl mb-2 block"></i>
                                    No hay notificaciones
                                </div>
                            </div>
                        </div>

                        <!-- Usuario -->
                        <div class="flex items-center gap-2 pl-2 border-l border-gray-100">
                            <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center">
                                <i class="fas fa-user text-sky-600 text-sm"></i>
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-xs font-semibold text-gray-800 leading-none"><?= View::e($auth['name'] ?? 'Usuario') ?></p>
                                <p class="text-[0.65rem] text-gray-400 mt-0.5"><?= ($auth['isSuperuser'] ?? false) ? 'Administrador' : 'Vendedor' ?></p>
                            </div>
                            <a href="/usuarios/perfil" class="text-gray-400 hover:text-sky-500 transition-colors ml-1" title="Mi Perfil">
                                <i class="fas fa-user-cog text-sm"></i>
                            </a>
                            <a href="/auth/2fa/setup" class="text-gray-400 hover:text-indigo-500 transition-colors ml-1" title="Verificación en dos pasos">
                                <i class="fas fa-shield-halved text-sm"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if (!empty($flash)): ?>
        <div class="px-6 pt-4 space-y-2">
            <?php foreach ($flash as $type => $messages): ?>
                <?php
                $colors = [
                    'success' => 'bg-green-50 text-green-800 border-green-200',
                    'danger'  => 'bg-red-50 text-red-800 border-red-200',
                    'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                    'info'    => 'bg-blue-50 text-blue-800 border-blue-200',
                ];
                $colorClass = $colors[$type] ?? $colors['info'];
                ?>
                <?php foreach ($messages as $msg): ?>
                <div class="border rounded-lg px-4 py-3 text-sm flex items-center gap-2 <?= $colorClass ?>">
                    <i class="fas fa-info-circle"></i>
                    <span><?= View::e($msg) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Contenido -->
        <main class="px-6 py-4 flex-1">
            <?= View::yield('content') ?>
        </main>
    </div>

    <script>
        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function() {
            document.querySelectorAll('#depositosDropdown, #notifDropdown').forEach(function(el) {
                el.classList.add('hidden');
            });
        });
    </script>
    <?= View::yield('extra_js') ?>
</body>
</html>
