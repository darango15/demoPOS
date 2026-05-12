<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form action="/ventas/cotizaciones/nueva" method="POST" class="space-y-6">
            <?= View::csrf() ?>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                    <select name="cliente_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="">Seleccionar...</option>
                        <?php foreach (($clientes ?? []) as $c): ?>
                            <option value="<?= $c->cliente_id ?>"><?= View::e($c->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Validez</label>
                    <input type="date" name="fecha_validez" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea name="notas" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3 border-t pt-4">
                <a href="/ventas/cotizaciones" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"><i class="fas fa-save mr-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php View::endSection('content'); ?>
