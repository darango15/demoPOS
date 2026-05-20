<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Métricas -->
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <p class="text-xs font-semibold text-gray-500">Total Productos</p>
        <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format($totalProductos) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
        <p class="text-xs font-semibold text-gray-500">Valor de Inventario</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">$<?= number_format($valorTotal, 2) ?></p>
    </div>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <th class="px-4 py-3">Código</th>
                <th class="px-4 py-3">Producto</th>
                <th class="px-4 py-3">Categoría</th>
                <th class="px-4 py-3 text-right">Stock</th>
                <th class="px-4 py-3 text-right">Mín.</th>
                <th class="px-4 py-3 text-right">Costo Prom.</th>
                <th class="px-4 py-3 text-right">Valor Total</th>
                <th class="px-4 py-3">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($productos ?? []) as $p): ?>
            <?php $stock = (float)$p['stock_total']; $min = (float)$p['stock_minimo']; ?>
            <tr class="hover:bg-gray-50/50 transition-colors <?= $stock <= 0 ? 'bg-red-50/50' : ($stock <= $min ? 'bg-orange-50/50' : '') ?>">
                <td class="px-4 py-3 font-mono text-xs text-gray-400"><?= View::e($p['codigo']) ?></td>
                <td class="px-4 py-3 font-medium text-gray-800"><?= View::e($p['nombre']) ?></td>
                <td class="px-4 py-3 text-gray-500 text-xs"><?= View::e($p['categoria'] ?? '—') ?></td>
                <td class="px-4 py-3 text-right font-semibold"><?= number_format($stock) ?></td>
                <td class="px-4 py-3 text-right text-gray-500"><?= number_format($min) ?></td>
                <td class="px-4 py-3 text-right text-gray-500">$<?= number_format((float)$p['costo_promedio'], 2) ?></td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800">$<?= number_format((float)$p['valor_total'], 2) ?></td>
                <td class="px-4 py-3">
                    <?php if ($stock <= 0): ?>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Agotado</span>
                    <?php elseif ($stock <= $min): ?>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Bajo</span>
                    <?php else: ?>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">OK</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php View::endSection('content'); ?>
