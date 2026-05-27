<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<?php $s = $stats ?? []; $marcas = $marcas ?? []; $pagination = $pagination ?? []; ?>

<!-- Tarjetas de métricas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-sky-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Total</p>
        <p class="text-2xl font-black text-sky-600"><?= (int)($s['total'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">marcas registradas</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-emerald-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Con productos</p>
        <p class="text-2xl font-black text-emerald-600"><?= (int)($s['con_productos'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">en uso</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-gray-300">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Sin productos</p>
        <p class="text-2xl font-black text-gray-500"><?= max(0, (int)($s['total'] ?? 0) - (int)($s['con_productos'] ?? 0)) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">sin asignar</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-violet-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Productos</p>
        <p class="text-2xl font-black text-violet-600"><?= (int)($s['total_asignados'] ?? 0) ?></p>
        <p class="text-xs text-gray-400 mt-0.5">con marca asignada</p>
    </div>
</div>

<!-- Barra de filtros + nueva marca -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="get" id="search-form" class="flex flex-wrap items-end gap-3">

        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar</label>
            <div class="relative">
                <i class="fas fa-search absolute left-0 top-2 text-gray-400 text-xs"></i>
                <input type="text" name="buscar" id="search-input"
                    value="<?= View::e($_GET['buscar'] ?? '') ?>"
                    placeholder="Nombre de marca..."
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

        <div class="flex gap-2">
            <?php if (!empty($_GET['buscar'])): ?>
            <a href="/inventario/marcas"
                class="inline-flex items-center gap-1 px-3 py-2 border border-gray-200 text-gray-400 rounded-lg text-sm hover:bg-gray-50 transition"
                title="Limpiar búsqueda">
                <i class="fas fa-times"></i>
            </a>
            <?php endif; ?>
            <button type="button" onclick="toggleNuevaMarca()"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-plus"></i> Nueva Marca
            </button>
        </div>
    </form>
</div>

<!-- Panel deslizable: Nueva Marca -->
<div id="panel-nueva-marca" class="hidden mb-4">
    <div class="bg-white rounded-xl border border-sky-200 shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-certificate text-sky-400 text-xs"></i> Nueva Marca
            </h3>
            <button type="button" onclick="toggleNuevaMarca()" class="text-gray-300 hover:text-gray-500 text-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="/inventario/marcas/guardar" method="POST" class="flex items-end gap-3">
            <?= View::csrf() ?>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Nombre *</label>
                <input type="text" name="nombre" required autofocus
                    placeholder="Ej: Bosch, Stanley, Truper..."
                    class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-300 focus:border-sky-400 focus:ring-0 outline-none">
            </div>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm shrink-0">
                <i class="fas fa-save"></i> Guardar
            </button>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xs font-medium text-gray-500">
            <?= number_format($pagination['total'] ?? 0) ?> marca(s)
            <?php if (!empty($_GET['buscar'])): ?>
            <span class="ml-1 text-sky-500">— filtrado</span>
            <?php endif; ?>
        </span>
        <span class="text-xs text-gray-400">
            Pág. <?= $pagination['current_page'] ?? 1 ?> / <?= $pagination['total_pages'] ?? 1 ?>
        </span>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                <th class="px-4 py-3 bg-white">Marca</th>
                <th class="px-4 py-3 bg-white text-center">Productos</th>
                <th class="px-4 py-3 bg-white text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($marcas ?? []) as $m): ?>
            <?php $totalProd = (int)($m['total_productos'] ?? 0); ?>
            <tr class="hover:bg-sky-50/40 transition-colors group" id="row-<?= $m['marca_id'] ?>">

                <!-- Nombre + edición inline -->
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-certificate text-sky-400 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <!-- Modo lectura -->
                            <p class="nombre-texto font-semibold text-gray-800 group-hover:text-sky-700 transition-colors">
                                <?= View::e($m['nombre']) ?>
                            </p>
                            <!-- Modo edición -->
                            <form class="nombre-form hidden" action="/inventario/marcas/<?= $m['marca_id'] ?>/actualizar" method="POST">
                                <?= View::csrf() ?>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="nombre" value="<?= View::e($m['nombre']) ?>"
                                        class="flex-1 py-1 px-0 text-sm bg-transparent border-0 border-b border-sky-400 focus:ring-0 outline-none font-semibold">
                                    <button type="submit"
                                        class="px-3 py-1 bg-sky-500 text-white rounded-lg text-xs font-semibold hover:bg-sky-600 transition shrink-0">
                                        Guardar
                                    </button>
                                    <button type="button" onclick="cancelarEdicion(<?= $m['marca_id'] ?>)"
                                        class="px-3 py-1 border border-gray-200 text-gray-500 rounded-lg text-xs hover:bg-gray-50 transition shrink-0">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>

                <!-- Total productos -->
                <td class="px-4 py-3 text-center">
                    <?php if ($totalProd > 0): ?>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 min-w-[2rem]">
                        <?= $totalProd ?>
                    </span>
                    <?php else: ?>
                    <span class="text-gray-300 text-xs">0</span>
                    <?php endif; ?>
                </td>

                <!-- Acciones -->
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-3 opacity-60 group-hover:opacity-100 transition-opacity">
                        <button type="button" onclick="editarMarca(<?= $m['marca_id'] ?>)"
                            class="text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <?php if ($totalProd === 0): ?>
                        <form action="/inventario/marcas/<?= $m['marca_id'] ?>/eliminar" method="POST"
                            onsubmit="return confirm('¿Eliminar la marca «<?= View::e(addslashes($m['nombre'])) ?>»?')">
                            <?= View::csrf() ?>
                            <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors" title="Eliminar">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                        <?php else: ?>
                        <span class="text-gray-200 cursor-not-allowed" title="Tiene productos asignados">
                            <i class="fas fa-trash text-xs"></i>
                        </span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($marcas)): ?>
            <tr>
                <td colspan="3" class="px-4 py-16 text-center">
                    <div class="inline-flex flex-col items-center gap-2">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-certificate text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-400">No se encontraron marcas</p>
                        <?php if (empty($_GET['buscar'])): ?>
                        <button type="button" onclick="toggleNuevaMarca()"
                            class="mt-1 text-sky-500 hover:underline text-sm font-medium">
                            Crear la primera marca
                        </button>
                        <?php else: ?>
                        <a href="/inventario/marcas" class="mt-1 text-gray-400 hover:text-sky-500 text-xs">
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
    <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
</div>
<?php endif; ?>

<?php View::endSection('content'); ?>

<?php View::section('extra_js'); ?>
<script>
    /* ── Buscador con debounce ── */
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

    /* ── Panel nueva marca ── */
    function toggleNuevaMarca() {
        var panel = document.getElementById('panel-nueva-marca');
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            panel.querySelector('input[name="nombre"]').focus();
        }
    }

    /* ── Edición inline ── */
    function editarMarca(id) {
        document.querySelector('#row-' + id + ' .nombre-texto').classList.add('hidden');
        document.querySelector('#row-' + id + ' .nombre-form').classList.remove('hidden');
        document.querySelector('#row-' + id + ' .nombre-form input[name="nombre"]').focus();
    }

    function cancelarEdicion(id) {
        document.querySelector('#row-' + id + ' .nombre-texto').classList.remove('hidden');
        document.querySelector('#row-' + id + ' .nombre-form').classList.add('hidden');
    }
</script>
<?php View::endSection('extra_js'); ?>
