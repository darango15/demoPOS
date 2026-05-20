<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    <!-- Nueva Marca -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Nueva Marca</h3>
            <form action="/inventario/marcas/guardar" method="POST">
                <?= View::csrf() ?>
                <div class="flex items-baseline gap-4 py-2 mb-4">
                    <label class="text-sm font-semibold text-gray-600 w-20 shrink-0">Nombre *</label>
                    <input type="text" name="nombre" required placeholder="Ej: Bayer, Colgate..."
                           class="flex-1 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                    <i class="fas fa-plus"></i> Agregar Marca
                </button>
            </form>
        </div>
    </div>

    <!-- Listado de marcas -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-2.5 border-b border-gray-50">
                <span class="text-xs text-gray-400"><?= count($marcas) ?> marca(s)</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3 text-center">Productos</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($marcas as $m): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors" id="row-<?= $m['marca_id'] ?>">
                        <td class="px-4 py-3">
                            <span class="nombre-texto font-medium text-gray-800"><?= View::e($m['nombre']) ?></span>
                            <form class="nombre-form hidden" action="/inventario/marcas/<?= $m['marca_id'] ?>/actualizar" method="POST">
                                <?= View::csrf() ?>
                                <div class="flex gap-2">
                                    <input type="text" name="nombre" value="<?= View::e($m['nombre']) ?>"
                                           class="flex-1 py-1 px-0 text-sm bg-transparent border-0 border-b border-sky-400 focus:ring-0 outline-none">
                                    <button type="submit" class="px-3 py-1 bg-sky-500 text-white rounded-lg text-xs font-semibold hover:bg-sky-600">Guardar</button>
                                    <button type="button" onclick="cancelarEdicion(<?= $m['marca_id'] ?>)" class="px-3 py-1 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50">Cancelar</button>
                                </div>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-medium"><?= (int)$m['total_productos'] ?></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="editarMarca(<?= $m['marca_id'] ?>)" class="text-gray-400 hover:text-blue-600" title="Editar">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                <?php if ((int)$m['total_productos'] === 0): ?>
                                <form action="/inventario/marcas/<?= $m['marca_id'] ?>/eliminar" method="POST" onsubmit="return confirm('¿Eliminar esta marca?')">
                                    <?= View::csrf() ?>
                                    <button type="submit" class="text-gray-300 hover:text-red-600" title="Eliminar">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="text-gray-200" title="Tiene productos asignados"><i class="fas fa-trash text-xs"></i></span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($marcas)): ?>
                    <tr><td colspan="3" class="px-4 py-10 text-center text-sm text-gray-400">No hay marcas registradas</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php View::endSection('content'); ?>
<?php View::section('extra_js'); ?>
<script>
function editarMarca(id) {
    document.querySelector(`#row-${id} .nombre-texto`).classList.add('hidden');
    document.querySelector(`#row-${id} .nombre-form`).classList.remove('hidden');
}
function cancelarEdicion(id) {
    document.querySelector(`#row-${id} .nombre-texto`).classList.remove('hidden');
    document.querySelector(`#row-${id} .nombre-form`).classList.add('hidden');
}
</script>
<?php View::endSection('extra_js'); ?>
