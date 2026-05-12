<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <form method="post" class="space-y-8">
        <?= View::csrf() ?>
        
        <!-- Información Básica -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i> Información Básica
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Código <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="codigo" value="<?= View::e($cliente->codigo ?? $nuevo_codigo ?? '') ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" 
                           placeholder="Ej: CLI-001" required>
                    <p class="mt-1 text-xs text-gray-500">Identificador único del cliente</p>
                </div>

                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo / Razón Social <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" value="<?= View::e($cliente->nombre ?? '') ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" 
                           placeholder="Nombre del cliente" required>
                </div>

                <!-- Tipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Persona <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" required>
                        <option value="natural" <?= ($cliente->tipo ?? '') == 'natural' ? 'selected' : '' ?>>Persona Natural</option>
                        <option value="juridico" <?= ($cliente->tipo ?? '') == 'juridico' ? 'selected' : '' ?>>Persona Jurídica</option>
                    </select>
                </div>

                <!-- RUC -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">RUC / Cédula</label>
                        <input type="text" name="ruc" value="<?= View::e($cliente->ruc ?? '') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" 
                               placeholder="Ej: 8-000-000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">DV</label>
                        <input type="text" name="dv" value="<?= View::e($cliente->dv ?? '') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" 
                               placeholder="00">
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Contacto -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                <i class="fas fa-address-book text-green-500 mr-2"></i> Información de Contacto
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono Principal</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-phone text-xs"></i>
                        </span>
                        <input type="text" name="telefono" value="<?= View::e($cliente->telefono ?? '') ?>" 
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" 
                               placeholder="Ej: +507 000-0000">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-envelope text-xs"></i>
                        </span>
                        <input type="email" name="email" value="<?= View::e($cliente->email ?? '') ?>" 
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" 
                               placeholder="cliente@ejemplo.com">
                    </div>
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección Principal</label>
                    <textarea name="direccion" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition" 
                              rows="2" placeholder="Dirección completa..."><?= View::e($cliente->direccion ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Información Financiera -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                <i class="fas fa-hand-holding-usd text-purple-500 mr-2"></i> Condiciones de Crédito
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Límite de Crédito -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Límite de Crédito ($)</label>
                    <input type="number" name="limite_credito" value="<?= View::e($cliente->limite_credito ?? '0.00') ?>" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                </div>

                <!-- Días de Crédito -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Días de Crédito</label>
                    <input type="number" name="dias_credito" value="<?= View::e($cliente->dias_credito ?? '0') ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                </div>

                <!-- ITBMS -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aplica ITBMS</label>
                    <select name="itbms" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                        <option value="SI" <?= (!isset($cliente->itbms) || $cliente->itbms > 0) ? 'selected' : '' ?>>Sí (Aplica ITBMS)</option>
                        <option value="NO" <?= (isset($cliente->itbms) && $cliente->itbms <= 0) ? 'selected' : '' ?>>No (Exento)</option>
                    </select>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado de Cuenta</label>
                    <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                        <option value="activo" <?= ($cliente->estado ?? '') == 'activo' ? 'selected' : '' ?>>Activo (Permitir ventas)</option>
                        <option value="inactivo" <?= ($cliente->estado ?? '') == 'inactivo' ? 'selected' : '' ?>>Inactivo (Bloquear ventas)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 pt-6 border-t border-gray-200">
            <a href="/clientes" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-6 rounded-lg text-sm inline-flex items-center transition">
                <i class="fas fa-arrow-left mr-2"></i> Cancelar y Volver
            </a>
            
            <div class="flex space-x-3">
                <button type="submit" name="save_and_add_another" class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-2 px-6 rounded-lg text-sm transition border border-blue-100">
                    Guardar y Nuevo
                </button>
                <button type="submit" class="bg-sky-500 hover:bg-blue-600 text-white font-bold py-2 px-8 rounded-lg text-sm transition shadow-sm">
                    <i class="fas fa-save mr-2"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </form>
</div>

<?php View::endSection('content'); ?>
