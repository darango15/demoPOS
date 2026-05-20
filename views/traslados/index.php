<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4 flex justify-between items-center">
    <span class="text-sm text-gray-400"><?= count($traslados) ?> traslado(s)</span>
    <a href="/inventario/traslados/nuevo" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
        <i class="fas fa-exchange-alt"></i> Nuevo Traslado
    </a>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" x-data="trasladosList()">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Origen</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Destino</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Enviado por</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            <?php foreach ($traslados as $t): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-400">#<?= $t['traslado_id'] ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700"><?= htmlspecialchars($t['origen_nombre']) ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                    <i class="fas fa-arrow-right mx-1 text-gray-300 text-xs"></i>
                    <?= htmlspecialchars($t['destino_nombre']) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <?php
                    $badgeClass = match($t['estado']) {
                        'recibido'    => 'bg-emerald-100 text-emerald-700',
                        'en_transito' => 'bg-amber-100 text-amber-700',
                        'borrador'    => 'bg-gray-100 text-gray-500',
                        'cancelado'   => 'bg-red-100 text-red-600',
                        default       => 'bg-gray-100 text-gray-500'
                    };
                    ?>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                        <?= ucfirst(str_replace('_', ' ', $t['estado'])) ?>
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                    <div class="font-semibold text-gray-700"><?= htmlspecialchars($t['usuario_envia_nombre']) ?></div>
                    <div class="text-xs text-gray-400"><?= date('d/m/Y H:i', strtotime($t['fecha_envio'])) ?></div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right">
                    <?php if ($t['estado'] === 'en_transito'): ?>
                    <button @click="recibirTraslado(<?= $t['traslado_id'] ?>)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-semibold hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition">
                        <i class="fas fa-check"></i> Confirmar Recepción
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($traslados)): ?>
            <tr>
                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">
                    <i class="fas fa-exchange-alt text-3xl mb-2 block text-gray-200"></i>
                    No se han registrado traslados.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function trasladosList() {
    return {
        async recibirTraslado(id) {
            if (!confirm('¿Confirmar recepción? El stock se actualizará en el depósito de destino.')) return;
            try {
                const response = await fetch(`/inventario/traslados/${id}/recibir`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                const res = await response.json();
                if (res.success) { window.location.reload(); }
                else { alert('Error: ' + res.error); }
            } catch (error) {
                alert('Ocurrió un error al procesar la recepción');
            }
        }
    }
}
</script>

<?php View::endSection('content'); ?>
