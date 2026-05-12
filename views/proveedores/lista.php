<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="flex items-center justify-between mb-6">
    <form method="GET" class="relative">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-search text-sm"></i></span>
        <input type="text" name="buscar" value="<?= View::e($buscar ?? '') ?>" placeholder="Buscar proveedor..."
            class="pl-9 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500-500">
    </form>
    <a href="/inventario/proveedores/nuevo" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
        <i class="fas fa-plus"></i> Nuevo Proveedor
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr class="text-left text-xs text-gray-500 uppercase"><th class="px-4 py-3">Código</th><th class="px-4 py-3">Nombre</th><th class="px-4 py-3">Contacto</th><th class="px-4 py-3">Teléfono</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3 text-center">Acciones</th></tr></thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($proveedores ?? []) as $prov): ?>
            <tr class="hover:bg-gray-50/50">
                <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= View::e($prov['codigo']) ?></td>
                <td class="px-4 py-3 font-medium text-gray-800"><?= View::e($prov['nombre']) ?></td>
                <td class="px-4 py-3 text-gray-500"><?= View::e($prov['contacto'] ?? '—') ?></td>
                <td class="px-4 py-3 text-gray-500"><?= View::e($prov['telefono'] ?? '—') ?></td>
                <td class="px-4 py-3 text-gray-500"><?= View::e($prov['email'] ?? '—') ?></td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium <?= ($prov['estado'] ?? 'activo') === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>"><?= ucfirst($prov['estado'] ?? 'activo') ?></span></td>
                <td class="px-4 py-3 text-center">
                    <a href="/inventario/proveedores/<?= $prov['proveedor_id'] ?>/evaluaciones" class="p-1.5 text-gray-400 hover:text-amber-600" title="Evaluaciones"><i class="fas fa-star text-xs"></i></a>
                    <a href="/inventario/proveedores/<?= $prov['proveedor_id'] ?>/editar" class="p-1.5 text-gray-400 hover:text-blue-600"><i class="fas fa-pen text-xs"></i></a>
                    <form action="/inventario/proveedores/<?= $prov['proveedor_id'] ?>/eliminar" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este proveedor?')"><?= View::csrf() ?><button class="p-1.5 text-gray-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button></form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($proveedores)): ?><tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No hay proveedores</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
<?php View::endSection('content'); ?>
