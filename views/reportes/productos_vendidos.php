<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Filter bar -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Desde</label>
            <input type="date" name="desde" value="<?= View::e($desde) ?>"
                   class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Hasta</label>
            <input type="date" name="hasta" value="<?= View::e($hasta) ?>"
                   class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-filter"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Código</th>
                <th class="px-4 py-3">Producto</th>
                <th class="px-4 py-3 text-right">Cant. Vendida</th>
                <th class="px-4 py-3 text-right">N° Ventas</th>
                <th class="px-4 py-3 text-right">Ingreso</th>
                <th class="px-4 py-3 text-right">Costo</th>
                <th class="px-4 py-3 text-right">Ganancia</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($productos ?? []) as $i => $p): ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-4 py-3 text-gray-400 font-bold text-xs"><?= $i + 1 ?></td>
                <td class="px-4 py-3 font-mono text-xs text-gray-400"><?= View::e($p['codigo']) ?></td>
                <td class="px-4 py-3 font-medium text-gray-800"><?= View::e($p['nombre']) ?></td>
                <td class="px-4 py-3 text-right font-semibold"><?= number_format((float)$p['total_vendido'], 0) ?></td>
                <td class="px-4 py-3 text-right text-gray-500"><?= $p['num_ventas'] ?></td>
                <td class="px-4 py-3 text-right font-semibold text-emerald-600">$<?= number_format((float)$p['total_ingreso'], 2) ?></td>
                <td class="px-4 py-3 text-right text-gray-500">$<?= number_format((float)$p['total_costo'], 2) ?></td>
                <td class="px-4 py-3 text-right font-semibold text-sky-600">$<?= number_format((float)$p['total_ingreso'] - (float)$p['total_costo'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($productos)): ?>
            <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400">Sin datos</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php View::endSection('content'); ?>
