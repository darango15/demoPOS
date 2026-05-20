<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<?php
$est = $cotizacion['estado'] ?? '';
$estadoClass = match($est) {
    'pendiente'  => 'bg-yellow-100 text-yellow-700',
    'aprobada'   => 'bg-emerald-100 text-emerald-700',
    'rechazada'  => 'bg-red-100 text-red-600',
    'convertida' => 'bg-sky-100 text-sky-700',
    default      => 'bg-gray-100 text-gray-600'
};
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-gray-400 mb-3">
    <a href="/ventas/cotizaciones" class="hover:text-gray-600 transition-colors">Cotizaciones</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-gray-700 font-medium">#<?= View::e($cotizacion['numero'] ?? '') ?></span>
</div>

<!-- Action bar -->
<div class="flex items-center justify-between bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-2.5 mb-4">
    <div class="flex gap-2">
        <a href="/ventas/nueva?cotizacion_id=<?= $cotizacion['cotizacion_id'] ?>"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
            <i class="fas fa-file-invoice-dollar"></i> Facturar
        </a>
        <?php if ($est === 'pendiente' || $est === 'aprobada'): ?>
        <form action="/ventas/cotizaciones/<?= $cotizacion['cotizacion_id'] ?>/convertir" method="POST" class="inline">
            <?= View::csrf() ?>
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-emerald-300 text-emerald-700 rounded-lg text-sm font-semibold hover:bg-emerald-50 transition">
                <i class="fas fa-check-double"></i> Auto-Convertir
            </button>
        </form>
        <?php endif; ?>
        <a href="/ventas/cotizaciones" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
            Volver
        </a>
    </div>

    <!-- Pipeline -->
    <div class="flex items-stretch text-xs font-semibold select-none">
        <div class="flex items-center <?= $est === 'pendiente' ? 'bg-sky-500 text-white' : 'bg-gray-100 text-gray-400' ?> pl-4 pr-6 py-2 rounded-l-lg relative">
            Solicitud
            <span class="absolute right-0 top-0 h-full w-3 overflow-hidden translate-x-2.5 z-10">
                <span class="block w-6 h-6 <?= $est === 'pendiente' ? 'bg-sky-500' : 'bg-gray-100' ?> rotate-45 origin-top-left mt-0.5 ml-0.5"></span>
            </span>
        </div>
        <div class="flex items-center <?= $est === 'aprobada' ? 'bg-sky-500 text-white' : 'bg-gray-100 text-gray-400' ?> pl-7 pr-6 py-2 relative">
            Aprobada
            <span class="absolute right-0 top-0 h-full w-3 overflow-hidden translate-x-2.5 z-10">
                <span class="block w-6 h-6 <?= $est === 'aprobada' ? 'bg-sky-500' : 'bg-gray-100' ?> rotate-45 origin-top-left mt-0.5 ml-0.5"></span>
            </span>
        </div>
        <div class="flex items-center <?= $est === 'convertida' ? 'bg-sky-500 text-white' : 'bg-gray-100 text-gray-400' ?> pl-7 pr-5 py-2 rounded-r-lg">
            Convertida
        </div>
    </div>
</div>

<!-- Document card -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-4">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Cotización #<?= View::e($cotizacion['numero'] ?? '') ?></h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-16">
        <div>
            <div class="flex items-baseline gap-4 py-2">
                <label class="text-sm font-semibold text-gray-600 w-36 shrink-0">Cliente</label>
                <span class="text-sm text-gray-800"><?= View::e($cotizacion['cliente_nombre'] ?? 'Consumidor Final') ?></span>
            </div>
            <div class="flex items-baseline gap-4 py-2">
                <label class="text-sm font-semibold text-gray-600 w-36 shrink-0">Vendedor</label>
                <span class="text-sm text-gray-600"><?= View::e($cotizacion['vendedor_nombre'] ?? 'N/A') ?></span>
            </div>
        </div>
        <div>
            <div class="flex items-baseline gap-4 py-2">
                <label class="text-sm font-semibold text-gray-600 w-36 shrink-0">Fecha</label>
                <span class="text-sm text-gray-600"><?= $cotizacion['fecha'] ?? '' ?></span>
            </div>
            <div class="flex items-baseline gap-4 py-2">
                <label class="text-sm font-semibold text-gray-600 w-36 shrink-0">Válida hasta</label>
                <span class="text-sm text-gray-600"><?= $cotizacion['fecha_vencimiento'] ?? 'N/A' ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Items table -->
<?php if (!empty($detalles)): ?>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-4">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <th class="px-6 py-3">Producto</th>
                <th class="px-6 py-3 text-right">Cantidad</th>
                <th class="px-6 py-3 text-right">Precio</th>
                <th class="px-6 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach ($detalles as $d): ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-3 font-medium text-gray-800"><?= View::e($d['producto_nombre'] ?? '—') ?></td>
                <td class="px-6 py-3 text-right"><?= $d['cantidad'] ?? 0 ?></td>
                <td class="px-6 py-3 text-right text-gray-600">$<?= number_format((float)($d['precio'] ?? 0), 2) ?></td>
                <td class="px-6 py-3 text-right font-semibold text-gray-800">$<?= number_format((float)($d['total_linea'] ?? 0), 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-gray-50 border-t-2 border-gray-100">
            <tr><td colspan="3" class="px-6 py-2 text-right text-xs text-gray-500">Subtotal</td><td class="px-6 py-2 text-right font-medium text-sm">$<?= number_format((float)($cotizacion['subtotal'] ?? 0), 2) ?></td></tr>
            <tr><td colspan="3" class="px-6 py-2 text-right text-xs text-gray-500">ITBMS</td><td class="px-6 py-2 text-right font-medium text-sm">$<?= number_format((float)($cotizacion['itbms'] ?? 0), 2) ?></td></tr>
            <tr><td colspan="3" class="px-6 py-3 text-right font-bold text-gray-800">Total</td><td class="px-6 py-3 text-right font-bold text-xl text-gray-900">$<?= number_format((float)($cotizacion['total'] ?? 0), 2) ?></td></tr>
        </tfoot>
    </table>
</div>
<?php endif; ?>

<?php View::endSection('content'); ?>
