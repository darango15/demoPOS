<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="flex items-center justify-between mb-6">
    <form method="GET" class="relative">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-search text-sm"></i></span>
        <input type="text" name="buscar" value="<?= View::e($buscar ?? '') ?>" placeholder="Buscar usuario..." class="pl-9 pr-4 py-2 border rounded-lg text-sm">
    </form>
    <a href="/usuarios/nuevo" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr class="text-left text-xs text-gray-500 uppercase"><th class="px-4 py-3">Usuario</th><th class="px-4 py-3">Nombre</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Staff</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Último Acceso</th><th class="px-4 py-3 text-center">Acciones</th></tr></thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($usuarios ?? []) as $u): ?>
            <tr class="hover:bg-gray-50/50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold text-xs"><?= strtoupper(substr($u['username'], 0, 1)) ?></div>
                        <span class="font-medium text-gray-800"><?= View::e($u['username']) ?></span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600"><?= View::e(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?: '—' ?></td>
                <td class="px-4 py-3 text-gray-500"><?= View::e($u['email'] ?? '—') ?></td>
                <td class="px-4 py-3"><?= $u['is_staff'] ? '<i class="fas fa-shield text-green-500"></i>' : '<i class="fas fa-user text-gray-400"></i>' ?></td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $u['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>"><?= $u['is_active'] ? 'Activo' : 'Inactivo' ?></span></td>
                <td class="px-4 py-3 text-gray-400 text-xs"><?= !empty($u['last_login']) ? date('d/m/Y H:i', strtotime($u['last_login'])) : 'Nunca' ?></td>
                <td class="px-4 py-3 text-center">
                    <a href="/usuarios/<?= $u['id'] ?>/editar" class="p-1.5 text-gray-400 hover:text-blue-600"><i class="fas fa-pen text-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($usuarios)): ?><tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No hay usuarios</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
<?php View::endSection('content'); ?>
