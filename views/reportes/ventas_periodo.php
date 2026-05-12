<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Filtros -->
<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap items-end gap-4">
        <div><label class="block text-xs text-gray-500 mb-1">Desde</label><input type="date" name="desde" value="<?= View::e($desde) ?>" class="border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Hasta</label><input type="date" name="hasta" value="<?= View::e($hasta) ?>" class="border rounded-lg px-3 py-2 text-sm"></div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"><i class="fas fa-filter mr-1"></i> Filtrar</button>
    </div>
</form>

<!-- Resumen -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-sm text-gray-500">Ventas</p><p class="text-xl font-bold text-gray-800"><?= number_format((int)($totales['cantidad'] ?? 0)) ?></p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-sm text-gray-500">Total</p><p class="text-xl font-bold text-green-600">$<?= number_format((float)($totales['total'] ?? 0), 2) ?></p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-sm text-gray-500">ITBMS</p><p class="text-xl font-bold text-gray-800">$<?= number_format((float)($totales['itbms'] ?? 0), 2) ?></p></div>
    <div class="bg-white rounded-xl border p-4 text-center"><p class="text-sm text-gray-500">Ganancia</p><p class="text-xl font-bold text-blue-600">$<?= number_format((float)($totales['total'] ?? 0) - (float)($totales['costo'] ?? 0), 2) ?></p></div>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr class="text-left text-xs text-gray-500 uppercase"><th class="px-4 py-3">Fecha</th><th class="px-4 py-3 text-right">Ventas</th><th class="px-4 py-3 text-right">Subtotal</th><th class="px-4 py-3 text-right">ITBMS</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3 text-right">Costo</th><th class="px-4 py-3 text-right">Ganancia</th></tr></thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($ventas ?? []) as $v): ?>
            <tr class="hover:bg-gray-50/50">
                <td class="px-4 py-3 font-medium"><?= !empty($v['fecha_emision']) ? date('d/m/Y', strtotime($v['fecha_emision'])) : 'S/F' ?></td>
                <td class="px-4 py-3 text-right"><?= $v['total_transacciones'] ?? 0 ?></td>
                <td class="px-4 py-3 text-right">$<?= number_format((float)$v['subtotal'], 2) ?></td>
                <td class="px-4 py-3 text-right text-gray-500">$<?= number_format((float)$v['itbms'], 2) ?></td>
                <td class="px-4 py-3 text-right font-semibold text-green-600">$<?= number_format((float)$v['total'], 2) ?></td>
                <td class="px-4 py-3 text-right text-gray-500">$<?= number_format((float)$v['costo'], 2) ?></td>
                <td class="px-4 py-3 text-right font-semibold text-blue-600">$<?= number_format((float)$v['total'] - (float)$v['costo'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($ventas)): ?><tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay datos para el período seleccionado</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php View::endSection('content'); ?>
