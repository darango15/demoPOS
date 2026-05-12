<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><?= View::e($producto->nombre ?? '') ?></h3>
        <form action="/inventario/<?= $producto->producto_id ?>/precios" method="POST" class="space-y-4">
            <?= View::csrf() ?>
            <?php $tipoLabels = ['a' => 'Precio A (Principal)', 'b' => 'Precio B', 'promocional' => 'Promocional']; ?>
            <?php foreach ($tipoLabels as $tipo => $label): ?>
                <?php $precioActual = ''; foreach (($precios ?? []) as $p) { if (($p->tipo_precio ?? $p['tipo_precio'] ?? '') === $tipo) { $precioActual = $p->precio ?? $p['precio'] ?? ''; break; } } ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= $label ?></label>
                    <input type="number" step="0.01" name="precio_<?= $tipo ?>" value="<?= $precioActual ?>" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500" placeholder="0.00">
                </div>
            <?php endforeach; ?>
            <div class="flex justify-end gap-3 border-t pt-4">
                <a href="/inventario/<?= $producto->producto_id ?>" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm"><i class="fas fa-save mr-1"></i> Guardar Precios</button>
            </div>
        </form>
    </div>
</div>
<?php View::endSection('content'); ?>
