<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="bg-white/50 backdrop-blur-sm p-6 rounded-2xl border border-white/20 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Auditoría de Inventario (Kardex)</h2>
            <p class="text-slate-500">Consulta el historial completo de entradas, salidas y traslados</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-white hover:bg-slate-50 text-slate-600 font-bold py-2 px-4 rounded-xl border border-slate-200 transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="/inventario/kardex" class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Producto</label>
            <select name="producto_id" class="w-full py-2 px-3 rounded-xl bg-white border border-slate-200 focus:ring-2 focus:ring-blue-500/20 outline-none text-sm">
                <option value="">Todos los productos</option>
                <?php foreach ($productos as $p): ?>
                <option value="<?= $p->producto_id ?>" <?= ($filtros['producto_id'] ?? '') == $p->producto_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p->nombre) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Depósito</label>
            <select name="deposito_id" class="w-full py-2 px-3 rounded-xl bg-white border border-slate-200 focus:ring-2 focus:ring-blue-500/20 outline-none text-sm">
                <option value="">Todos los depósitos</option>
                <?php foreach ($depositos as $d): ?>
                <option value="<?= $d['deposito_id'] ?>" <?= ($filtros['deposito_id'] ?? '') == $d['deposito_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Tipo Movimiento</label>
            <select name="tipo" class="w-full py-2 px-3 rounded-xl bg-white border border-slate-200 focus:ring-2 focus:ring-blue-500/20 outline-none text-sm">
                <option value="">Cualquier tipo</option>
                <option value="entrada" <?= ($filtros['tipo'] ?? '') == 'entrada' ? 'selected' : '' ?>>Entrada (Compra/Ajuste)</option>
                <option value="salida" <?= ($filtros['tipo'] ?? '') == 'salida' ? 'selected' : '' ?>>Salida (Venta/Ajuste)</option>
                <option value="traslado_en" <?= ($filtros['tipo'] ?? '') == 'traslado_en' ? 'selected' : '' ?>>Traslado (Entrante)</option>
                <option value="traslado_out" <?= ($filtros['tipo'] ?? '') == 'traslado_out' ? 'selected' : '' ?>>Traslado (Saliente)</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-4 rounded-xl transition-all">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50/50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Fecha / Hora</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Producto / Almacén</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Saldo Nuevo</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Motivo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($movimientos as $m): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700"><?= date('d/m/Y', strtotime($m['fecha_registro'])) ?></span>
                                <span class="text-[10px] text-slate-400"><?= date('H:i:s', strtotime($m['fecha_registro'])) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($m['producto_nombre']) ?></span>
                                <span class="text-[10px] text-blue-500 font-medium uppercase tracking-widest"><?= htmlspecialchars($m['deposito_nombre']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php 
                            $badge = match($m['tipo']) {
                                'entrada' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'salida' => 'bg-rose-50 text-rose-600 border-rose-100',
                                'traslado_en' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'traslado_out' => 'bg-amber-50 text-amber-600 border-amber-100',
                                default => 'bg-slate-50 text-slate-600 border-slate-100'
                            };
                            ?>
                            <span class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider border <?= $badge ?>">
                                <?= str_replace('_', ' ', $m['tipo']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-extrabold <?= in_array($m['tipo'], ['entrada', 'traslado_en']) ? 'text-emerald-500' : 'text-rose-500' ?>">
                                <?= in_array($m['tipo'], ['entrada', 'traslado_en']) ? '+' : '-' ?><?= number_format((float)$m['cantidad'], 2) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-black text-slate-900"><?= number_format((float)$m['saldo_nuevo'], 2) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-slate-500 max-w-xs truncate" title="<?= htmlspecialchars($m['motivo']) ?>">
                                <?= htmlspecialchars($m['motivo']) ?>
                            </p>
                            <span class="text-[9px] text-slate-300">Usuario: <?= htmlspecialchars($m['usuario_nombre']) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($movimientos)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                                    <i class="fas fa-history text-2xl text-slate-200"></i>
                                </div>
                                <p class="text-slate-400 italic">No se registran movimientos con los filtros aplicados.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php View::endSection('content'); ?>
