<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap items-end gap-4">
        <div><label class="block text-xs text-gray-500 mb-1">Desde</label><input type="date" name="desde" value="<?= View::e($desde) ?>" class="border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Hasta</label><input type="date" name="hasta" value="<?= View::e($hasta) ?>" class="border rounded-lg px-3 py-2 text-sm"></div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"><i class="fas fa-filter mr-1"></i> Filtrar</button>
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr class="text-left text-xs text-gray-500 uppercase"><th class="px-4 py-3">#</th><th class="px-4 py-3">Código</th><th class="px-4 py-3">Producto</th><th class="px-4 py-3 text-right">Cant. Vendida</th><th class="px-4 py-3 text-right">N° Ventas</th><th class="px-4 py-3 text-right">Ingreso</th><th class="px-4 py-3 text-right">Costo</th><th class="px-4 py-3 text-right">Ganancia</th></tr></thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($productos ?? []) as $i => $p): ?>
            <tr class="hover:bg-gray-50/50">
                <td class="px-4 py-3 text-gray-400 font-bold"><?= $i + 1 ?></td>
                <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= View::e($p['codigo']) ?></td>
                <td class="px-4 py-3 font-medium text-gray-800"><?= View::e($p['nombre']) ?></td>
                <td class="px-4 py-3 text-right font-semibold"><?= number_format((float)$p['total_vendido'], 0) ?></td>
                <td class="px-4 py-3 text-right text-gray-500"><?= $p['num_ventas'] ?></td>
                <td class="px-4 py-3 text-right font-medium text-green-600">$<?= number_format((float)$p['total_ingreso'], 2) ?></td>
                <td class="px-4 py-3 text-right text-gray-500">$<?= number_format((float)$p['total_costo'], 2) ?></td>
                <td class="px-4 py-3 text-right font-semibold text-blue-600">$<?= number_format((float)$p['total_ingreso'] - (float)$p['total_costo'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($productos)): ?><tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Sin datos</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php View::endSection('content'); ?>
