<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Formulario nueva marca -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Nueva Marca</h3>
            <form action="/inventario/marcas/guardar" method="POST" class="space-y-4">
                <?= View::csrf() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" required placeholder="Ej: Bayer, Colgate..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition">
                    <i class="fas fa-plus mr-1"></i> Agregar Marca
                </button>
            </form>
        </div>
    </div>

    <!-- Listado de marcas -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-700">Marcas registradas (<?= count($marcas) ?>)</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3 text-center">Productos</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($marcas as $m): ?>
                    <tr class="hover:bg-gray-50 group" id="row-<?= $m['marca_id'] ?>">
                        <td class="px-4 py-3">
                            <!-- Vista normal -->
                            <span class="nombre-texto font-medium text-gray-800"><?= View::e($m['nombre']) ?></span>
                            <!-- Formulario edición inline (oculto) -->
                            <form class="nombre-form hidden" action="/inventario/marcas/<?= $m['marca_id'] ?>/actualizar" method="POST">
                                <?= View::csrf() ?>
                                <div class="flex gap-2">
                                    <input type="text" name="nombre" value="<?= View::e($m['nombre']) ?>"
                                           class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-sky-500 flex-1">
                                    <button type="submit" class="px-3 py-1 bg-sky-500 text-white rounded-lg text-xs font-semibold hover:bg-sky-600">Guardar</button>
                                    <button type="button" onclick="cancelarEdicion(<?= $m['marca_id'] ?>)" class="px-3 py-1 border border-gray-300 text-gray-600 rounded-lg text-xs hover:bg-gray-50">Cancelar</button>
                                </div>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-medium"><?= (int)$m['total_productos'] ?></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="editarMarca(<?= $m['marca_id'] ?>)" class="text-sky-500 hover:text-sky-700 text-sm" title="Editar">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <?php if ((int)$m['total_productos'] === 0): ?>
                                <form action="/inventario/marcas/<?= $m['marca_id'] ?>/eliminar" method="POST" onsubmit="return confirm('¿Eliminar esta marca?')">
                                    <?= View::csrf() ?>
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="text-gray-300 text-sm" title="Tiene productos asignados"><i class="fas fa-trash"></i></span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($marcas)): ?>
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400 text-sm">No hay marcas registradas</td></tr>
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
