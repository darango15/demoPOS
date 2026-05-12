<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Métricas compactas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="metric-card bg-white rounded-lg p-4 shadow-xs">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500">Total Productos</p>
                <h3 class="text-lg font-bold text-gray-800"><?= $totalProductos ?? 0 ?></h3>
            </div>
            <div class="w-8 h-8 rounded-lg bg-sky-500 bg-opacity-10 flex items-center justify-center">
                <i class="fas fa-box text-sky-500"></i>
            </div>
        </div>
    </div>

    <div class="metric-card bg-white rounded-lg p-4 shadow-xs">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500">Categorías</p>
                <h3 class="text-lg font-bold text-gray-800"><?= $totalCategorias ?? 0 ?></h3>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-500 bg-opacity-10 flex items-center justify-center">
                <i class="fas fa-tags text-emerald-500"></i>
            </div>
        </div>
    </div>

    <div class="metric-card bg-white rounded-lg p-4 shadow-xs">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500">Stock Bajo</p>
                <h3 class="text-lg font-bold text-gray-800"><?= $productosStockBajo ?? 0 ?></h3>
            </div>
            <div class="w-8 h-8 rounded-lg bg-amber-500 bg-opacity-10 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-amber-500"></i>
            </div>
        </div>
    </div>

    <div class="metric-card bg-white rounded-lg p-4 shadow-xs">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500">Sin Stock</p>
                <h3 class="text-lg font-bold text-gray-800"><?= $productosSinStock ?? 0 ?></h3>
            </div>
            <div class="w-8 h-8 rounded-lg bg-red-500 bg-opacity-10 flex items-center justify-center">
                <i class="fas fa-times-circle text-red-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y búsqueda -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <form method="get" class="relative w-full md:w-64 mb-3 md:mb-0" id="search-form">
            <input type="text" name="buscar" value="<?= View::e($_GET['buscar'] ?? '') ?>" placeholder="Buscar producto..."
                class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-transparent"
                id="search-input" />
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
            <div id="search-spinner" class="absolute right-3 top-2.5 hidden">
                <i class="fas fa-spinner fa-spin text-sky-500"></i>
            </div>
            <!-- Mantener categoria si existe -->
            <?php if(isset($_GET['categoria'])): ?>
                <input type="hidden" name="categoria" value="<?= View::e($_GET['categoria']) ?>">
            <?php endif; ?>
        </form>

        <div class="flex items-center space-x-3">
            <a href="/inventario/nuevo"
                class="bg-sky-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg flex items-center text-sm transition-colors">
                <i class="fas fa-plus-circle mr-1.5"></i> Nuevo
            </a>
        </div>
    </div>

    <!-- Filtros de categoría -->
    <div class="flex overflow-x-auto space-x-2 mt-4 pb-1">
        <?php 
        $currentCat = $_GET['categoria'] ?? 'todos';
        ?>
        <a href="?categoria=todos<?= isset($_GET['buscar']) ? '&buscar='.View::e($_GET['buscar']) : '' ?>"
            class="category-filter <?= ($currentCat === 'todos') ? 'active bg-sky-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' ?> px-3 py-1.5 rounded-lg whitespace-nowrap text-sm transition-colors">
            Todos
        </a>
        <?php foreach (($categorias ?? []) as $cat): ?>
        <?php $catId = is_array($cat) ? ($cat['categoria_id'] ?? '') : ($cat->categoria_id ?? ''); ?>
        <a href="?categoria=<?= $catId ?><?= isset($_GET['buscar']) ? '&buscar='.View::e($_GET['buscar']) : '' ?>"
            class="category-filter <?= ($currentCat == $catId) ? 'active bg-sky-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' ?> px-3 py-1.5 rounded-lg whitespace-nowrap text-sm transition-colors">
            <?= View::e(is_array($cat) ? $cat['nombre'] : $cat->nombre) ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Lista de productos -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio A</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio B</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inventario</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach (($productos ?? []) as $producto): ?>
            <?php
            $pNombre = is_array($producto) ? ($producto['nombre'] ?? '') : ($producto->nombre ?? '');
            $pCodigo = is_array($producto) ? ($producto['codigo'] ?? '') : ($producto->codigo ?? '');
            $pId = is_array($producto) ? ($producto['producto_id'] ?? '') : ($producto->producto_id ?? '');
            $stock = is_array($producto) ? ($producto['stock_total'] ?? 0) : ($producto->stock_total ?? 0);
            $stockMin = is_array($producto) ? ($producto['stock_minimo'] ?? 0) : ($producto->stock_minimo ?? 0);
            $costo = is_array($producto) ? ($producto['costo'] ?? $producto['costo_promedio'] ?? 0) : ($producto->costo ?? $producto->costo_promedio ?? 0);
            
            $bgClass = '';
            if ($stock <= 0) {
                $bgClass = 'bg-red-50';
            } elseif ($stock <= $stockMin) {
                $bgClass = 'bg-yellow-50';
            }
            ?>
            <tr class="<?= $bgClass ?> hover:bg-gray-50 transition-colors">
                <!-- Producto y SKU -->
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            <?php $img = is_array($producto) ? ($producto['imagen_principal'] ?? '') : ($producto->imagen_principal ?? ''); ?>
                            <?php if ($img): ?>
                            <img src="/assets/uploads/<?= View::e($img) ?>" class="h-10 w-10 object-cover" onerror="this.onerror=null; this.innerHTML='<i class=\'fas fa-box text-gray-400\'></i>'; this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <i class="fas fa-box text-gray-400" style="display:none;"></i>
                            <?php else: ?>
                            <i class="fas fa-box text-gray-400"></i>
                            <?php endif; ?>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="/inventario/<?= $pId ?>" class="hover:text-sky-500 transition-colors">
                                    <?= View::e($pNombre) ?>
                                </a>
                            </div>
                            <div class="text-xs text-gray-500">SKU: <?= View::e($pCodigo) ?></div>
                        </div>
                    </div>
                </td>

                <!-- Precio A -->
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?php $precioA = is_array($producto) ? ($producto['precio_a'] ?? 0) : ($producto->precio_a ?? 0); ?>
                    <?php if ($precioA): ?>
                        $<?= number_format((float)$precioA, 2) ?>
                    <?php else: ?>
                        <span class="text-gray-400">-</span>
                    <?php endif; ?>
                </td>

                <!-- Precio B -->
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?php $precioB = is_array($producto) ? ($producto['precio_b'] ?? 0) : ($producto->precio_b ?? 0); ?>
                    <?php if ($precioB): ?>
                        $<?= number_format((float)$precioB, 2) ?>
                    <?php else: ?>
                        <span class="text-gray-400">-</span>
                    <?php endif; ?>
                </td>

                <!-- Costo -->
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                    $<?= number_format((float)$costo, 2) ?>
                </td>

                <!-- Inventario -->
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-medium"><?= number_format((float)$stock, 0) ?> unid.</div>
                    <?php 
                        $cantDepositos = is_array($producto) ? ($producto['cantidad_depositos'] ?? 1) : ($producto->cantidad_depositos ?? 1); 
                        $depNombre = is_array($producto) ? ($producto['deposito_principal'] ?? 'Principal') : ($producto->deposito_principal ?? 'Principal');
                    ?>
                    <div class="text-xs text-gray-500 mt-1">
                        <?php if ($cantDepositos == 1): ?>
                            <?= View::e($depNombre) ?>
                        <?php elseif ($cantDepositos > 1): ?>
                            <span class="text-sky-500 font-medium cursor-help" title="Múltiples depósitos">
                                <?= $cantDepositos ?> depósitos <i class="fas fa-info-circle text-xs"></i>
                            </span>
                        <?php else: ?>
                            Sin inventario
                        <?php endif; ?>
                    </div>
                </td>

                <!-- Estado -->
                <td class="px-4 py-3 whitespace-nowrap">
                    <?php if ($stock <= 0): ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Agotado</span>
                    <?php elseif ($stock <= $stockMin): ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Stock bajo</span>
                    <?php else: ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">En stock</span>
                    <?php endif; ?>
                </td>

                <!-- Acciones -->
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <a href="/inventario/<?= $pId ?>" class="text-sky-500 hover:text-sky-700 p-1" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/inventario/<?= $pId ?>/editar" class="text-green-500 hover:text-green-700 p-1" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/inventario/<?= $pId ?>/precios" class="text-yellow-500 hover:text-yellow-700 p-1" title="Gestionar precios">
                            <i class="fas fa-tag"></i>
                        </a>
                        <form method="post" action="/inventario/<?= $pId ?>/eliminar" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                            <?= View::csrf() ?>
                            <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($productos)): ?>
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <i class="fas fa-box-open text-gray-300 text-4xl mb-3"></i>
                        <p>No se encontraron productos.</p>
                        <a href="/inventario/nuevo" class="mt-2 text-sky-500 hover:underline font-medium">Crear uno nuevo</a>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Paginación -->
<?php if (!empty($productos)): ?>
<div class="mt-6">
    <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
</div>
<?php endif; ?>

<?php View::endSection('content'); ?>

<?php View::section('extra_js'); ?>
<script>
    // Búsqueda con debounce para evitar muchas consultas
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            clearTimeout(searchTimeout);
            const spinner = document.getElementById('search-spinner');

            if (e.target.value.length >= 3 || e.target.value.length === 0) {
                spinner.classList.remove('hidden');

                searchTimeout = setTimeout(() => {
                    document.getElementById('search-form').submit();
                }, 500); // Esperar 500ms después de que el usuario deje de escribir
            }
        });
    }
</script>
<?php View::endSection('extra_js'); ?>
