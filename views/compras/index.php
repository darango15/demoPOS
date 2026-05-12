<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Gestión de Compras</h2>
        <p class="text-slate-500">Historial de facturas recibidas de proveedores</p>
    </div>
    <a href="/compras/nueva" class="bg-blue-500 hover:bg-blue-500/90 text-white font-bold py-2 px-4 rounded-xl shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
        <i class="fas fa-plus"></i> Nueva Compra
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Factura #</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Proveedor</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Monto Total</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($compras as $compra): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">
                            <?= htmlspecialchars($compra['numero_factura']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?= htmlspecialchars($compra['proveedor_nombre']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 text-lg">
                            $<?= number_format((float)$compra['monto_total'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $badgeClass = match($compra['estado']) {
                                'recibida'              => 'bg-emerald-100 text-emerald-700',
                                'parcialmente_recibida' => 'bg-blue-100 text-blue-700',
                                'pendiente'             => 'bg-amber-100 text-amber-700',
                                'cancelada'             => 'bg-rose-100 text-rose-700',
                                default                 => 'bg-slate-100 text-slate-600'
                            };
                            $badgeLabel = match($compra['estado']) {
                                'parcialmente_recibida' => 'Parcial',
                                default                 => ucfirst($compra['estado'])
                            };
                            ?>
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider <?= $badgeClass ?>">
                                <?= $badgeLabel ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?= date('d/m/Y', strtotime($compra['fecha_compra'] ?? $compra['fecha_registro'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm flex items-center justify-end gap-2">
                            <?php if (in_array($compra['estado'], ['pendiente', 'parcialmente_recibida'])): ?>
                            <a href="/compras/<?= $compra['compra_id'] ?>/recibir"
                               class="text-blue-500 hover:text-blue-700 transition-colors p-2" title="Registrar Recepción">
                                <i class="fas fa-truck-loading"></i>
                            </a>
                            <?php endif; ?>
                            <a href="/compras/<?= $compra['compra_id'] ?>" class="text-slate-400 hover:text-blue-500 transition-colors p-2" title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($compras)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                            No se han registrado compras todavía.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php View::endSection('content'); ?>
