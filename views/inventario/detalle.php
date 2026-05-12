<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Producto Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start gap-4">
                <?php if (!empty($producto->imagen_principal)): ?>
                    <img src="/assets/uploads/<?= View::e($producto->imagen_principal) ?>" alt="" class="h-24 w-24 rounded-xl object-cover">
                <?php else: ?>
                    <div class="h-24 w-24 bg-gray-100 rounded-xl flex items-center justify-center"><i class="fas fa-box text-gray-400 text-2xl"></i></div>
                <?php endif; ?>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-800"><?= View::e($producto->nombre) ?></h2>
                    <p class="text-sm text-gray-500">Código: <span class="font-mono"><?= View::e($producto->codigo) ?></span></p>
                    <?php if ($producto->codigo_barras): ?><p class="text-sm text-gray-500">EAN: <span class="font-mono"><?= View::e($producto->codigo_barras) ?></span></p><?php endif; ?>
                    <?php if ($producto->marca): ?><p class="text-sm text-gray-500">Marca: <?= View::e($producto->marca) ?></p><?php endif; ?>
                    <p class="mt-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $producto->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>">
                            <?= ucfirst($producto->estado) ?>
                        </span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="/inventario/<?= $producto->producto_id ?>/editar" class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100"><i class="fas fa-pen mr-1"></i> Editar</a>
                    <a href="/inventario/<?= $producto->producto_id ?>/precios" class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-sm font-medium hover:bg-green-100"><i class="fas fa-tags mr-1"></i> Precios</a>
                </div>
            </div>
            <?php if ($producto->descripcion): ?>
                <p class="mt-4 text-sm text-gray-600 border-t pt-4"><?= nl2br(View::e($producto->descripcion)) ?></p>
            <?php endif; ?>
        </div>

        <!-- Precios + Presentaciones unificados -->
        <?php
        $precios_map = [];
        foreach (($precios ?? []) as $p) {
            $tipo = is_array($p) ? ($p['tipo_precio'] ?? '') : ($p->tipo_precio ?? '');
            $val  = is_array($p) ? ($p['precio'] ?? 0) : ($p->precio ?? 0);
            $precios_map[$tipo] = $val;
        }
        ?>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-tags text-gray-400 mr-2"></i>Precios por Presentación</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 uppercase border-b">
                        <th class="pb-2">Presentación</th>
                        <th class="pb-2 text-center">Unidades</th>
                        <th class="pb-2 text-right text-indigo-500">Precio A</th>
                        <th class="pb-2 text-right text-emerald-500">Precio B</th>
                        <th class="pb-2 text-right">Cód. Barras</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <!-- Fila unidad base -->
                    <tr class="bg-gray-50/50">
                        <td class="py-2 font-semibold text-gray-700">Unidad base</td>
                        <td class="py-2 text-center text-gray-400">1</td>
                        <td class="py-2 text-right font-bold text-indigo-700">$<?= number_format((float)($precios_map['a'] ?? 0), 2) ?></td>
                        <td class="py-2 text-right font-bold text-emerald-700">$<?= number_format((float)($precios_map['b'] ?? 0), 2) ?></td>
                        <td class="py-2 text-right text-gray-300">—</td>
                    </tr>
                    <?php foreach (($unidades ?? []) as $u): ?>
                    <tr>
                        <td class="py-2 font-medium text-gray-700"><?= View::e($u['nombre']) ?></td>
                        <td class="py-2 text-center text-gray-600"><?= number_format((float)$u['factor_conversion'], 2) ?></td>
                        <td class="py-2 text-right font-bold text-indigo-700">$<?= number_format((float)$u['precio_a'], 2) ?></td>
                        <td class="py-2 text-right font-bold text-emerald-700">$<?= number_format((float)$u['precio_b'], 2) ?></td>
                        <td class="py-2 text-right text-gray-400 font-mono text-xs"><?= View::e($u['codigo_barras'] ?: '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Inventario por depósito -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-warehouse text-gray-400 mr-2"></i>Inventario por Depósito</h3>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 uppercase border-b"><th class="pb-2">Depósito</th><th class="pb-2 text-right">Existencia</th><th class="pb-2 text-right">Mínimo</th><th class="pb-2 text-right">Últ. Costo</th></tr></thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach (($inventarios ?? []) as $inv): ?>
                    <tr>
                        <td class="py-2"><?= View::e($inv['deposito_nombre'] ?? 'N/A') ?></td>
                        <td class="py-2 text-right font-semibold"><?= number_format((float)$inv['existencia'], 0) ?></td>
                        <td class="py-2 text-right text-gray-500"><?= number_format((float)($inv['minimo'] ?? 0), 0) ?></td>
                        <td class="py-2 text-right text-gray-500">$<?= number_format((float)($inv['ultimo_costo'] ?? 0), 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($inventarios)): ?><tr><td colspan="4" class="py-4 text-center text-gray-400">Sin inventario</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Información</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Categoría</dt><dd class="font-medium text-gray-800"><?= View::e($categoria->nombre ?? '—') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Proveedor</dt><dd class="font-medium text-gray-800"><?= View::e($proveedor->nombre ?? '—') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Stock Total</dt><dd class="font-bold text-lg text-gray-800"><?= number_format($producto->stockTotal(), 0) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Stock Mínimo</dt><dd class="font-medium"><?= number_format((float)$producto->stock_minimo, 0) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Costo ($)</dt><dd class="font-bold text-gray-800">$<?= number_format((float)$producto->costo, 2) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">ITBMS</dt><dd class="font-medium">$<?= number_format((float)$producto->itbms, 2) ?></dd></div>
            </dl>
        </div>

        <!-- Agregar stock rápido -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-plus-circle text-green-500 mr-2"></i>Agregar Stock</h3>
            <form action="/inventario/<?= $producto->producto_id ?>/stock" method="POST" class="space-y-3">
                <?= View::csrf() ?>
                <select name="deposito_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Seleccionar depósito</option>
                    <?php foreach (($inventarios ?? []) as $inv): ?>
                        <option value="<?= $inv['deposito_id'] ?>"><?= View::e($inv['deposito_nombre'] ?? 'Depósito ' . $inv['deposito_id']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="cantidad" min="1" required placeholder="Cantidad" class="w-full border rounded-lg px-3 py-2 text-sm">
                <input type="number" name="costo" step="0.01" placeholder="Costo unitario" class="w-full border rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-1"></i> Agregar
                </button>
            </form>
        </div>
    </div>
</div>

<?php View::endSection('content'); ?>
