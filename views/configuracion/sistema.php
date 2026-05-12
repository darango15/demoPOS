<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<form method="post" action="/configuracion/guardar" class="space-y-6">
    <?= View::csrf() ?>
    
    <!-- Panel de Estado del Sistema -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Usuarios Activos</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['usuarios_activos'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Ventas</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_ventas'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-boxes text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Productos</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_productos'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-user-friends text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Clientes</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_clientes'] ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ajustes Generales -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajustes Generales</h3>
            <div class="space-y-4">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Negocio</label>
                    <input type="text" name="nombre_negocio" value="<?= View::e($configuracion['nombre_negocio']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ej: Mi Tienda POS">
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Moneda Principal</label>
                    <select name="moneda" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="USD" <?= $configuracion['moneda'] == 'USD' ? 'selected' : '' ?>>USD - Dólar Americano</option>
                        <option value="PAB" <?= $configuracion['moneda'] == 'PAB' ? 'selected' : '' ?>>PAB - Balboa Panameño</option>
                        <option value="EUR" <?= $configuracion['moneda'] == 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Impuesto ITBMS (%)</label>
                    <input type="number" name="impuesto" value="<?= $configuracion['impuesto'] ?>" step="0.01" min="0" max="100" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
        </div>

        <!-- Configuración de Facturación -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Facturación</h3>
            <div class="space-y-4">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Serie Facturación</label>
                    <input type="text" name="serie_facturacion" value="<?= View::e($configuracion['serie_facturacion']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ej: F001-001">
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resolución DGI</label>
                    <input type="text" name="resolucion_dgi" value="<?= View::e($configuracion['resolucion_dgi']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Número de resolución">
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Impresora Predeterminada</label>
                    <select name="impresora_predeterminada" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="ticket" <?= $configuracion['impresora_predeterminada'] == 'ticket' ? 'selected' : '' ?>>Ticket (58mm)</option>
                        <option value="a4" <?= $configuracion['impresora_predeterminada'] == 'a4' ? 'selected' : '' ?>>A4/Letter</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Configuración de Impresoras -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Impresoras Directas</h3>
            <div class="space-y-4">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Impresora de Ticket</label>
                    <input type="text" name="impresora_ticket" value="<?= View::e($configuracion['impresora_ticket']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ej: POS-58">
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-500">Nombre de la impresora de tickets</span>
                        <a href="#" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200">
                            <i class="fas fa-print mr-1"></i> Probar
                        </a>
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Impresora A4</label>
                    <input type="text" name="impresora_a4" value="<?= View::e($configuracion['impresora_a4']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ej: HP-LaserJet">
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-500">Para reportes y facturas</span>
                        <a href="#" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200">
                            <i class="fas fa-print mr-1"></i> Probar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seguridad y Backup -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Seguridad y Backup</h3>
            <div class="space-y-4">
                <div class="form-group flex items-center justify-between py-2 border-b border-gray-100">
                    <label class="text-sm font-medium text-gray-700">Backup Automático</label>
                    <input type="checkbox" name="backup_automatico" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= $configuracion['backup_automatico'] ? 'checked' : '' ?>>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora de Backup</label>
                    <input type="time" name="hora_backup" value="<?= View::e($configuracion['hora_backup']) ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <p class="text-xs text-gray-500 mt-1">Hora preferida para backups automáticos</p>
                </div>
                <div class="form-group flex items-center justify-between py-2 border-b border-gray-100">
                    <label class="text-sm font-medium text-gray-700">Sincronización Automática</label>
                    <input type="checkbox" name="sincronizacion_automatica" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= $configuracion['sincronizacion_automatica'] ? 'checked' : '' ?>>
                </div>
            </div>
        </div>

        <!-- AI Hub (Inteligencia de Negocio) -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-2 border-blue-500/10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                    <i class="fas fa-brain"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">AI Hub (Beta)</h3>
                <span class="px-2 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-bold rounded-full uppercase">Premium</span>
            </div>
            <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                Activa las funciones de Inteligencia Artificial para recibir predicciones de stock, detectar clientes VIP y obtener sugerencias automáticas para tu negocio.
            </p>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                    <div>
                        <p class="text-sm font-bold text-slate-700">Activar Inteligencia Artificial</p>
                        <p class="text-[10px] text-slate-400">Habilita insights y predicciones proactivas</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="ai_enabled" value="1" class="sr-only peer" <?= ($empresa->ai_enabled ?? 0) ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 flex gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-[10px] text-blue-700">
                        Nota: Algunas funciones de IA requieren procesamiento en la nube y pueden tener un coste adicional según tu volumen de datos.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Sistema -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado del Sistema</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600"><?= $system['uso_cpu'] ?>%</div>
                <p class="text-sm text-gray-600">Uso de CPU</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $system['uso_cpu'] ?>%"></div>
                </div>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600"><?= $system['uso_memoria'] ?>%</div>
                <p class="text-sm text-gray-600">Uso de Memoria</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: <?= $system['uso_memoria'] ?>%"></div>
                </div>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-600"><?= $system['uso_disco'] ?>%</div>
                <p class="text-sm text-gray-600">Uso de Disco</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: <?= $system['uso_disco'] ?>%"></div>
                </div>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold <?= $system['debug_mode'] ? 'text-red-600' : 'text-green-600' ?>">
                    <?= $system['debug_mode'] ? 'DEBUG' : 'PROD' ?>
                </div>
                <p class="text-sm text-gray-600">Modo</p>
                <p class="text-xs text-gray-500"><?= View::e($system['sistema_operativo']) ?></p>
            </div>
        </div>
    </div>

    <!-- Botón Guardar -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Guardar Configuración</h3>
                <p class="text-sm text-gray-600">Los cambios se aplicarán inmediatamente</p>
            </div>
            <button type="submit" class="bg-sky-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition shadow-sm">
                <i class="fas fa-save mr-2"></i> Guardar Cambios
            </button>
        </div>
    </div>
</form>

<!-- Acciones Rápidas -->
<div class="bg-white rounded-xl shadow-sm p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mantenimiento</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="#" class="bg-blue-50 hover:bg-blue-100 text-blue-800 font-medium py-4 px-4 rounded-xl text-center transition duration-200 block border border-blue-100">
            <i class="fas fa-download text-2xl mb-2"></i>
            <p class="font-semibold text-sm">Respaldar Datos</p>
            <p class="text-xs text-blue-600 mt-1">Descargar backup SQL</p>
        </a>
        
        <a href="#" class="bg-green-50 hover:bg-green-100 text-green-800 font-medium py-4 px-4 rounded-xl text-center transition duration-200 block border border-green-100">
            <i class="fas fa-sync-alt text-2xl mb-2"></i>
            <p class="font-semibold text-sm">Sincronizar</p>
            <p class="text-xs text-green-600 mt-1">Optimizar base de datos</p>
        </a>
        
        <a href="#" class="bg-red-50 hover:bg-red-100 text-red-800 font-medium py-4 px-4 rounded-xl text-center transition duration-200 block border border-red-100">
            <i class="fas fa-broom text-2xl mb-2"></i>
            <p class="font-semibold text-sm">Limpiar Cache</p>
            <p class="text-xs text-red-600 mt-1">Liberar memoria temporal</p>
        </a>
    </div>
</div>

<?php View::endSection('content'); ?>
