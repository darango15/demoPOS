<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="max-w-3xl mx-auto">
    <form action="/clientes/<?= $cliente->cliente_id ?>/editar" method="POST" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <?= View::csrf() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Código *</label><input type="text" name="codigo" value="<?= View::e($cliente->codigo) ?>" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label><input type="text" name="nombre" value="<?= View::e($cliente->nombre) ?>" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label><select name="tipo" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="natural" <?= $cliente->tipo === 'natural' ? 'selected' : '' ?>>Natural</option><option value="juridico" <?= $cliente->tipo === 'juridico' ? 'selected' : '' ?>>Jurídico</option></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">RUC</label><input type="text" name="ruc" value="<?= View::e($cliente->ruc) ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">DV</label><input type="text" name="dv" value="<?= View::e($cliente->dv) ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label><input type="text" name="telefono" value="<?= View::e($cliente->telefono) ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="<?= View::e($cliente->email) ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">ITBMS</label><select name="itbms" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="SI" <?= ($cliente->itbms > 0) ? 'selected' : '' ?>>Aplica ITBMS</option><option value="NO" <?= ($cliente->itbms <= 0) ? 'selected' : '' ?>>Exento</option></select></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label><textarea name="direccion" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"><?= View::e($cliente->direccion) ?></textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Límite de Crédito ($)</label><input type="number" name="limite_credito" value="<?= $cliente->limite_credito ?>" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Días de Crédito</label><input type="number" name="dias_credito" value="<?= $cliente->dias_credito ?>" min="0" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        </div>
        <div class="flex justify-end gap-3 border-t pt-4">
            <a href="/clientes/<?= $cliente->cliente_id ?>" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm"><i class="fas fa-save mr-1"></i> Actualizar</button>
        </div>
    </form>
</div>

<?php View::endSection('content'); ?>
