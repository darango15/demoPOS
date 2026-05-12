<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Info Principal -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800"><?= View::e($cliente->nombre) ?></h2>
                    <p class="text-sm text-gray-500">Código: <?= View::e($cliente->codigo) ?></p>
                    <p class="mt-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 capitalize"><?= View::e($cliente->tipo) ?></span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $cliente->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>"><?= ucfirst($cliente->estado) ?></span>
                    </p>
                </div>
                <a href="/clientes/<?= $cliente->cliente_id ?>/editar" class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100"><i class="fas fa-pen mr-1"></i> Editar</a>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t text-sm">
                <div><span class="text-gray-500">RUC:</span> <span class="font-medium"><?= View::e($cliente->ruc ?: '—') ?></span></div>
                <div><span class="text-gray-500">DV:</span> <span class="font-medium"><?= View::e($cliente->dv ?: '—') ?></span></div>
                <div><span class="text-gray-500">Teléfono:</span> <span class="font-medium"><?= View::e($cliente->telefono ?: '—') ?></span></div>
                <div><span class="text-gray-500">Email:</span> <span class="font-medium"><?= View::e($cliente->email ?: '—') ?></span></div>
                <div class="col-span-2"><span class="text-gray-500">Dirección:</span> <span class="font-medium"><?= View::e($cliente->direccion ?: '—') ?></span></div>
                <div><span class="text-gray-500">Límite Crédito:</span> <span class="font-medium">$<?= number_format((float)$cliente->limite_credito, 2) ?></span></div>
                <div><span class="text-gray-500">Saldo:</span> <span class="font-medium text-<?= (float)$cliente->saldo > 0 ? 'red' : 'green' ?>-600">$<?= number_format((float)$cliente->saldo, 2) ?></span></div>
            </div>
        </div>

        <!-- Últimas Ventas -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-receipt text-gray-400 mr-2"></i>Últimas Compras</h3>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 uppercase border-b"><th class="pb-2">Factura</th><th class="pb-2 text-right">Total</th><th class="pb-2">Estado</th><th class="pb-2">Fecha</th></tr></thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach (($ventas ?? []) as $v): ?>
                    <tr>
                        <td class="py-2"><a href="/ventas/venta/<?= $v['venta_id'] ?>" class="text-blue-600 hover:text-blue-700 font-medium"><?= View::e($v['numero_factura']) ?></a></td>
                        <td class="py-2 text-right font-semibold">$<?= number_format((float)$v['total'], 2) ?></td>
                        <td class="py-2"><span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $v['estado'] === 'pagada' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>"><?= ucfirst($v['estado']) ?></span></td>
                        <td class="py-2 text-gray-400 text-xs"><?= date('d/m/Y', strtotime($v['fecha'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ventas)): ?><tr><td colspan="4" class="py-4 text-center text-gray-400">Sin compras registradas</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sidebar Direcciones -->
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>Direcciones</h3>
            <?php foreach (($direcciones ?? []) as $dir): ?>
            <div class="py-2 border-b last:border-0 text-sm">
                <p class="text-gray-800"><?= View::e($dir->direccion) ?></p>
                <?php if ($dir->telefono): ?><p class="text-gray-500 text-xs"><i class="fas fa-phone text-xs mr-1"></i><?= View::e($dir->telefono) ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
            <form action="/clientes/<?= $cliente->cliente_id ?>/direccion" method="POST" class="mt-3 space-y-2">
                <?= View::csrf() ?>
                <input type="text" name="direccion" required placeholder="Nueva dirección" class="w-full border rounded-lg px-3 py-2 text-sm">
                <input type="text" name="telefono" placeholder="Teléfono" class="w-full border rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="w-full px-3 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100"><i class="fas fa-plus mr-1"></i> Agregar</button>
            </form>
        </div>
    </div>
</div>

<?php View::endSection('content'); ?>
