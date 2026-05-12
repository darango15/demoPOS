<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<?php
$tipoLabels = [
    'stock_minimo'        => ['label' => 'Stock Mínimo',         'icon' => 'fa-arrow-down',         'color' => 'amber'],
    'stock_exceso'        => ['label' => 'Stock en Exceso',      'icon' => 'fa-arrow-up',            'color' => 'blue'],
    'vencimiento_proximo' => ['label' => 'Próximo a Vencer',     'icon' => 'fa-clock',               'color' => 'orange'],
    'lote_vencido'        => ['label' => 'Lote Vencido',         'icon' => 'fa-skull-crossbones',    'color' => 'red'],
    'rotacion_lenta'      => ['label' => 'Rotación Lenta',       'icon' => 'fa-snooze',              'color' => 'gray'],
];
$prioridadClasses = [
    'critica' => 'bg-red-100 text-red-700 border-red-300',
    'alta'    => 'bg-orange-100 text-orange-700 border-orange-300',
    'media'   => 'bg-amber-100 text-amber-700 border-amber-300',
    'baja'    => 'bg-gray-100 text-gray-600 border-gray-300',
];
?>

<!-- Resumen -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <?php foreach (['critica' => ['Críticas','red'], 'alta' => ['Altas','orange'], 'media' => ['Medias','amber'], 'baja' => ['Bajas','gray']] as $p => [$label, $color]): ?>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-<?= $color ?>-400">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide"><?= $label ?></p>
        <p class="text-3xl font-black text-<?= $color ?>-600"><?= $resumen[$p] ?? 0 ?></p>
    </div>
    <?php endforeach; ?>
</div>

<!-- Acciones globales -->
<?php if (!empty($alertas)): ?>
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500"><?= count($alertas) ?> alerta(s) activa(s) — generadas en este momento</p>
    <form method="POST" action="/inventario/alertas/resolver-todas">
        <?= View::csrf() ?>
        <input type="hidden" name="tipo" value="">
        <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
            <i class="fas fa-check-double mr-1"></i> Resolver todas
        </button>
    </form>
</div>
<?php endif; ?>

<!-- Lista de alertas agrupadas por tipo -->
<?php
$agrupadas = [];
foreach ($alertas as $a) { $agrupadas[$a['tipo']][] = $a; }
?>

<?php if (empty($alertas)): ?>
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="fas fa-check-circle text-5xl text-emerald-400 mb-3"></i>
    <p class="text-lg font-semibold text-gray-700">Sin alertas activas</p>
    <p class="text-sm text-gray-400 mt-1">El inventario está en buen estado.</p>
</div>
<?php else: ?>
<?php foreach ($agrupadas as $tipo => $grupo): ?>
<?php $meta = $tipoLabels[$tipo] ?? ['label' => $tipo, 'icon' => 'fa-bell', 'color' => 'gray']; ?>

<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-5">
    <!-- Cabecera del grupo -->
    <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b">
        <div class="flex items-center gap-2">
            <i class="fas <?= $meta['icon'] ?> text-<?= $meta['color'] ?>-500"></i>
            <span class="font-semibold text-gray-700 text-sm"><?= $meta['label'] ?></span>
            <span class="ml-1 px-2 py-0.5 bg-<?= $meta['color'] ?>-100 text-<?= $meta['color'] ?>-700 rounded-full text-xs font-bold"><?= count($grupo) ?></span>
        </div>
        <form method="POST" action="/inventario/alertas/resolver-todas">
            <?= View::csrf() ?>
            <input type="hidden" name="tipo" value="<?= $tipo ?>">
            <button class="text-xs text-gray-400 hover:text-gray-700 transition">Resolver grupo</button>
        </form>
    </div>

    <!-- Filas -->
    <table class="w-full text-sm">
        <thead class="text-[10px] text-gray-400 uppercase bg-gray-50/50">
            <tr>
                <th class="px-4 py-2 text-left">Mensaje</th>
                <th class="px-4 py-2 text-center">Prioridad</th>
                <th class="px-4 py-2 text-center w-24">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        <?php foreach ($grupo as $alerta): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                    <p class="text-gray-800"><?= View::e($alerta['mensaje']) ?></p>
                    <?php if ($alerta['producto_nombre']): ?>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <span class="font-mono"><?= View::e($alerta['producto_codigo']) ?></span>
                        <?php if ($alerta['deposito_nombre']): ?> · <?= View::e($alerta['deposito_nombre']) ?><?php endif; ?>
                    </p>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border <?= $prioridadClasses[$alerta['prioridad']] ?? '' ?>">
                        <?= ucfirst($alerta['prioridad']) ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="/inventario/alertas/<?= $alerta['alerta_id'] ?>/resolver">
                        <?= View::csrf() ?>
                        <button class="text-xs text-emerald-600 hover:text-emerald-800 font-medium transition">
                            <i class="fas fa-check mr-1"></i>Resolver
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php View::endSection('content'); ?>
