<?php
use App\Core\View;
use App\Core\Auth;
use App\Core\ModuleManager;

$currentUri = $_SERVER['REQUEST_URI'] ?? '/';

function navLink(string $href, string $icon, string $label, string $current): string {
    $active = ($current === $href)
           || str_starts_with($current, $href . '/')
           || str_starts_with($current, $href . '?');
    // Evitar que '/' marque todo como activo
    if ($href === '/' && $current !== '/') {
        $active = false;
    }
    $cls = $active
        ? 'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-sky-600 bg-sky-50 border border-sky-100'
        : 'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors';
    return "<a href=\"{$href}\" class=\"{$cls}\"><i class=\"{$icon} w-4 text-center\"></i> " . htmlspecialchars($label) . "</a>";
}

function renderMenuItem(array $item, string $currentUri): string
{
    if (($item['type'] ?? '') === 'section') {
        return '<p class="nav-section">' . htmlspecialchars($item['label'] ?? '') . '</p>';
    }

    // Verificar permiso
    if (!empty($item['permission']) && !\App\Core\Auth::can($item['permission'])) {
        return '';
    }
    if (!empty($item['superuser']) && !\App\Core\Auth::isSuperuser()) {
        return '';
    }

    return navLink($item['href'] ?? '#', $item['icon'] ?? 'fas fa-circle', $item['label'] ?? '', $currentUri);
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
        body { font-family: "Inter", sans-serif; background: #e8f1fb; }
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

<body class="min-h-screen flex" style="background:#e8f1fb;">

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

        <!-- Navegación dinámica (construida desde módulos instalados) -->
        <nav class="flex-1 px-2 pb-4">
            <div class="space-y-0.5">
                <?php foreach (ModuleManager::getMenu() as $item): ?>
                    <?= renderMenuItem($item, $currentUri) ?>
                <?php endforeach; ?>
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

                        <?php /* INACTIVO — sucursal única: selector de sucursal/depósito oculto
                        Para reactivar: descomentar el bloque entre estas etiquetas.

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
                            <div id="depositosDropdown" class="hidden absolute top-full left-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden" onclick="event.stopPropagation();">
                                <div class="py-2">
                                    [contenido del dropdown: lista de sucursales y depósitos]
                                </div>
                            </div>
                        </div>
                        */ ?>

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
