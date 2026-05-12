<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="/inventario/depositos/nuevo" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
        <i class="fas fa-plus"></i> Nuevo Depósito
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach (($depositos ?? []) as $dep): ?>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-800"><?= View::e($dep['nombre']) ?></h3>
                <p class="text-xs text-gray-400 font-mono"><?= View::e($dep['codigo']) ?></p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= ($dep['estado'] ?? 'activo') === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>">
                <?= ucfirst($dep['estado'] ?? 'activo') ?>
            </span>
        </div>
        <?php if (!empty($dep['descripcion'])): ?><p class="text-sm text-gray-500 mb-3 line-clamp-1"><i class="fas fa-info-circle text-xs mr-1 opacity-50"></i><?= View::e($dep['descripcion']) ?></p><?php endif; ?>
        <div class="flex items-center justify-between pt-3 border-t">
            <a href="/inventario/depositos/<?= $dep['deposito_id'] ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Ver inventario →</a>
            <div class="flex gap-1">
                <a href="/inventario/depositos/<?= $dep['deposito_id'] ?>/editar" class="p-1.5 text-gray-400 hover:text-blue-600"><i class="fas fa-pen text-xs"></i></a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($depositos)): ?>
    <div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-warehouse text-3xl mb-2"></i><p>No hay depósitos</p></div>
    <?php endif; ?>
</div>

<?php View::endSection('content'); ?>
