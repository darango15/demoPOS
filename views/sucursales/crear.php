<?php use App\Core\View; View::layout('app'); ?>

<?php View::section('content'); ?>
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Nueva Sucursal</h3>
            <p class="text-sm text-gray-500 mt-1">Completa los datos para registrar una nueva sucursal</p>
        </div>

        <form action="/configuracion/sucursales/guardar" method="POST" class="p-6">
            <?= View::csrf() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información Principal -->
                <div class="col-span-1 md:col-span-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Información Principal</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="codigo" class="form-label">Código Sucursal</label>
                            <input type="text" id="codigo" name="codigo" class="form-input" placeholder="Ej: SUC-001" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre Sucursal</label>
                            <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ej: Sucursal Centro" required>
                        </div>
                    </div>
                </div>

                <!-- Contacto & Ubicación -->
                <div class="col-span-1 md:col-span-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 mt-2">Contacto y Ubicación</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-input" placeholder="Teléfono de contacto">
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="correo@sucursal.com">
                        </div>
                        
                        <div class="form-group md:col-span-2">
                            <label for="direccion" class="form-label">Dirección Completa</label>
                            <input type="text" id="direccion" name="direccion" class="form-input" placeholder="Dirección física de la sucursal">
                        </div>
                    </div>
                </div>

                <!-- Configuraciones -->
                <div class="col-span-1 md:col-span-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 mt-2">Configuraciones</h4>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center gap-6 mt-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="es_principal" name="es_principal" value="1" class="form-checkbox h-4 w-4 text-sky-600 rounded">
                                <label for="es_principal" class="ml-2 block text-sm font-medium text-gray-700">
                                    Es Sucursal Principal
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="activa" name="activa" value="1" class="form-checkbox h-4 w-4 text-sky-600 rounded" checked>
                                <label for="activa" class="ml-2 block text-sm font-medium text-gray-700">
                                    Sucursal Activa
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="/configuracion/sucursales" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar Sucursal
                </button>
            </div>
        </form>
    </div>
</div>
<?php View::endSection('content'); ?>
