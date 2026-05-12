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
                <option value="pagada" <?= $estado_actual == 'pagada' ? 'selected' : '' ?>>Pagadas</option>
                <option value="pendiente" <?= $estado_actual == 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                <option value="anulada" <?= $estado_actual == 'anulada' ? 'selected' : '' ?>>Anuladas</option>
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
    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-green-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Ventas</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">$<?= number_format((float)($total_ventas ?? 0), 2) ?></h3>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-blue-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-600">Cantidad Ventas</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= (int)($cantidad_ventas ?? 0) ?></h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-receipt text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-purple-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-600">Promedio por Venta</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    $<?= number_format((float)($promedio_venta ?? 0), 2) ?>
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
                <p class="text-sm font-medium text-gray-600">Ventas Hoy</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                    <?= (int)($ventas_hoy_count ?? 0) ?>
                </h3>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg">
                <i class="fas fa-calendar-day text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de ventas -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="flex justify-between items-center p-4 border-b">
        <h3 class="text-lg font-semibold text-gray-900">Últimas Ventas</h3>
        <div class="flex space-x-2">
            <!-- BOTÓN DIRECTO AL PUNTO DE VENTA -->
            <a href="/ventas/pos"
                class="bg-sky-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
                <i class="fas fa-cash-register mr-2"></i> Punto de Venta
            </a>
        </div>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Factura</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Fecha</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Cliente</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vendedor</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Estado</th>
                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach (($ventas ?? []) as $venta): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    <?= View::e($venta['numero_factura']) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?= date("d/m/Y H:i", strtotime($venta['fecha'])) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?= View::e($venta['cliente_nombre'] ?: "Consumidor Final") ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?= View::e($venta['vendedor_nombre'] ?? '') ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    $<?= number_format((float)$venta['total'], 2) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <?php
                        $badgeClasses = 'bg-red-500 bg-opacity-10 text-red-500';
                        if ($venta['estado'] === 'pagada') $badgeClasses = 'bg-emerald-500 bg-opacity-10 text-emerald-500';
                        elseif ($venta['estado'] === 'pendiente') $badgeClasses = 'bg-amber-500 bg-opacity-10 text-amber-500';
                    ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badgeClasses ?>">
                        <?= ucfirst(View::e($venta['estado'])) ?>
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <a href="/ventas/venta/<?= $venta['venta_id'] ?>"
                            class="text-blue-500 hover:text-blue-700 action-btn" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if ($venta['estado'] == 'pagada'): ?>
                        <button onclick="imprimirFactura(<?= $venta['venta_id'] ?>)"
                            class="text-green-500 hover:text-green-700 action-btn" title="Imprimir">
                            <i class="fas fa-print"></i>
                        </button>
                        <?php endif; ?>
                        <?php if ($venta['estado'] != 'anulada'): ?>
                        <button onclick="abrirModalAnular(<?= $venta['venta_id'] ?>, '<?= addslashes($venta['numero_factura']) ?>')"
                                class="text-red-500 hover:text-red-700 action-btn" title="Anular Venta">
                            <i class="fas fa-ban"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($ventas)): ?>
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                    <i class="fas fa-receipt text-3xl mb-2 text-gray-300"></i>
                    <p>No se encontraron ventas</p>
                    <a href="/ventas/crear" class="text-sky-500 hover:underline mt-2 inline-block">
                        Crear primera venta
                    </a>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginación usando el partial existente o estructurado manualmente como antes -->
    <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
</div>

<!-- Modal Anular Venta -->
<div id="modal-anular" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Anular Venta</h3>
        <p class="text-sm text-gray-500 mb-4">Se generará una <strong>nota de crédito</strong> por el monto total.</p>
        <form id="form-anular" method="POST">
            <?= \App\Core\View::csrf() ?>
            <div class="mb-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Factura</label>
                <input type="text" id="modal-factura-num" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de anulación <span class="text-red-500">*</span></label>
                <select name="motivo_tipo" id="motivo_tipo" onchange="toggleMotivoCustom()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2">
                    <option value="Producto defectuoso">Producto defectuoso</option>
                    <option value="Error en precio">Error en precio</option>
                    <option value="Error en cantidad">Error en cantidad</option>
                    <option value="Devolución del cliente">Devolución del cliente</option>
                    <option value="Duplicado">Factura duplicada</option>
                    <option value="otro">Otro motivo...</option>
                </select>
                <textarea name="motivo" id="motivo-custom" placeholder="Describe el motivo de la anulación..."
                    class="hidden w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none" rows="3"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="cerrarModalAnular()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700"><i class="fas fa-ban mr-1"></i>Confirmar Anulación</button>
            </div>
        </form>
    </div>
</div>

<?php View::endSection('content'); ?>

<?php View::section('extra_js'); ?>
<script>
    function imprimirFactura(ventaId) {
        window.open(`/ventas/venta/${ventaId}/?print=true`, '_blank');
    }

    function abrirModalAnular(ventaId, numeroFactura) {
        document.getElementById('form-anular').action = `/ventas/venta/${ventaId}/anular`;
        document.getElementById('modal-factura-num').value = numeroFactura;
        document.getElementById('motivo_tipo').value = 'Producto defectuoso';
        document.getElementById('motivo-custom').classList.add('hidden');
        document.getElementById('modal-anular').classList.remove('hidden');
    }

    function cerrarModalAnular() {
        document.getElementById('modal-anular').classList.add('hidden');
    }

    function toggleMotivoCustom() {
        const sel = document.getElementById('motivo_tipo');
        const txt = document.getElementById('motivo-custom');
        if (sel.value === 'otro') {
            txt.classList.remove('hidden');
            txt.required = true;
        } else {
            txt.classList.add('hidden');
            txt.required = false;
        }
    }

    document.getElementById('form-anular').addEventListener('submit', function(e) {
        const sel = document.getElementById('motivo_tipo');
        if (sel.value === 'otro') {
            const txt = document.getElementById('motivo-custom');
            if (!txt.value.trim()) { e.preventDefault(); txt.focus(); return; }
            sel.disabled = true; // send only textarea value
        } else {
            document.getElementById('motivo-custom').disabled = true;
        }
    });
</script>
<?php View::endSection('extra_js'); ?>
