<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="/inventario/categorias/nueva" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
        <i class="fas fa-plus"></i> Nueva Categoría
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr class="text-left text-xs text-gray-500 uppercase">
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Descripción</th>
                <th class="px-4 py-3">Padre</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($categorias ?? []) as $cat): ?>
            <tr class="hover:bg-gray-50/50">
                <td class="px-4 py-3 font-medium text-gray-800"><?= View::e($cat['nombre']) ?></td>
                <td class="px-4 py-3 text-gray-500"><?= View::e($cat['descripcion'] ?? '—') ?></td>
                <td class="px-4 py-3 text-gray-500"><?= View::e($cat['padre_nombre'] ?? '—') ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= ($cat['estado'] ?? 'activo') === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>">
                        <?= ucfirst($cat['estado'] ?? 'activo') ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <a href="/inventario/categorias/<?= $cat['categoria_id'] ?>/editar" class="p-1.5 text-gray-400 hover:text-blue-600"><i class="fas fa-pen text-xs"></i></a>
                        <form action="/inventario/categorias/<?= $cat['categoria_id'] ?>/eliminar" method="POST" onsubmit="return confirm('¿Eliminar esta categoría?')">
                            <?= View::csrf() ?>
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($categorias)): ?>
            <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">No hay categorías</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php View::endSection('content'); ?>
