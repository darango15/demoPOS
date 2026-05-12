<?php use App\Core\View; View::layout('app'); ?>

<?php View::section('content'); ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Listado de Sucursales</h3>
            <p class="text-sm text-gray-500 mt-1">Administra las sucursales de tu empresa</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/configuracion/sucursales/nueva" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <i class="fas fa-plus"></i> Nueva Sucursal
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50/50 text-gray-700 text-xs uppercase font-semibold border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-4 rounded-tl-xl truncate">Código</th>
                    <th scope="col" class="px-6 py-4">Nombre / Principal</th>
                    <th scope="col" class="px-6 py-4">Dirección / Teléfono</th>
                    <th scope="col" class="px-6 py-4 text-center">Estado</th>
                    <th scope="col" class="px-6 py-4 text-right rounded-tr-xl">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/70">
                <?php if (empty($sucursales)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 bg-gray-50/30">
                        <i class="fas fa-store text-4xl mb-3 text-gray-300"></i>
                        <p class="text-gray-500 font-medium pb-2">No hay sucursales registradas</p>
                        <a href="/configuracion/sucursales/nueva" class="text-sky-600 hover:text-sky-700 hover:underline">
                            Crear tu primera sucursal
                        </a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($sucursales as $sucursal): ?>
                    <tr class="hover:bg-sky-50/30 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                <?= View::e($sucursal['codigo'] ?: 'N/A') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 group-hover:text-sky-600 transition-colors flex items-center gap-2">
                                <?= View::e($sucursal['nombre']) ?>
                                <?php if($sucursal['es_principal']): ?>
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 text-amber-600" title="Sucursal Principal">
                                        <i class="fas fa-star text-[0.6rem]"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm truncate max-w-xs text-gray-600" title="<?= View::e($sucursal['direccion']) ?>">
                                <i class="fas fa-map-marker-alt text-gray-400 w-4 text-center mr-1"></i>
                                <?= View::e($sucursal['direccion'] ?: 'Sin dirección') ?>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 flex items-center">
                                <i class="fas fa-phone-alt text-gray-400 w-4 text-center mr-1"></i>
                                <?= View::e($sucursal['telefono'] ?: 'Sin teléfono') ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <?php if ($sucursal['activa']): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activa
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Inactiva
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <a href="/configuracion/sucursales/<?= $sucursal['sucursal_id'] ?>/editar" class="p-1.5 text-gray-400 hover:text-sky-600 hover:bg-sky-50 rounded transition-colors" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/configuracion/sucursales/<?= $sucursal['sucursal_id'] ?>/eliminar" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta sucursal? Todos los datos asociados podrían verse afectados.');">
                                    <?= View::csrf() ?>
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php View::endSection('content'); ?>
