<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Filtros + acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-56">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar</label>
            <div class="relative">
                <i class="fas fa-search absolute left-0 top-2 text-gray-400 text-xs"></i>
                <input type="text" name="buscar" value="<?= View::e($buscar ?? '') ?>" placeholder="Buscar proveedor..."
                       class="w-full pl-5 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="/inventario/proveedores/nuevo" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">
                <i class="fas fa-plus"></i> Nuevo Proveedor
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <th class="px-4 py-3">Código</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Contacto</th>
                <th class="px-4 py-3">Teléfono</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($proveedores ?? []) as $prov): ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-4 py-3 font-mono text-xs text-gray-400"><?= View::e($prov['codigo']) ?></td>
                <td class="px-4 py-3 font-semibold text-gray-800"><?= View::e($prov['nombre']) ?></td>
                <td class="px-4 py-3 text-gray-500 text-xs"><?= View::e($prov['contacto'] ?? '—') ?></td>
                <td class="px-4 py-3 text-gray-500 text-xs"><?= View::e($prov['telefono'] ?? '—') ?></td>
                <td class="px-4 py-3 text-gray-500 text-xs"><?= View::e($prov['email'] ?? '—') ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= ($prov['estado'] ?? 'activo') === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>">
                        <?= ucfirst($prov['estado'] ?? 'activo') ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="/inventario/proveedores/<?= $prov['proveedor_id'] ?>/evaluaciones" class="text-amber-400 hover:text-amber-600" title="Evaluaciones"><i class="fas fa-star text-xs"></i></a>
                        <a href="/inventario/proveedores/<?= $prov['proveedor_id'] ?>/editar" class="text-gray-400 hover:text-blue-600" title="Editar"><i class="fas fa-pen text-xs"></i></a>
                        <form action="/inventario/proveedores/<?= $prov['proveedor_id'] ?>/eliminar" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este proveedor?')">
                            <?= View::csrf() ?>
                            <button class="text-gray-300 hover:text-red-600" title="Eliminar"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($proveedores)): ?>
            <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">No hay proveedores registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>

<?php View::endSection('content'); ?>
