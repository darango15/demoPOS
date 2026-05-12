<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="p-6 border-b">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800"><?= View::e($venta['numero_factura']) ?></h2>
                <p class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></p>
            </div>
            <div class="flex items-center gap-3">
                <?php
                $bc = match($venta['estado']) { 'pagada'=>'bg-green-100 text-green-700', 'anulada'=>'bg-red-100 text-red-700', default=>'bg-yellow-100 text-yellow-700' };
                ?>
                <span class="px-3 py-1 rounded-full text-sm font-medium <?= $bc ?>"><?= ucfirst($venta['estado']) ?></span>
                <?php if ($venta['estado'] === 'pendiente'): ?>
                <button onclick="abrirModalPagar(<?= $venta['venta_id'] ?>, '<?= addslashes($venta['numero_factura']) ?>', <?= $venta['total'] ?>)"
                        class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                    <i class="fas fa-check-circle mr-1"></i> Registrar Pago
                </button>
                <?php endif; ?>
                <?php if ($venta['estado'] !== 'anulada'): ?>
                <button onclick="abrirModalAnular(<?= $venta['venta_id'] ?>, '<?= addslashes($venta['numero_factura']) ?>')"
                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100">
                    <i class="fas fa-ban mr-1"></i> Anular
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm">
            <div><span class="text-gray-500">Cliente:</span><br><span class="font-medium"><?= View::e($venta['cliente_nombre'] ?? 'Consumidor Final') ?></span></div>
            <div><span class="text-gray-500">RUC:</span><br><span class="font-medium"><?= View::e($venta['cliente_ruc'] ?? '—') ?></span></div>
            <div><span class="text-gray-500">Vendedor:</span><br><span class="font-medium"><?= View::e($venta['vendedor_nombre'] ?? '—') ?></span></div>
            <div><span class="text-gray-500">Forma de Pago:</span><br><span class="font-medium capitalize"><?= View::e($venta['forma_pago'] ?? '—') ?></span></div>
        </div>
    </div>

    <!-- Detalles -->
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-left text-xs text-gray-500 uppercase">
                <th class="px-6 py-3">Producto</th>
                <th class="px-6 py-3 text-right">Precio</th>
                <th class="px-6 py-3 text-right">Cant.</th>
                <th class="px-6 py-3 text-right">Desc.</th>
                <th class="px-6 py-3 text-right">ITBMS</th>
                <th class="px-6 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach (($detalles ?? []) as $d): ?>
            <tr class="hover:bg-gray-50/50">
                <td class="px-6 py-3">
                    <span class="text-gray-400 text-xs mr-2"><?= View::e($d['producto_codigo']) ?></span>
                    <span class="font-medium text-gray-800"><?= View::e($d['producto_nombre']) ?></span>
                    <?php if (!empty($d['deposito_nombre'])): ?><span class="text-xs text-gray-400 ml-1">(<?= View::e($d['deposito_nombre']) ?>)</span><?php endif; ?>
                </td>
                <td class="px-6 py-3 text-right text-gray-600">$<?= number_format((float)$d['precio'], 2) ?></td>
                <td class="px-6 py-3 text-right font-medium"><?= $d['cantidad'] ?></td>
                <td class="px-6 py-3 text-right text-gray-500">$<?= number_format((float)$d['descuento'], 2) ?></td>
                <td class="px-6 py-3 text-right text-gray-500">$<?= number_format((float)$d['itbms'], 2) ?></td>
                <td class="px-6 py-3 text-right font-semibold text-gray-800">$<?= number_format((float)$d['total_linea'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-gray-50 border-t-2">
            <tr><td colspan="5" class="px-6 py-2 text-right text-sm text-gray-500">Subtotal</td><td class="px-6 py-2 text-right font-medium">$<?= number_format((float)$venta['subtotal'], 2) ?></td></tr>
            <tr><td colspan="5" class="px-6 py-2 text-right text-sm text-gray-500">Descuento</td><td class="px-6 py-2 text-right font-medium text-red-600">-$<?= number_format((float)$venta['descuento'], 2) ?></td></tr>
            <tr><td colspan="5" class="px-6 py-2 text-right text-sm text-gray-500">ITBMS (7%)</td><td class="px-6 py-2 text-right font-medium">$<?= number_format((float)$venta['itbms'], 2) ?></td></tr>
            <tr class="text-lg"><td colspan="5" class="px-6 py-3 text-right font-semibold text-gray-800">Total</td><td class="px-6 py-3 text-right font-bold text-gray-900">$<?= number_format((float)$venta['total'], 2) ?></td></tr>
        </tfoot>
    </table>
</div>

<div class="mt-4 text-center">
    <a href="/ventas" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left mr-1"></i> Volver a ventas</a>
</div>

<!-- Modal Registrar Pago -->
<div id="modal-pagar" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Registrar Pago</h3>
        <p class="text-sm text-gray-500 mb-4">Se marcará la venta como <strong>pagada</strong> y se actualizará el saldo del cliente.</p>
        <form id="form-pagar" method="POST">
            <?= View::csrf() ?>
            <div class="mb-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Factura</label>
                <input type="text" id="pagar-factura-num" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Monto a cobrar</label>
                <input type="text" id="pagar-monto" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-semibold text-gray-800">
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('modal-pagar').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700"><i class="fas fa-check-circle mr-1"></i>Confirmar Pago</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Anular Venta -->
<div id="modal-anular" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Anular Venta</h3>
        <p class="text-sm text-gray-500 mb-4">Se generará una <strong>nota de crédito</strong> por el monto total.</p>
        <form id="form-anular" method="POST">
            <?= View::csrf() ?>
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
    function abrirModalPagar(ventaId, numeroFactura, total) {
        document.getElementById('form-pagar').action = `/ventas/venta/${ventaId}/pagar`;
        document.getElementById('pagar-factura-num').value = numeroFactura;
        document.getElementById('pagar-monto').value = '$' + parseFloat(total).toFixed(2);
        document.getElementById('modal-pagar').classList.remove('hidden');
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
