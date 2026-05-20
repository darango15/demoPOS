<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Métricas compactas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl p-4 shadow-sm">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500 mb-1">Total Productos</p>
                <h3 class="text-xl font-bold text-gray-800"><?= $totalProductos ?? 0 ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center"><i class="fas fa-box text-sky-500 text-sm"></i></div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500 mb-1">Categorías</p>
                <h3 class="text-xl font-bold text-gray-800"><?= $totalCategorias ?? 0 ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="fas fa-tags text-emerald-500 text-sm"></i></div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500 mb-1">Stock Bajo</p>
                <h3 class="text-xl font-bold text-gray-800"><?= $productosStockBajo ?? 0 ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center"><i class="fas fa-exclamation-triangle text-amber-500 text-sm"></i></div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500 mb-1">Sin Stock</p>
                <h3 class="text-xl font-bold text-gray-800"><?= $productosSinStock ?? 0 ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center"><i class="fas fa-times-circle text-red-500 text-sm"></i></div>
        </div>
    </div>
</div>

<!-- Filtros + acciones -->
<?php $currentCat = $_GET['categoria'] ?? ''; ?>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="get" id="search-form" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar</label>
            <div class="relative">
                <i class="fas fa-search absolute left-0 top-2 text-gray-400 text-xs"></i>
                <input type="text" name="buscar" value="<?= View::e($_GET['buscar'] ?? '') ?>"
                    placeholder="Buscar producto..." id="search-input"
                    class="w-full pl-5 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
                <div id="search-spinner" class="absolute right-0 top-2 hidden"><i class="fas fa-spinner fa-spin text-sky-500 text-xs"></i></div>
            </div>
        </div>
        <div class="min-w-44">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Categoría</label>
            <select name="categoria" id="categoria-select" onchange="this.form.submit()"
                class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todas las categorías</option>
                <?php foreach (($categorias ?? []) as $cat):
                    $catId = is_array($cat) ? ($cat['categoria_id'] ?? '') : ($cat->categoria_id ?? '');
                ?>
                <option value="<?= $catId ?>" <?= ($currentCat == $catId) ? 'selected' : '' ?>>
                    <?= View::e(is_array($cat) ? $cat['nombre'] : $cat->nombre) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="min-w-32">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Estado</label>
            <select name="estado" id="estado-select" onchange="this.form.submit()"
                class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todos</option>
                <option value="activo"   <?= (($_GET['estado'] ?? '') === 'activo')   ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= (($_GET['estado'] ?? '') === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        <div class="flex gap-2">
            <?php if (!empty($_GET['buscar']) || !empty($currentCat) || !empty($_GET['estado'])): ?>
            <a href="/inventario" class="inline-flex items-center gap-1 px-3 py-2 border border-gray-200 text-gray-400 rounded-lg text-sm hover:bg-gray-50 transition" title="Limpiar">
                <i class="fas fa-times"></i>
            </a>
            <?php endif; ?>
            <a href="/inventario/exportar-excel" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition shadow-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="/inventario/nuevo" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-plus"></i> Nuevo
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead>
            <tr>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Producto</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Precio A</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Precio B</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Costo</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Inventario</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach (($productos ?? []) as $producto): ?>
            <?php
            $pNombre = is_array($producto) ? ($producto['nombre'] ?? '') : ($producto->nombre ?? '');
            $pCodigo = is_array($producto) ? ($producto['codigo'] ?? '') : ($producto->codigo ?? '');
            $pId = is_array($producto) ? ($producto['producto_id'] ?? '') : ($producto->producto_id ?? '');
            $stock = is_array($producto) ? ($producto['stock_total'] ?? 0) : ($producto->stock_total ?? 0);
            $stockMin = is_array($producto) ? ($producto['stock_minimo'] ?? 0) : ($producto->stock_minimo ?? 0);
            $costo = is_array($producto) ? ($producto['costo'] ?? $producto['costo_promedio'] ?? 0) : ($producto->costo ?? $producto->costo_promedio ?? 0);
            $bgClass = '';
            if ($stock <= 0) $bgClass = 'bg-red-50/60';
            elseif ($stock <= $stockMin) $bgClass = 'bg-amber-50/60';
            ?>
            <tr class="<?= $bgClass ?> hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                            <?php $img = is_array($producto) ? ($producto['imagen_principal'] ?? '') : ($producto->imagen_principal ?? ''); ?>
                            <?php if ($img): ?>
                            <img src="/assets/uploads/<?= View::e($img) ?>" class="h-9 w-9 object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <i class="fas fa-box text-gray-400 text-sm hidden"></i>
                            <?php else: ?>
                            <i class="fas fa-box text-gray-400 text-sm"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="/inventario/<?= $pId ?>" class="text-sm font-semibold text-gray-900 hover:text-sky-500 transition-colors"><?= View::e($pNombre) ?></a>
                            <div class="text-xs text-gray-400">SKU: <?= View::e($pCodigo) ?></div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?php $precioA = is_array($producto) ? ($producto['precio_a'] ?? 0) : ($producto->precio_a ?? 0); ?>
                    <?= $precioA ? '$' . number_format((float)$precioA, 2) : '<span class="text-gray-300">—</span>' ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?php $precioB = is_array($producto) ? ($producto['precio_b'] ?? 0) : ($producto->precio_b ?? 0); ?>
                    <?= $precioB ? '$' . number_format((float)$precioB, 2) : '<span class="text-gray-300">—</span>' ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">$<?= number_format((float)$costo, 2) ?></td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900"><?= number_format((float)$stock, 0) ?> unid.</div>
                    <?php
                        $cantDepositos = is_array($producto) ? ($producto['cantidad_depositos'] ?? 1) : ($producto->cantidad_depositos ?? 1);
                        $depNombre = is_array($producto) ? ($producto['deposito_principal'] ?? 'Principal') : ($producto->deposito_principal ?? 'Principal');
                    ?>
                    <div class="text-xs text-gray-400 mt-0.5">
                        <?php if ($cantDepositos == 1): ?>
                            <?= View::e($depNombre) ?>
                        <?php elseif ($cantDepositos > 1): ?>
                            <span class="text-sky-500 font-medium"><?= $cantDepositos ?> depósitos</span>
                        <?php else: ?>
                            Sin inventario
                        <?php endif; ?>
                    </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <?php if ($stock <= 0): ?>
                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-700">Agotado</span>
                    <?php elseif ($stock <= $stockMin): ?>
                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Stock bajo</span>
                    <?php else: ?>
                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">En stock</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                    <div class="flex justify-end gap-2">
                        <a href="/inventario/<?= $pId ?>" class="text-sky-500 hover:text-sky-700" title="Ver"><i class="fas fa-eye"></i></a>
                        <a href="/inventario/<?= $pId ?>/editar" class="text-gray-400 hover:text-blue-600" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="/inventario/<?= $pId ?>/precios" class="text-amber-500 hover:text-amber-700" title="Precios"><i class="fas fa-tag"></i></a>
                        <form method="post" action="/inventario/<?= $pId ?>/eliminar" class="inline" onsubmit="return confirm('¿Eliminar este producto?')">
                            <?= View::csrf() ?>
                            <button type="submit" class="text-gray-300 hover:text-red-600" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($productos)): ?>
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">
                    <i class="fas fa-box-open text-3xl mb-2 block text-gray-200"></i>
                    No se encontraron productos.
                    <a href="/inventario/nuevo" class="block mt-1 text-sky-500 hover:underline font-medium">Crear uno nuevo</a>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($productos)): ?>
<div class="mt-4">
    <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
</div>
<?php endif; ?>

<?php View::endSection('content'); ?>

<?php View::section('extra_js'); ?>
<script>
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const spinner = document.getElementById('search-spinner');
            if (e.target.value.length >= 2 || e.target.value.length === 0) {
                spinner.classList.remove('hidden');
                searchTimeout = setTimeout(() => { document.getElementById('search-form').submit(); }, 500);
            }
        });
    }
</script>
<?php View::endSection('extra_js'); ?>
