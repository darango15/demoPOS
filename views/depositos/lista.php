<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4 flex justify-between items-center">
    <span class="text-sm text-gray-400"><?= count($depositos ?? []) ?> depósito(s)</span>
    <a href="/inventario/depositos/nuevo" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
        <i class="fas fa-plus"></i> Nuevo Depósito
    </a>
</div>

<!-- Cards de depósitos -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach (($depositos ?? []) as $dep): ?>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-bold text-gray-800"><?= View::e($dep['nombre']) ?></h3>
                <p class="text-xs text-gray-400 font-mono"><?= View::e($dep['codigo']) ?></p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= ($dep['estado'] ?? 'activo') === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>">
                <?= ucfirst($dep['estado'] ?? 'activo') ?>
            </span>
        </div>
        <?php if (!empty($dep['descripcion'])): ?>
        <p class="text-xs text-gray-500 mb-3 line-clamp-1"><?= View::e($dep['descripcion']) ?></p>
        <?php endif; ?>
        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
            <a href="/inventario/depositos/<?= $dep['deposito_id'] ?>" class="text-sm text-sky-600 hover:text-sky-700 font-semibold">
                Ver inventario →
            </a>
            <a href="/inventario/depositos/<?= $dep['deposito_id'] ?>/editar" class="text-gray-400 hover:text-blue-600" title="Editar">
                <i class="fas fa-pen text-xs"></i>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($depositos)): ?>
    <div class="col-span-full text-center py-12 text-gray-400">
        <i class="fas fa-warehouse text-3xl mb-2 block text-gray-200"></i>
        <p class="text-sm">No hay depósitos registrados.</p>
    </div>
    <?php endif; ?>
</div>

<?php View::endSection('content'); ?>
