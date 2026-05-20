<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4 flex justify-between items-center">
    <span class="text-sm text-gray-400"><?= count($categorias ?? []) ?> categoría(s)</span>
    <a href="/inventario/categorias/nueva" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
        <i class="fas fa-plus"></i> Nueva Categoría
    </a>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Descripción</th>
                <th class="px-4 py-3">Padre</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($categorias ?? []) as $cat): ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-4 py-3 font-semibold text-gray-800"><?= View::e($cat['nombre']) ?></td>
                <td class="px-4 py-3 text-gray-500 text-xs"><?= View::e($cat['descripcion'] ?? '—') ?></td>
                <td class="px-4 py-3 text-gray-500 text-xs"><?= View::e($cat['padre_nombre'] ?? '—') ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= ($cat['estado'] ?? 'activo') === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>">
                        <?= ucfirst($cat['estado'] ?? 'activo') ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="/inventario/categorias/<?= $cat['categoria_id'] ?>/editar" class="text-gray-400 hover:text-blue-600" title="Editar"><i class="fas fa-pen text-xs"></i></a>
                        <form action="/inventario/categorias/<?= $cat['categoria_id'] ?>/eliminar" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                            <?= View::csrf() ?>
                            <button type="submit" class="text-gray-300 hover:text-red-600" title="Eliminar"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($categorias)): ?>
            <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">No hay categorías registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php View::endSection('content'); ?>
