<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold">
                <?= strtoupper(substr($usuario->first_name ?? $usuario->username ?? '?', 0, 1)) ?>
            </div>
            <div>
                <h3 class="text-lg font-semibold"><?= View::e(trim(($usuario->first_name ?? '') . ' ' . ($usuario->last_name ?? '')) ?: $usuario->username) ?></h3>
                <p class="text-sm text-gray-500">@<?= View::e($usuario->username ?? '') ?></p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><span class="text-sm text-gray-500">Email</span><p class="font-medium"><?= View::e($usuario->email ?? 'N/A') ?></p></div>
            <div><span class="text-sm text-gray-500">Staff</span><p class="font-medium"><?= ($usuario->is_staff ?? false) ? 'Sí' : 'No' ?></p></div>
            <div><span class="text-sm text-gray-500">Estado</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($usuario->is_active ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= ($usuario->is_active ?? false) ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>
            <div><span class="text-sm text-gray-500">Último acceso</span><p class="font-medium"><?= $usuario->last_login ?? 'Nunca' ?></p></div>
        </div>
        <div class="flex gap-3 mt-6 border-t pt-4">
            <a href="/usuarios" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Volver</a>
            <a href="/usuarios/<?= $usuario->id ?>/editar" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
<?php View::endSection('content'); ?>
