<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Filtros + acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-56">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar</label>
            <div class="relative">
                <i class="fas fa-search absolute left-0 top-2 text-gray-400 text-xs"></i>
                <input type="text" name="buscar" value="<?= View::e($buscar ?? '') ?>" placeholder="Buscar usuario..."
                       class="w-full pl-5 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="/usuarios/nuevo" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <th class="px-4 py-3">Usuario</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Staff</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Último Acceso</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($usuarios ?? []) as $u): ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center text-sky-700 font-bold text-xs">
                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                        </div>
                        <span class="font-semibold text-gray-800"><?= View::e($u['username']) ?></span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600"><?= View::e(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?: '—' ?></td>
                <td class="px-4 py-3 text-gray-500 text-xs"><?= View::e($u['email'] ?? '—') ?></td>
                <td class="px-4 py-3">
                    <?php if ($u['is_staff']): ?>
                        <i class="fas fa-shield-alt text-sky-500" title="Staff"></i>
                    <?php else: ?>
                        <i class="fas fa-user text-gray-300"></i>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $u['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' ?>">
                        <?= $u['is_active'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">
                    <?= !empty($u['last_login']) ? date('d/m/Y H:i', strtotime($u['last_login'])) : 'Nunca' ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="/usuarios/<?= $u['id'] ?>/editar" class="text-gray-400 hover:text-blue-600" title="Editar">
                        <i class="fas fa-pen text-xs"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($usuarios)): ?>
            <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
</div>

<?php View::endSection('content'); ?>
