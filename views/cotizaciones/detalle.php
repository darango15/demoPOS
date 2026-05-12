<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">Cotización #<?= View::e($cotizacion['numero'] ?? '') ?></h3>
                <p class="text-sm text-gray-500">Fecha: <?= $cotizacion['fecha'] ?? '' ?></p>
            </div>
            <?php $est = $cotizacion['estado'] ?? ''; ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                <?= match($est) { 'pendiente' => 'bg-yellow-100 text-yellow-800', 'aprobada' => 'bg-green-100 text-green-800', 'rechazada' => 'bg-red-100 text-red-800', 'convertida' => 'bg-blue-100 text-blue-800', default => 'bg-gray-100 text-gray-800' } ?>">
                <?= ucfirst($est) ?>
            </span>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Cliente:</span> <span class="font-medium"><?= View::e($cotizacion['cliente_nombre'] ?? 'Consumidor Final') ?></span></div>
            <div><span class="text-gray-500">Validez:</span> <span class="font-medium"><?= $cotizacion['fecha_vencimiento'] ?? 'N/A' ?></span></div>
            <div><span class="text-gray-500">Vendedor:</span> <span class="font-medium"><?= View::e($cotizacion['vendedor_nombre'] ?? 'N/A') ?></span></div>
        </div>
        <?php if (!empty($detalles)): ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-2 text-left">Producto</th>
                <th class="px-4 py-2 text-right">Cantidad</th>
                <th class="px-4 py-2 text-right">Precio</th>
                <th class="px-4 py-2 text-right">Total</th>
            </tr></thead>
            <tbody>
                <?php foreach ($detalles as $d): ?>
                <tr class="border-t">
                    <td class="px-4 py-2"><?= View::e($d['producto_nombre'] ?? '-') ?></td>
                    <td class="px-4 py-2 text-right"><?= $d['cantidad'] ?? 0 ?></td>
                    <td class="px-4 py-2 text-right">$<?= number_format((float)($d['precio'] ?? 0), 2) ?></td>
                    <td class="px-4 py-2 text-right">$<?= number_format((float)($d['total_linea'] ?? 0), 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <div class="border-t pt-4 text-right space-y-1">
            <p class="text-sm">Subtotal: <span class="font-medium">$<?= number_format((float)($cotizacion['subtotal'] ?? 0), 2) ?></span></p>
            <p class="text-sm">ITBMS: <span class="font-medium">$<?= number_format((float)($cotizacion['itbms'] ?? 0), 2) ?></span></p>
            <p class="text-lg font-bold text-blue-600">Total: $<?= number_format((float)($cotizacion['total'] ?? 0), 2) ?></p>
        </div>
        <div class="flex flex-wrap gap-3 border-t pt-4">
            <a href="/ventas/cotizaciones" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Volver</a>
            
            <a href="/ventas/nueva?cotizacion_id=<?= $cotizacion['cotizacion_id'] ?>" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-bold hover:bg-sky-700 transition-colors">
                <i class="fas fa-file-invoice-dollar mr-1"></i>Facturar (Pasar a Factura)
            </a>

            <?php if ($est === 'pendiente' || $est === 'aprobada'): ?>
            <form action="/ventas/cotizaciones/<?= $cotizacion['cotizacion_id'] ?>/convertir" method="POST" class="inline">
                <?= View::csrf() ?>
                <button type="submit" class="px-4 py-2 border border-emerald-600 text-emerald-600 rounded-lg text-sm font-bold hover:bg-emerald-50 transition-colors">
                    <i class="fas fa-check-double mr-1"></i>Auto-Convertir (Rápido)
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php View::endSection('content'); ?>
