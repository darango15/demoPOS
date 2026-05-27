<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<?php $s = $stats ?? []; $compras = $compras ?? []; $pagination = $pagination ?? []; ?>

<!-- Tarjetas de métricas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-sky-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Total OC</p>
        <p class="text-2xl font-black text-sky-600"><?= (int)($s['total'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">órdenes registradas</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-amber-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Pendientes</p>
        <p class="text-2xl font-black text-amber-600"><?= (int)($s['pendientes'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">por recibir o parciales</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-emerald-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Recibidas</p>
        <p class="text-2xl font-black text-emerald-600"><?= (int)($s['recibidas'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">completadas</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-violet-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Monto recibido</p>
        <p class="text-2xl font-black text-violet-600">$<?= number_format((float)($s['monto_recibido'] ?? 0), 2) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">en OC completadas</p>
    </div>
</div>

<!-- Barra de búsqueda -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="GET" id="search-form" class="flex flex-wrap items-end gap-3">

        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar</label>
            <div class="relative">
                <i class="fas fa-search absolute left-0 top-2 text-gray-400 text-xs"></i>
                <input type="text" name="buscar" id="search-input"
                    value="<?= View::e($buscar ?? '') ?>"
                    placeholder="N° factura o proveedor..."
                    class="w-full pl-5 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
                <div id="search-spinner" class="absolute right-0 top-2 hidden">
                    <i class="fas fa-spinner fa-spin text-sky-500 text-xs"></i>
                </div>
            </div>
        </div>

        <div class="min-w-28">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Por página</label>
            <select name="por_pagina" onchange="this.form.submit()"
                class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <?php foreach ([10, 25, 50, 100] as $n): ?>
                <option value="<?= $n ?>" <?= (($pagination['per_page'] ?? 25) == $n) ? 'selected' : '' ?>><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Contador de resultados + acciones -->
        <div class="flex items-center gap-2 ml-auto">
            <span class="text-xs font-medium text-gray-400 whitespace-nowrap">
                <?= number_format($pagination['total'] ?? 0) ?> orden(es)
                <?php if (!empty($buscar)): ?>
                <span class="text-sky-500">— filtrado</span>
                <?php endif; ?>
            </span>
            <?php if (!empty($buscar)): ?>
            <a href="/compras"
                class="inline-flex items-center gap-1 px-3 py-2 border border-gray-200 text-gray-400 rounded-lg text-sm hover:bg-gray-50 transition"
                title="Limpiar búsqueda">
                <i class="fas fa-times"></i>
            </a>
            <?php endif; ?>
            <a href="/compras/nueva"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-plus"></i> Nueva Compra
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xs font-medium text-gray-500">
            Ordenado por fecha de OC — más reciente primero
        </span>
        <span class="text-xs text-gray-400">
            Pág. <?= $pagination['current_page'] ?? 1 ?> / <?= $pagination['total_pages'] ?? 1 ?>
        </span>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                <th class="px-4 py-3 bg-white">Factura #</th>
                <th class="px-4 py-3 bg-white">Proveedor</th>
                <th class="px-4 py-3 bg-white text-right">Monto total</th>
                <th class="px-4 py-3 bg-white text-center">Estado</th>
                <th class="px-4 py-3 bg-white">Fecha OC</th>
                <th class="px-4 py-3 bg-white text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($compras as $compra): ?>
            <?php
                $badgeMap = [
                    'recibida'              => ['bg-emerald-50 text-emerald-700 border-emerald-100', 'Recibida'],
                    'parcialmente_recibida' => ['bg-blue-50 text-blue-700 border-blue-100',          'Parcial'],
                    'pendiente'             => ['bg-amber-50 text-amber-700 border-amber-100',        'Pendiente'],
                    'cancelada'             => ['bg-red-50 text-red-600 border-red-100',              'Cancelada'],
                ];
                [$badgeClass, $badgeLabel] = $badgeMap[$compra['estado']] ?? ['bg-gray-100 text-gray-500 border-gray-200', ucfirst($compra['estado'])];
            ?>
            <tr class="hover:bg-sky-50/40 transition-colors group">

                <!-- Factura -->
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-file-invoice-dollar text-sky-400 text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-800 group-hover:text-sky-700 transition-colors font-mono text-xs">
                            <?= View::e($compra['numero_factura']) ?>
                        </span>
                    </div>
                </td>

                <!-- Proveedor -->
                <td class="px-4 py-3 text-gray-600 text-sm"><?= View::e($compra['proveedor_nombre']) ?></td>

                <!-- Monto -->
                <td class="px-4 py-3 text-right font-bold text-gray-900">
                    $<?= number_format((float)$compra['monto_total'], 2) ?>
                </td>

                <!-- Estado -->
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                        <?= $badgeLabel ?>
                    </span>
                </td>

                <!-- Fecha OC -->
                <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                    <?= date('d/m/Y', strtotime($compra['fecha_compra'] ?? $compra['fecha_registro'])) ?>
                </td>

                <!-- Acciones -->
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-3 opacity-60 group-hover:opacity-100 transition-opacity">
                        <?php if (\in_array($compra['estado'], ['pendiente', 'parcialmente_recibida'])): ?>
                        <a href="/compras/<?= $compra['compra_id'] ?>/recibir"
                            class="text-gray-400 hover:text-blue-600 transition-colors" title="Registrar recepción">
                            <i class="fas fa-truck-loading text-xs"></i>
                        </a>
                        <?php endif; ?>
                        <a href="/compras/<?= $compra['compra_id'] ?>"
                            class="text-gray-400 hover:text-sky-600 transition-colors" title="Ver detalles">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($compras)): ?>
            <tr>
                <td colspan="6" class="px-4 py-16 text-center">
                    <div class="inline-flex flex-col items-center gap-2">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-file-invoice-dollar text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-400">No se encontraron órdenes de compra</p>
                        <?php if (empty($buscar)): ?>
                        <a href="/compras/nueva"
                            class="mt-1 text-sky-500 hover:underline text-sm font-medium">
                            Registrar primera compra
                        </a>
                        <?php else: ?>
                        <a href="/compras" class="mt-1 text-gray-400 hover:text-sky-500 text-xs">
                            Limpiar búsqueda
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (($pagination['total_pages'] ?? 1) > 1): ?>
<div class="mt-4">
    <?php View::include('partials.pagination', ['pagination' => $pagination]); ?>
</div>
<?php endif; ?>

<?php View::endSection('content'); ?>

<?php View::section('extra_js'); ?>
<script>
    var searchTimeout;
    var searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            var spinner = document.getElementById('search-spinner');
            if (e.target.value.length >= 2 || e.target.value.length === 0) {
                spinner.classList.remove('hidden');
                searchTimeout = setTimeout(function() {
                    document.getElementById('search-form').submit();
                }, 500);
            }
        });
    }
</script>
<?php View::endSection('extra_js'); ?>
