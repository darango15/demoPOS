<?php use App\Core\View; View::layout('app'); ?>
<?php
View::section('content');
$estado_actual = $estado_actual ?? '';
$cliente_actual = $cliente_actual ?? '';
$fecha_inicio_actual = $fecha_inicio_actual ?? '';
$fecha_fin_actual = $fecha_fin_actual ?? '';
?>

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" value="<?= View::e($fecha_inicio_actual) ?>" class="form-input">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="<?= View::e($fecha_fin_actual) ?>" class="form-input">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="pendiente" <?= $estado_actual == 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                <option value="aprobada" <?= $estado_actual == 'aprobada' ? 'selected' : '' ?>>Aprobadas</option>
                <option value="rechazada" <?= $estado_actual == 'rechazada' ? 'selected' : '' ?>>Rechazadas</option>
                <option value="convertida" <?= $estado_actual == 'convertida' ? 'selected' : '' ?>>Convertidas</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select name="cliente" class="form-select">
                <option value="">Todos</option>
                <?php foreach (($clientes ?? []) as $cliente):
                      $cId = is_object($cliente) ? $cliente->cliente_id : ($cliente['cliente_id'] ?? '');
                      $cNombre = is_object($cliente) ? $cliente->nombre : ($cliente['nombre'] ?? '');
                ?>
                <option value="<?= $cId ?>" <?= $cliente_actual == $cId ? 'selected' : '' ?>>
                    <?= View::e($cNombre) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit"
                class="bg-sky-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg w-full">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Métricas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-indigo-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Cotizaciones</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    $<?= number_format((float)($total_cotizaciones ?? 0), 2) ?>
                </h3>
            </div>
            <div class="bg-indigo-100 p-3 rounded-lg">
                <i class="fas fa-file-invoice text-indigo-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-blue-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-600">Cantidad</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= (int)($cantidad_cotizaciones ?? 0) ?></h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-hashtag text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-purple-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-600">Promedio</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    $<?= number_format((float)($promedio_cotizacion ?? 0), 2) ?>
                </h3>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-orange-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-600">Cotizaciones Hoy</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    <?= (int)($cotizaciones_hoy_count ?? 0) ?>
                </h3>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg">
                <i class="fas fa-calendar-day text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de cotizaciones -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="flex justify-between items-center p-4 border-b">
        <h3 class="text-lg font-semibold text-gray-900">Registro de Cotizaciones</h3>
        <a href="/ventas/cotizaciones/nueva"
            class="bg-sky-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Nueva Cotización
        </a>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Número</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Fecha</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vence</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Cliente</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Estado</th>
                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach (($cotizaciones ?? []) as $cotizacion): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    <?= View::e($cotizacion['numero']) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?= date('d/m/Y H:i', strtotime($cotizacion['fecha'])) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?php
                    $fecha_validez = strtotime($cotizacion['fecha_vencimiento'] ?? '');
                    $vencida = $fecha_validez && $fecha_validez < time() && $cotizacion['estado'] === 'pendiente';
                    ?>
                    <span class="<?= $vencida ? 'text-red-600 font-medium' : '' ?>">
                        <?= $fecha_validez ? date('d/m/Y', $fecha_validez) : '-' ?>
                        <?php if ($vencida): ?>
                        <i class="fas fa-exclamation-circle ml-1" title="Vencida"></i>
                        <?php endif; ?>
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?= View::e($cotizacion['cliente_nombre'] ?? 'Consumidor Final') ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    $<?= number_format((float)$cotizacion['total'], 2) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <?php
                        $badgeClasses = match($cotizacion['estado']) {
                            'aprobada', 'convertida' => 'bg-emerald-500 bg-opacity-10 text-emerald-500',
                            'rechazada' => 'bg-red-500 bg-opacity-10 text-red-500',
                            default     => 'bg-amber-500 bg-opacity-10 text-amber-500',
                        };
                    ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badgeClasses ?>">
                        <?= ucfirst(View::e($cotizacion['estado'])) ?>
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <a href="/ventas/cotizaciones/<?= $cotizacion['cotizacion_id'] ?>"
                            class="text-blue-500 hover:text-blue-700 action-btn" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="imprimirCotizacion(<?= $cotizacion['cotizacion_id'] ?>)"
                            class="text-green-500 hover:text-green-700 action-btn" title="Imprimir">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($cotizaciones)): ?>
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                    <i class="fas fa-file-invoice text-3xl mb-2 text-gray-300"></i>
                    <p>No se encontraron cotizaciones</p>
                    <a href="/ventas/cotizaciones/nueva" class="text-sky-500 hover:underline mt-2 inline-block">
                        Crear primera cotización
                    </a>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="px-4 py-3 border-t bg-gray-50">
        <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
    </div>
</div>
<?php View::endSection('content'); ?>

<?php View::section('extra_js'); ?>
<script>
    function imprimirCotizacion(id) {
        window.open(`/ventas/cotizaciones/${id}/?print=true`, '_blank');
    }
</script>
<?php View::endSection('extra_js'); ?>
