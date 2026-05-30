<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<?php $s = $stats ?? []; ?>

<!-- Tarjetas de métricas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-sky-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Total</p>
        <p class="text-2xl font-black text-sky-600"><?= (int)($s['total'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">clientes registrados</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-emerald-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Activos</p>
        <p class="text-2xl font-black text-emerald-600"><?= (int)($s['activos'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">en estado activo</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-gray-300">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Inactivos</p>
        <p class="text-2xl font-black text-gray-500"><?= (int)($s['inactivos'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">deshabilitados</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-violet-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Saldo Pendiente</p>
        <p class="text-2xl font-black text-violet-600">$<?= number_format((float)($s['saldo_total'] ?? 0), 2) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">total por cobrar</p>
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
                    placeholder="Código, nombre o RUC..."
                    class="w-full pl-5 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
                <div id="search-spinner" class="absolute right-0 top-2 hidden">
                    <i class="fas fa-spinner fa-spin text-sky-500 text-xs"></i>
                </div>
            </div>
        </div>

        <div class="min-w-28">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Estado</label>
            <select name="estado" onchange="this.form.submit()"
                class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todos</option>
                <option value="activo" <?= ($estado_filtro ?? '') === 'activo' ? 'selected' : '' ?>>Activos</option>
                <option value="inactivo" <?= ($estado_filtro ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
            </select>
        </div>

        <div class="min-w-28">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tipo</label>
            <select name="tipo" onchange="this.form.submit()"
                class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todos</option>
                <option value="natural" <?= ($tipo_filtro ?? '') === 'natural' ? 'selected' : '' ?>>Natural</option>
                <option value="juridico" <?= ($tipo_filtro ?? '') === 'juridico' ? 'selected' : '' ?>>Jurídico</option>
            </select>
        </div>

        <div class="min-w-24">
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
                <?= number_format($pagination['total'] ?? 0) ?> cliente(s)
                <?php if (!empty($buscar) || !empty($estado_filtro) || !empty($tipo_filtro)): ?>
                <span class="text-sky-500">— filtrado</span>
                <?php endif; ?>
            </span>
            <?php if (!empty($buscar) || !empty($estado_filtro) || !empty($tipo_filtro)): ?>
            <a href="/clientes"
                class="inline-flex items-center gap-1 px-3 py-2 border border-gray-200 text-gray-400 rounded-lg text-sm hover:bg-gray-50 transition"
                title="Limpiar filtros">
                <i class="fas fa-times"></i>
            </a>
            <?php endif; ?>
            <a href="/clientes/nuevo"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-user-plus"></i> Nuevo Cliente
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xs font-medium text-gray-500">Ordenado por nombre</span>
        <span class="text-xs text-gray-400">
            Pág. <?= $pagination['current_page'] ?? 1 ?> / <?= $pagination['total_pages'] ?? 1 ?>
        </span>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                <th class="px-4 py-3 bg-white">Cliente</th>
                <th class="px-4 py-3 bg-white">Código / RUC</th>
                <th class="px-4 py-3 bg-white">Contacto</th>
                <th class="px-4 py-3 bg-white text-center">Tipo</th>
                <th class="px-4 py-3 bg-white text-center">Estado</th>
                <th class="px-4 py-3 bg-white text-right">Saldo</th>
                <th class="px-4 py-3 bg-white text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($clientes ?? []) as $cliente): ?>
            <tr class="hover:bg-sky-50/40 transition-colors group">

                <!-- Cliente -->
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-sky-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800"><?= View::e($cliente->nombre) ?></p>
                            <p class="text-xs text-gray-400"><?= View::e($cliente->email ?: '—') ?></p>
                        </div>
                    </div>
                </td>

                <!-- Código / RUC -->
                <td class="px-4 py-3">
                    <p class="text-xs font-mono text-gray-500"><?= View::e($cliente->codigo ?: '—') ?></p>
                    <?php if (!empty($cliente->ruc)): ?>
                    <p class="text-xs text-gray-400">RUC: <?= View::e($cliente->ruc) ?></p>
                    <?php endif; ?>
                </td>

                <!-- Contacto -->
                <td class="px-4 py-3 text-sm text-gray-600">
                    <?= View::e($cliente->telefono ?: '—') ?>
                </td>

                <!-- Tipo -->
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border
                        <?= ($cliente->tipo ?? '') === 'juridico'
                            ? 'bg-violet-50 text-violet-700 border-violet-100'
                            : 'bg-sky-50 text-sky-700 border-sky-100' ?>">
                        <?= ($cliente->tipo ?? '') === 'juridico' ? 'Jurídico' : 'Natural' ?>
                    </span>
                </td>

                <!-- Estado -->
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border
                        <?= $cliente->estado === 'activo'
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                            : 'bg-red-50 text-red-600 border-red-100' ?>">
                        <?= ucfirst(View::e($cliente->estado)) ?>
                    </span>
                </td>

                <!-- Saldo -->
                <td class="px-4 py-3 text-right text-sm font-semibold
                    <?= ((float)($cliente->saldo ?? 0)) > 0 ? 'text-red-600' : 'text-gray-500' ?>">
                    $<?= number_format((float)($cliente->saldo ?? 0), 2) ?>
                </td>

                <!-- Acciones -->
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                        <a href="/clientes/<?= $cliente->cliente_id ?>" class="text-blue-500 hover:text-blue-700" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/clientes/<?= $cliente->cliente_id ?>/editar" class="text-gray-400 hover:text-blue-600" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/ventas/nueva?cliente=<?= $cliente->cliente_id ?>" class="text-emerald-500 hover:text-emerald-700" title="Nueva Venta">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                        <form method="post" action="/clientes/<?= $cliente->cliente_id ?>/eliminar" class="inline"
                              onsubmit="return confirm('¿Eliminar a <?= View::e(addslashes($cliente->nombre)) ?>?')">
                            <?= View::csrf() ?>
                            <button type="submit" class="text-gray-300 hover:text-red-500" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="7" class="px-4 py-16 text-center">
                    <div class="inline-flex flex-col items-center gap-2">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-users text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-400">No se encontraron clientes</p>
                        <?php if (empty($buscar) && empty($estado_filtro) && empty($tipo_filtro)): ?>
                        <a href="/clientes/nuevo" class="mt-1 text-sky-500 hover:underline text-sm font-medium">
                            Registrar el primer cliente
                        </a>
                        <?php else: ?>
                        <a href="/clientes" class="mt-1 text-gray-400 hover:text-sky-500 text-xs">
                            Limpiar filtros
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
                if (spinner) spinner.classList.remove('hidden');
                searchTimeout = setTimeout(function() {
                    document.getElementById('search-form').submit();
                }, 500);
            }
        });
    }
</script>
<?php View::endSection('extra_js'); ?>
