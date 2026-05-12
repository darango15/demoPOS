<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>
<div class="max-w-4xl mx-auto space-y-4">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">Compra #<?= View::e($compra['numero_factura'] ?? '') ?></h3>
                <p class="text-sm text-gray-500">Fecha: <?= $compra['fecha_compra'] ?? '' ?></p>
            </div>
            <?php $est = $compra['estado'] ?? ''; ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                <?= match($est) {
                    'recibida'             => 'bg-green-100 text-green-800',
                    'parcialmente_recibida'=> 'bg-blue-100 text-blue-800',
                    'pendiente'            => 'bg-yellow-100 text-yellow-800',
                    'cancelada'            => 'bg-red-100 text-red-800',
                    default                => 'bg-gray-100 text-gray-800'
                } ?>">
                <?= $est === 'parcialmente_recibida' ? 'Parcialmente Recibida' : ucfirst($est) ?>
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Proveedor:</span> <span class="font-medium"><?= View::e($compra['proveedor_nombre'] ?? 'N/A') ?></span></div>
            <div><span class="text-gray-500">Depósito:</span> <span class="font-medium"><?= View::e($compra['deposito_nombre'] ?? 'N/A') ?></span></div>
            <div><span class="text-gray-500">Factura Proveedor:</span> <span class="font-medium"><?= View::e($compra['numero_factura_proveedor'] ?? '-') ?></span></div>
            <div><span class="text-gray-500">Registrado por:</span> <span class="font-medium"><?= View::e($compra['usuario_nombre'] ?? 'N/A') ?></span></div>
            <?php if (!empty($compra['notas'])): ?>
            <div class="col-span-2"><span class="text-gray-500">Notas:</span> <span class="font-medium"><?= View::e($compra['notas']) ?></span></div>
            <?php endif; ?>
        </div>

        <?php $hayParcial = in_array($est, ['pendiente', 'parcialmente_recibida']); ?>

        <?php if (!empty($detalles)): ?>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Código</th>
                    <th class="px-4 py-2 text-left">Producto</th>
                    <th class="px-4 py-2 text-right">Pedido</th>
                    <th class="px-4 py-2 text-right">Recibido</th>
                    <?php if ($hayParcial): ?>
                    <th class="px-4 py-2 text-right text-amber-700">Pendiente</th>
                    <?php endif; ?>
                    <th class="px-4 py-2 text-right">Costo Unit.</th>
                    <th class="px-4 py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $d):
                    $pendiente = (float)($d['cantidad_pendiente'] ?? 0);
                ?>
                <tr class="border-t hover:bg-gray-50 <?= $pendiente > 0 ? 'bg-amber-50/40' : '' ?>">
                    <td class="px-4 py-2 font-mono text-gray-500"><?= View::e($d['producto_codigo'] ?? '-') ?></td>
                    <td class="px-4 py-2"><?= View::e($d['producto_nombre'] ?? '-') ?></td>
                    <td class="px-4 py-2 text-right"><?= number_format((float)($d['cantidad'] ?? 0), 2) ?></td>
                    <td class="px-4 py-2 text-right font-medium <?= (float)$d['cantidad_recibida'] > 0 ? 'text-green-700' : 'text-gray-400' ?>">
                        <?= number_format((float)($d['cantidad_recibida'] ?? 0), 2) ?>
                    </td>
                    <?php if ($hayParcial): ?>
                    <td class="px-4 py-2 text-right font-medium <?= $pendiente > 0 ? 'text-amber-700' : 'text-gray-400' ?>">
                        <?= $pendiente > 0 ? number_format($pendiente, 2) : '—' ?>
                    </td>
                    <?php endif; ?>
                    <td class="px-4 py-2 text-right">$<?= number_format((float)($d['costo'] ?? 0), 2) ?></td>
                    <td class="px-4 py-2 text-right font-medium">$<?= number_format((float)($d['total_linea'] ?? 0), 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>

        <div class="border-t pt-4 text-right space-y-1">
            <p class="text-sm">Subtotal: <span class="font-medium">$<?= number_format((float)($compra['monto_subtotal'] ?? 0), 2) ?></span></p>
            <p class="text-sm">ITBMS: <span class="font-medium">$<?= number_format((float)($compra['monto_itbms'] ?? 0), 2) ?></span></p>
            <p class="text-lg font-bold text-blue-600">Total: $<?= number_format((float)($compra['monto_total'] ?? 0), 2) ?></p>
        </div>

        <div class="border-t pt-4 flex items-center gap-3">
            <a href="/compras" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-1"></i>Volver
            </a>
            <?php if ($hayParcial): ?>
            <a href="/compras/<?= $compra['compra_id'] ?>/recibir"
               class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center gap-2">
                <i class="fas fa-truck-loading"></i> Registrar Recepción
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php View::endSection('content'); ?>
