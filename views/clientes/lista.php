<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Filtros y Búsqueda -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="get" class="space-y-4 md:space-y-0" id="searchForm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Búsqueda -->
            <div class="relative w-full md:w-80">
                <input type="text" name="buscar" value="<?= View::e($_GET['buscar'] ?? '') ?>" 
                       placeholder="Buscar por código, nombre o RUC..." 
                       class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-transparent">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
            </div>

            <!-- Filtros -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <select name="estado" class="text-sm py-2 pl-3 pr-8 rounded-lg border border-gray-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="activo" <?= ($_GET['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activos</option>
                    <option value="inactivo" <?= ($_GET['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
                </select>
                
                <select name="tipo" class="text-sm py-2 pl-3 pr-8 rounded-lg border border-gray-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-transparent">
                    <option value="">Todos los tipos</option>
                    <option value="natural" <?= ($_GET['tipo'] ?? '') === 'natural' ? 'selected' : '' ?>>Natural</option>
                    <option value="juridico" <?= ($_GET['tipo'] ?? '') === 'juridico' ? 'selected' : '' ?>>Jurídico</option>
                </select>
                
                <button type="submit" class="bg-sky-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg flex items-center text-sm transition-colors">
                    <i class="fas fa-filter mr-1.5"></i> Filtrar
                </button>
                
                <a href="/clientes" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg flex items-center text-sm transition-colors cursor-pointer">
                    <i class="fas fa-redo mr-1.5"></i> Limpiar
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tabla Optimizada -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-gray-700">Clientes (<?= count($clientes ?? []) ?> en total)</span>
        </div>
        
        <a href="/clientes/nuevo" class="bg-sky-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg flex items-center text-sm transition-colors">
            <i class="fas fa-user-plus mr-1.5"></i> Nuevo Cliente
        </a>
    </div>

    <!-- Tabla simplificada para mejor rendimiento -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="clientesTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                    <!-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Límite</th> -->
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Saldo</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach (($clientes ?? []) as $cliente): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?= View::e($cliente->codigo) ?>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900"><?= View::e($cliente->nombre) ?></div>
                                <div class="text-xs text-gray-500"><?= View::e($cliente->tipo_cliente ?? 'N/A') ?></div>
                                <?php if (!empty($cliente->documento)): ?>
                                <div class="text-xs text-gray-400">Doc: <?= View::e($cliente->documento) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                        <div><?= View::e($cliente->telefono ?: 'Sin teléfono') ?></div>
                        <div class="text-xs text-gray-500"><?= View::e($cliente->email ?: 'Sin email') ?></div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $cliente->estado === 'activo' ? 'bg-emerald-500 bg-opacity-10 text-emerald-500' : 'bg-red-500 bg-opacity-10 text-red-500' ?>">
                            <?= View::e(ucfirst($cliente->estado)) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 font-medium <?= $cliente->saldo > 0 ? 'text-red-500' : '' ?>">
                        $<?= number_format((float)$cliente->saldo, 2) ?>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <!-- Botón Ver -->
                            <a href="/clientes/<?= $cliente->cliente_id ?>" 
                               class="text-blue-500 hover:text-blue-700 p-1 rounded transition-colors"
                               title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <!-- Botón Nueva Venta -->
                            <a href="/ventas/nueva?cliente=<?= $cliente->cliente_id ?>" 
                               class="text-emerald-500 hover:text-emerald-700 p-1 rounded transition-colors"
                               title="Nueva Venta">
                                <i class="fas fa-shopping-cart"></i>
                            </a>

                            <!-- Botón Editar -->
                            <a href="/clientes/<?= $cliente->cliente_id ?>/editar" 
                               class="text-green-500 hover:text-green-700 p-1 rounded transition-colors"
                               title="Editar cliente">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <!-- Botón Eliminar -->
                            <form method="post" action="/clientes/<?= $cliente->cliente_id ?>/eliminar" 
                                  class="inline" 
                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar a <?= View::e($cliente->nombre) ?>?');">
                                <?= View::csrf() ?>
                                <button type="submit" 
                                        class="text-red-500 hover:text-red-700 p-1 rounded transition-colors"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($clientes)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-users text-4xl"></i>
                        </div>
                        <p class="text-gray-500">No se encontraron clientes.</p>
                        <?php if (!empty($_GET['buscar']) || !empty($_GET['estado']) || !empty($_GET['tipo'])): ?>
                        <p class="text-sm text-gray-400 mt-1">Intenta ajustar los filtros de búsqueda</p>
                        <?php endif; ?>
                        <div class="mt-4">
                            <a href="/clientes/nuevo" class="text-sky-500 hover:underline font-medium">
                                Crear uno nuevo
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación Simplificada -->
    <?php if (!empty($clientes)): ?>
    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 mt-4">
        <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
    </div>
    <?php endif; ?>
</div>

<?php View::endSection('content'); ?>
