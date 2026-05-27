<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<?php $s = $stats ?? []; $traslados = $traslados ?? []; $pagination = $pagination ?? []; ?>

<!-- Tarjetas de métricas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-sky-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Total</p>
        <p class="text-2xl font-black text-sky-600"><?= (int)($s['total'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">traslados registrados</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-amber-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">En tránsito</p>
        <p class="text-2xl font-black text-amber-600"><?= (int)($s['en_transito'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">pendientes de recepción</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-emerald-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Recibidos</p>
        <p class="text-2xl font-black text-emerald-600"><?= (int)($s['recibidos'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">completados</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-gray-300">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Cancelados</p>
        <p class="text-2xl font-black text-gray-500"><?= (int)($s['cancelados'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">anulados</p>
    </div>
</div>

<!-- Barra de búsqueda -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="GET" id="search-form" class="flex flex-wrap items-end gap-3">

        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar por usuario</label>
            <div class="relative">
                <i class="fas fa-user absolute left-0 top-2 text-gray-400 text-xs"></i>
                <input type="text" name="buscar" id="search-input"
                    value="<?= View::e($buscar ?? '') ?>"
                    placeholder="Nombre de quien realiza el traslado..."
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

        <div class="flex items-center gap-2 ml-auto">
            <span class="text-xs font-medium text-gray-400 whitespace-nowrap">
                <?= number_format($pagination['total'] ?? 0) ?> traslado(s)
                <?php if (!empty($buscar)): ?>
                <span class="text-sky-500">— filtrado</span>
                <?php endif; ?>
            </span>
            <?php if (!empty($buscar)): ?>
            <a href="/inventario/traslados"
                class="inline-flex items-center gap-1 px-3 py-2 border border-gray-200 text-gray-400 rounded-lg text-sm hover:bg-gray-50 transition"
                title="Limpiar búsqueda">
                <i class="fas fa-times"></i>
            </a>
            <?php endif; ?>
            <a href="/inventario/traslados/nuevo"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-exchange-alt"></i> Nuevo Traslado
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" x-data="trasladosList()">

    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xs font-medium text-gray-500">Ordenado por más reciente</span>
        <span class="text-xs text-gray-400">
            Pág. <?= $pagination['current_page'] ?? 1 ?> / <?= $pagination['total_pages'] ?? 1 ?>
        </span>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                <th class="px-4 py-3 bg-white">#</th>
                <th class="px-4 py-3 bg-white">Origen</th>
                <th class="px-4 py-3 bg-white">Destino</th>
                <th class="px-4 py-3 bg-white text-center">Estado</th>
                <th class="px-4 py-3 bg-white">Enviado por</th>
                <th class="px-4 py-3 bg-white text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($traslados as $t): ?>
            <?php
                $badgeMap = [
                    'recibido'    => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    'en_transito' => 'bg-amber-50 text-amber-700 border-amber-100',
                    'borrador'    => 'bg-gray-100 text-gray-500 border-gray-200',
                    'cancelado'   => 'bg-red-50 text-red-600 border-red-100',
                ];
                $badgeClass = $badgeMap[$t['estado']] ?? 'bg-gray-100 text-gray-500 border-gray-200';
            ?>
            <tr class="hover:bg-sky-50/40 transition-colors group">

                <!-- # -->
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-exchange-alt text-sky-400 text-xs"></i>
                        </div>
                        <span class="font-mono text-xs text-gray-400">#<?= $t['traslado_id'] ?></span>
                    </div>
                </td>

                <!-- Origen -->
                <td class="px-4 py-3 font-semibold text-gray-700 text-sm">
                    <?= View::e($t['origen_nombre']) ?>
                </td>

                <!-- Destino -->
                <td class="px-4 py-3 text-gray-600 text-sm">
                    <div class="flex items-center gap-1.5">
                        <i class="fas fa-arrow-right text-gray-300 text-xs"></i>
                        <?= View::e($t['destino_nombre']) ?>
                    </div>
                </td>

                <!-- Estado -->
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                        <?= ucfirst(str_replace('_', ' ', $t['estado'])) ?>
                    </span>
                </td>

                <!-- Enviado por -->
                <td class="px-4 py-3">
                    <p class="text-sm font-semibold text-gray-700"><?= View::e($t['usuario_envia_nombre']) ?></p>
                    <p class="text-xs text-gray-400 mt-0.5"><?= date('d/m/Y H:i', strtotime($t['fecha_envio'])) ?></p>
                </td>

                <!-- Acciones -->
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center opacity-60 group-hover:opacity-100 transition-opacity">
                        <?php if ($t['estado'] === 'en_transito'): ?>
                        <button @click="recibirTraslado(<?= $t['traslado_id'] ?>)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-semibold hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition">
                            <i class="fas fa-check"></i> Confirmar
                        </button>
                        <?php else: ?>
                        <span class="text-xs text-gray-300">—</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($traslados)): ?>
            <tr>
                <td colspan="6" class="px-4 py-16 text-center">
                    <div class="inline-flex flex-col items-center gap-2">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-400">No se encontraron traslados</p>
                        <?php if (empty($buscar)): ?>
                        <a href="/inventario/traslados/nuevo"
                            class="mt-1 text-sky-500 hover:underline text-sm font-medium">
                            Crear el primer traslado
                        </a>
                        <?php else: ?>
                        <a href="/inventario/traslados" class="mt-1 text-gray-400 hover:text-sky-500 text-xs">
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
    /* ── Debounce de búsqueda ── */
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

    /* ── Alpine: confirmar recepción ── */
    function trasladosList() {
        return {
            async recibirTraslado(id) {
                if (!confirm('¿Confirmar recepción? El stock se actualizará en el depósito de destino.')) return;
                try {
                    const response = await fetch(`/inventario/traslados/${id}/recibir`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' }
                    });
                    const res = await response.json();
                    if (res.success) { window.location.reload(); }
                    else { alert('Error: ' + res.error); }
                } catch (error) {
                    alert('Ocurrió un error al procesar la recepción');
                }
            }
        };
    }
</script>
<?php View::endSection('extra_js'); ?>
