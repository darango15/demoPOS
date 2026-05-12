<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Traslados entre Almacenes</h2>
        <p class="text-slate-500">Mueve mercancía de forma controlada entre tus depósitos</p>
    </div>
    <a href="/inventario/traslados/nuevo" class="bg-blue-500 hover:bg-blue-500/90 text-white font-bold py-2 px-4 rounded-xl shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
        <i class="fas fa-exchange-alt"></i> Nuevo Traslado
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Origen</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Destino</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Enviado por</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white" x-data="trasladosList()">
                <?php foreach ($traslados as $t): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-400">#<?= $t['traslado_id'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-700"><?= htmlspecialchars($t['origen_nombre']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-700">
                            <i class="fas fa-long-arrow-alt-right mx-2 text-slate-300"></i>
                            <?= htmlspecialchars($t['destino_nombre']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $badgeClass = match($t['estado']) {
                                'recibido' => 'bg-emerald-100 text-emerald-700',
                                'en_transito' => 'bg-amber-100 text-amber-700',
                                'borrador' => 'bg-slate-100 text-slate-600',
                                'cancelado' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-600'
                            };
                            ?>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $badgeClass ?>">
                                <?= $t['estado'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <div class="flex flex-col">
                                <span class="font-medium text-slate-700"><?= htmlspecialchars($t['usuario_envia_nombre']) ?></span>
                                <span class="text-[10px]"><?= date('d/m/Y H:i', strtotime($t['fecha_envio'])) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <?php if ($t['estado'] === 'en_transito'): ?>
                                <button @click="recibirTraslado(<?= $t['traslado_id'] ?>)" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white font-bold py-1 px-3 rounded-lg transition-all text-xs border border-emerald-100">
                                    Confirmar Recepción
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($traslados)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                                    <i class="fas fa-exchange-alt text-2xl text-slate-200"></i>
                                </div>
                                <p class="text-slate-400 italic">No se han registrado traslados entre almacenes.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function trasladosList() {
    return {
        async recibirTraslado(id) {
            if (!confirm('¿Estás seguro de que has recibido esta mercancía? El stock se actualizará en el depósito de destino.')) return;
            
            try {
                const response = await fetch(`/inventario/traslados/${id}/recibir`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                const res = await response.json();
                if (res.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + res.error);
                }
            } catch (error) {
                alert('Ocurrió un error al procesar la recepción');
            }
        }
    }
}
</script>

<?php View::endSection('content'); ?>
