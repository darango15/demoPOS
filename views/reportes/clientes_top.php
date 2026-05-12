<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr class="text-left text-xs text-gray-500 uppercase"><th class="px-4 py-3">#</th><th class="px-4 py-3">Código</th><th class="px-4 py-3">Cliente</th><th class="px-4 py-3">Tipo</th><th class="px-4 py-3 text-right">N° Compras</th><th class="px-4 py-3 text-right">Total Compras</th><th class="px-4 py-3">Última Compra</th></tr></thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach (($clientes ?? []) as $i => $c): ?>
            <tr class="hover:bg-gray-50/50">
                <td class="px-4 py-3 text-gray-400 font-bold"><?= $i + 1 ?></td>
                <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= View::e($c['codigo']) ?></td>
                <td class="px-4 py-3 font-medium text-gray-800"><?= View::e($c['nombre']) ?></td>
                <td class="px-4 py-3 text-gray-500 capitalize"><?= View::e($c['tipo']) ?></td>
                <td class="px-4 py-3 text-right font-semibold"><?= $c['num_compras'] ?></td>
                <td class="px-4 py-3 text-right font-bold text-green-600">$<?= number_format((float)$c['total_compras'], 2) ?></td>
                <td class="px-4 py-3 text-gray-400 text-xs"><?= $c['ultima_compra'] ? date('d/m/Y', strtotime($c['ultima_compra'])) : '—' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($clientes)): ?><tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Sin datos</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php View::endSection('content'); ?>
