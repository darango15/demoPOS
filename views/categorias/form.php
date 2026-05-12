<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>
<div class="max-w-lg mx-auto">
    <form action="<?= $action ?? '/inventario/categorias/nueva' ?>" method="POST" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <?= View::csrf() ?>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label><input type="text" name="nombre" value="<?= View::e($categoria->nombre ?? '') ?>" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label><textarea name="descripcion" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"><?= View::e($categoria->descripcion ?? '') ?></textarea></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Categoría Padre</label>
            <select name="padre_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Ninguna (raíz)</option>
                <?php foreach (($padres ?? []) as $p): ?>
                    <option value="<?= $p->categoria_id ?>" <?= ($categoria->padre_id ?? '') == $p->categoria_id ? 'selected' : '' ?>><?= View::e($p->nombre) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex justify-end gap-3 border-t pt-4">
            <a href="/inventario/categorias" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm"><i class="fas fa-save mr-1"></i> Guardar</button>
        </div>
    </form>
</div>
<?php View::endSection('content'); ?>
