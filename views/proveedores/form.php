<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>
<div class="max-w-lg mx-auto">
    <form action="<?= $action ?? '/inventario/proveedores/nuevo' ?>" method="POST" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <?= View::csrf() ?>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Código *</label><input type="text" name="codigo" value="<?= View::e($proveedor->codigo ?? '') ?>" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label><input type="text" name="nombre" value="<?= View::e($proveedor->nombre ?? '') ?>" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">RUC</label><input type="text" name="ruc" value="<?= View::e($proveedor->ruc ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Contacto</label><input type="text" name="contacto" value="<?= View::e($proveedor->contacto ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label><input type="text" name="telefono" value="<?= View::e($proveedor->telefono ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="<?= View::e($proveedor->email ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label><textarea name="direccion" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"><?= View::e($proveedor->direccion ?? '') ?></textarea></div>
        <div class="flex justify-end gap-3 border-t pt-4">
            <a href="/inventario/proveedores" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm"><i class="fas fa-save mr-1"></i> Guardar</button>
        </div>
    </form>
</div>
<?php View::endSection('content'); ?>
