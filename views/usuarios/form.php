<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>
<div class="max-w-lg mx-auto">
    <form action="<?= $action ?? '/usuarios/nuevo' ?>" method="POST" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <?= View::csrf() ?>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Usuario *</label><input type="text" name="username" value="<?= View::e($usuario->username ?? '') ?>" required <?= isset($usuario) ? 'readonly class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-500 cursor-not-allowed"' : 'class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"' ?>></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label><input type="text" name="first_name" value="<?= View::e($usuario->first_name ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label><input type="text" name="last_name" value="<?= View::e($usuario->last_name ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="<?= View::e($usuario->email ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1"><?= isset($usuario) ? 'Nueva Contraseña (dejar en blanco para no cambiar)' : 'Contraseña *' ?></label><input type="password" name="password" <?= isset($usuario) ? '' : 'required' ?> class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <?php if (!isset($usuario)): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
            <select name="rol" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <?php foreach ($roles ?? ['cajero','supervisor','gerente','auditor','superadmin'] as $r): ?>
                    <option value="<?= $r ?>"><?= ucfirst($r) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_staff" value="1" <?= ($usuario->is_staff ?? false) ? 'checked' : '' ?> class="rounded"> Staff</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" <?= ($usuario->is_active ?? true) ? 'checked' : '' ?> class="rounded"> Activo</label>
        </div>
        <div class="flex justify-end gap-3 border-t pt-4">
            <a href="/usuarios" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm"><i class="fas fa-save mr-1"></i> Guardar</button>
        </div>
    </form>
</div>
<?php View::endSection('content'); ?>
