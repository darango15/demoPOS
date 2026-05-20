<?php use App\Core\View; View::layout('app'); ?>
<?php
View::section('content');
$estado_actual = $estado_actual ?? '';
$cliente_actual = $cliente_actual ?? '';
$fecha_inicio_actual = $fecha_inicio_actual ?? '';
$fecha_fin_actual = $fecha_fin_actual ?? '';
?>

<!-- Métricas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
        <p class="text-xs text-gray-500 mb-1">Total Ventas</p>
        <h3 class="text-xl font-bold text-gray-900">$<?= number_format((float)($total_ventas ?? 0), 2) ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 mb-1">Cantidad</p>
        <h3 class="text-xl font-bold text-gray-900"><?= (int)($cantidad_ventas ?? 0) ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
        <p class="text-xs text-gray-500 mb-1">Promedio</p>
        <h3 class="text-xl font-bold text-gray-900">$<?= number_format((float)($promedio_venta ?? 0), 2) ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-orange-500">
        <p class="text-xs text-gray-500 mb-1">Ventas Hoy</p>
        <h3 class="text-xl font-bold text-gray-900"><?= (int)($ventas_hoy_count ?? 0) ?></h3>
    </div>
</div>

<!-- Filtros + acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="get" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" value="<?= View::e($fecha_inicio_actual) ?>"
                   class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
        </div>
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="<?= View::e($fecha_fin_actual) ?>"
                   class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
        </div>
        <div class="flex-1 min-w-28">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Estado</label>
            <select name="estado" class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todos</option>
                <option value="pagada" <?= $estado_actual == 'pagada' ? 'selected' : '' ?>>Pagadas</option>
                <option value="pendiente" <?= $estado_actual == 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                <option value="anulada" <?= $estado_actual == 'anulada' ? 'selected' : '' ?>>Anuladas</option>
            </select>
        </div>
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Cliente</label>
            <select name="cliente" class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todos</option>
                <?php foreach (($clientes ?? []) as $cliente):
                      $cId = is_object($cliente) ? $cliente->cliente_id : ($cliente['cliente_id'] ?? '');
                      $cNombre = is_object($cliente) ? $cliente->nombre : ($cliente['nombre'] ?? '');
                ?>
                <option value="<?= $cId ?>" <?= $cliente_actual == $cId ? 'selected' : '' ?>><?= View::e($cNombre) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="/ventas/pos" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-semibold hover:bg-gray-900 transition shadow-sm">
                <i class="fas fa-cash-register"></i> Punto de Venta
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead>
            <tr>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Factura</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vendedor</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach (($ventas ?? []) as $venta): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900"><?= View::e($venta['numero_factura']) ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= date("d/m/Y H:i", strtotime($venta['fecha'])) ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?= View::e($venta['cliente_nombre'] ?: "Consumidor Final") ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= View::e($venta['vendedor_nombre'] ?? '') ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">$<?= number_format((float)$venta['total'], 2) ?></td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <?php
                        $badgeClasses = 'bg-red-100 text-red-700';
                        if ($venta['estado'] === 'pagada') $badgeClasses = 'bg-emerald-100 text-emerald-700';
                        elseif ($venta['estado'] === 'pendiente') $badgeClasses = 'bg-amber-100 text-amber-700';
                    ?>
                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badgeClasses ?>">
                        <?= ucfirst(View::e($venta['estado'])) ?>
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                    <div class="flex justify-end gap-2">
                        <a href="/ventas/venta/<?= $venta['venta_id'] ?>" class="text-blue-500 hover:text-blue-700" title="Ver Detalles"><i class="fas fa-eye"></i></a>
                        <?php if ($venta['estado'] == 'pagada'): ?>
                        <button onclick="imprimirFactura(<?= $venta['venta_id'] ?>)" class="text-green-500 hover:text-green-700" title="Imprimir"><i class="fas fa-print"></i></button>
                        <?php endif; ?>
                        <?php if ($venta['estado'] != 'anulada'): ?>
                        <button onclick="abrirModalAnular(<?= $venta['venta_id'] ?>, '<?= addslashes($venta['numero_factura']) ?>')" class="text-red-500 hover:text-red-700" title="Anular"><i class="fas fa-ban"></i></button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($ventas)): ?>
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">
                    <i class="fas fa-receipt text-3xl mb-2 block text-gray-200"></i>
                    No se encontraron ventas
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
</div>

<!-- Modal Anular -->
<div id="modal-anular" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Anular Venta</h3>
        <p class="text-sm text-gray-500 mb-4">Se generará una <strong>nota de crédito</strong> por el monto total.</p>
        <form id="form-anular" method="POST">
            <?= \App\Core\View::csrf() ?>
            <div class="mb-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Factura</label>
                <input type="text" id="modal-factura-num" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Motivo <span class="text-red-500">*</span></label>
                <select name="motivo_tipo" id="motivo_tipo" onchange="toggleMotivoCustom()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-2">
                    <option value="Producto defectuoso">Producto defectuoso</option>
                    <option value="Error en precio">Error en precio</option>
                    <option value="Error en cantidad">Error en cantidad</option>
                    <option value="Devolución del cliente">Devolución del cliente</option>
                    <option value="Duplicado">Factura duplicada</option>
                    <option value="otro">Otro motivo...</option>
                </select>
                <textarea name="motivo" id="motivo-custom" placeholder="Describe el motivo..."
                    class="hidden w-full px-3 py-2 border border-gray-200 rounded-lg text-sm resize-none" rows="3"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="cerrarModalAnular()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700"><i class="fas fa-ban mr-1"></i>Confirmar Anulación</button>
            </div>
        </form>
    </div>
</div>

<?php View::endSection('content'); ?>

<?php View::section('extra_js'); ?>
<script>
    function imprimirFactura(ventaId) { window.open(`/ventas/venta/${ventaId}/?print=true`, '_blank'); }

    function abrirModalAnular(ventaId, numeroFactura) {
        document.getElementById('form-anular').action = `/ventas/venta/${ventaId}/anular`;
        document.getElementById('modal-factura-num').value = numeroFactura;
        document.getElementById('motivo_tipo').value = 'Producto defectuoso';
        document.getElementById('motivo-custom').classList.add('hidden');
        document.getElementById('modal-anular').classList.remove('hidden');
    }

    function cerrarModalAnular() { document.getElementById('modal-anular').classList.add('hidden'); }

    function toggleMotivoCustom() {
        const sel = document.getElementById('motivo_tipo');
        const txt = document.getElementById('motivo-custom');
        if (sel.value === 'otro') { txt.classList.remove('hidden'); txt.required = true; }
        else { txt.classList.add('hidden'); txt.required = false; }
    }

    document.getElementById('form-anular').addEventListener('submit', function(e) {
        const sel = document.getElementById('motivo_tipo');
        if (sel.value === 'otro') {
            const txt = document.getElementById('motivo-custom');
            if (!txt.value.trim()) { e.preventDefault(); txt.focus(); return; }
            sel.disabled = true;
        } else {
            document.getElementById('motivo-custom').disabled = true;
        }
    });
</script>
<?php View::endSection('extra_js'); ?>
