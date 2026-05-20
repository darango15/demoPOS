<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Filtros + acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <div class="flex flex-wrap items-center gap-3 justify-between">
        <div class="flex items-center gap-3 flex-1 flex-wrap">
            <span class="text-sm text-gray-500"><?= count($compras) ?> orden(es)</span>
        </div>
        <a href="/compras/nueva" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
            <i class="fas fa-plus"></i> Nueva Compra
        </a>
    </div>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Factura #</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Proveedor</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Monto Total</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            <?php foreach ($compras as $compra): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-800"><?= htmlspecialchars($compra['numero_factura']) ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($compra['proveedor_nombre']) ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">$<?= number_format((float)$compra['monto_total'], 2) ?></td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <?php
                    $badgeClass = match($compra['estado']) {
                        'recibida'              => 'bg-emerald-100 text-emerald-700',
                        'parcialmente_recibida' => 'bg-blue-100 text-blue-700',
                        'pendiente'             => 'bg-amber-100 text-amber-700',
                        'cancelada'             => 'bg-red-100 text-red-600',
                        default                 => 'bg-gray-100 text-gray-600'
                    };
                    $badgeLabel = $compra['estado'] === 'parcialmente_recibida' ? 'Parcial' : ucfirst($compra['estado']);
                    ?>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                    <?= date('d/m/Y', strtotime($compra['fecha_compra'] ?? $compra['fecha_registro'])) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                    <div class="flex justify-end gap-2">
                        <?php if (in_array($compra['estado'], ['pendiente', 'parcialmente_recibida'])): ?>
                        <a href="/compras/<?= $compra['compra_id'] ?>/recibir" class="text-blue-500 hover:text-blue-700" title="Registrar Recepción">
                            <i class="fas fa-truck-loading"></i>
                        </a>
                        <?php endif; ?>
                        <a href="/compras/<?= $compra['compra_id'] ?>" class="text-gray-400 hover:text-blue-500" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($compras)): ?>
            <tr>
                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400 italic">No se han registrado compras todavía.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php View::endSection('content'); ?>
