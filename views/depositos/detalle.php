<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="space-y-6">
    <!-- Encabezado y Datos de la Bodega -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fas fa-warehouse text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900"><?= View::e($deposito->nombre) ?></h2>
                    <p class="text-sm text-gray-500 font-mono"><?= View::e($deposito->codigo) ?></p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $deposito->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <span class="h-1.5 w-1.5 rounded-full mr-2 <?= $deposito->estado === 'activo' ? 'bg-green-500' : 'bg-red-500' ?>"></span>
                    <?= ucfirst($deposito->estado) ?>
                </span>
                <a href="/inventario/depositos/<?= $deposito->deposito_id ?>/editar" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-edit text-xs"></i> Editar Bodega
                </a>
                <form action="/inventario/depositos/<?= $deposito->deposito_id ?>/eliminar" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta bodega?')">
                    <?= View::csrf() ?>
                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>

        <?php if (!empty($deposito->descripcion)): ?>
        <div class="mt-4 pt-4 border-t border-gray-50">
            <p class="text-sm text-gray-600 italic">"<?= View::e($deposito->descripcion) ?>"</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tabla de Inventario -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-boxes text-blue-500"></i> Existencias en esta Bodega
            </h3>
            <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg">
                <?= count($inventario) ?> Productos registrados
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50/50 text-gray-500 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Producto</th>
                        <th class="px-6 py-3 text-center">Código</th>
                        <th class="px-6 py-3 text-center">Stock Mínimo</th>
                        <th class="px-6 py-3 text-right">Existencia Actual</th>
                        <th class="px-6 py-3">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($inventario)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-box-open text-3xl opacity-20"></i>
                                <p>Esta bodega no tiene productos registrados en inventario.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($inventario as $item): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($item['imagen_principal'])): ?>
                                    <img src="<?= $item['imagen_principal'] ?>" class="h-8 w-8 rounded-lg object-cover shadow-sm bg-gray-100">
                                <?php else: ?>
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-xs"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="font-medium text-gray-900"><?= View::e($item['producto_nombre']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center font-mono text-xs text-gray-500">
                            <?= View::e($item['codigo']) ?>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500">
                            <?= number_format((float)$item['minimo'], 2) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php 
                            $stock = (float)$item['existencia'];
                            $min = (float)$item['minimo'];
                            $colorClass = $stock <= $min ? 'text-red-600 font-bold' : 'text-gray-900 font-medium';
                            ?>
                            <span class="<?= $colorClass ?>">
                                <?= number_format($stock, 2) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($stock <= $min): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase">
                                    Stock Bajo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase">
                                    Normal
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
    
    <div class="flex justify-start">
        <a href="/inventario/depositos" class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i> Volver a la lista de bodegas
        </a>
    </div>
</div>

<?php View::endSection('content'); ?>
