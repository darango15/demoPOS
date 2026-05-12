<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Stat cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Lotes</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= $stats['total'] ?? 0 ?></h3>
                <p class="text-xs text-gray-500 mt-2">Lotes registrados</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-boxes text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-orange-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-600">Por Vencer</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= $stats['por_vencer'] ?? 0 ?></h3>
                <p class="text-xs text-orange-600 mt-2">Vencen en &le;30 días</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg">
                <i class="fas fa-clock text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-red-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-600">Vencidos</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= $stats['vencidos'] ?? 0 ?></h3>
                <p class="text-xs text-red-600 mt-2">Requieren acción</p>
            </div>
            <div class="bg-red-100 p-3 rounded-lg">
                <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-gray-400">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-600">Sin Fecha</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= $stats['sin_fecha'] ?? 0 ?></h3>
                <p class="text-xs text-gray-500 mt-2">Sin fecha de vencimiento</p>
            </div>
            <div class="bg-gray-100 p-3 rounded-lg">
                <i class="fas fa-infinity text-gray-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table card -->
<div class="bg-white rounded-xl shadow-sm">
    <div class="flex justify-between items-center p-6 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800">Lotes de Inventario</h2>
        <form method="POST" action="/inventario/lotes/marcar-vencidos" onsubmit="return confirm('¿Marcar todos los lotes vencidos como vencido?');">
            <?= View::csrf() ?>
            <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-skull-crossbones"></i> Marcar Vencidos
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">N° Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Depósito</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Stock Lote</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Fabricación</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Vencimiento</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Días</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach (($lotes ?? []) as $lote): ?>
                <?php
                    $estadoVenc = $lote['estado_vencimiento'] ?? 'sin_vencimiento';
                    $dias = $lote['dias_para_vencer'];

                    // Badge color
                    switch ($estadoVenc) {
                        case 'vencido':
                            $badgeCls = 'bg-red-100 text-red-700';
                            $badgeLabel = 'Vencido';
                            break;
                        case 'por_vencer':
                            $badgeCls = 'bg-amber-100 text-amber-700';
                            $badgeLabel = 'Por Vencer';
                            break;
                        case 'vigente':
                            $badgeCls = 'bg-green-100 text-green-700';
                            $badgeLabel = 'Vigente';
                            break;
                        default:
                            $badgeCls = 'bg-gray-100 text-gray-600';
                            $badgeLabel = 'Sin Vencimiento';
                    }

                    // Días color
                    if ($dias === null) {
                        $diasHtml = '<span class="text-gray-400">N/A</span>';
                    } elseif ((int)$dias < 0) {
                        $diasHtml = '<span class="font-bold text-red-600">' . (int)$dias . '</span>';
                    } elseif ((int)$dias <= 30) {
                        $diasHtml = '<span class="font-bold text-amber-600">' . (int)$dias . '</span>';
                    } else {
                        $diasHtml = '<span class="font-bold text-green-600">' . (int)$dias . '</span>';
                    }
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800"><?= View::e($lote['producto_nombre']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-500"><?= View::e($lote['codigo']) ?></td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-700"><?= View::e($lote['numero_lote']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= View::e($lote['deposito_nombre']) ?></td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-800"><?= number_format((float)$lote['cantidad_actual'], 2) ?></td>
                    <td class="px-4 py-3 text-center text-sm text-gray-500">
                        <?= $lote['fecha_fabricacion'] ? date('d/m/Y', strtotime($lote['fecha_fabricacion'])) : '<span class="text-gray-300">—</span>' ?>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                        <?= $lote['fecha_vencimiento'] ? date('d/m/Y', strtotime($lote['fecha_vencimiento'])) : '<span class="text-gray-300">—</span>' ?>
                    </td>
                    <td class="px-4 py-3 text-center text-sm"><?= $diasHtml ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs font-bold rounded-full <?= $badgeCls ?>"><?= $badgeLabel ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($lotes)): ?>
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-sm text-gray-400 italic">
                        No hay lotes registrados. Los lotes se crean al registrar compras con número de lote.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php View::endSection('content'); ?>
