<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="max-w-[1600px] mx-auto relative" x-data="ventasForm()">
    <!-- Sistema de Notificaciones (Toasts) -->
    <div class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-8 scale-95"
                 x-transition:enter-end="opacity-100 transform translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-x-0 scale-100"
                 x-transition:leave-end="opacity-0 transform translate-x-8 scale-95"
                 class="pointer-events-auto flex items-center p-4 rounded-2xl shadow-2xl border backdrop-blur-xl min-w-[320px] max-w-md"
                 :class="{
                    'bg-emerald-500/90 border-emerald-400/50 text-white': toast.type === 'success',
                    'bg-rose-500/90 border-rose-400/50 text-white': toast.type === 'error',
                    'bg-amber-500/90 border-amber-400/50 text-white': toast.type === 'warning'
                 }">
                <div class="mr-4 flex-shrink-0 w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas" :class="{
                        'fa-check-circle': toast.type === 'success',
                        'fa-exclamation-circle': toast.type === 'error',
                        'fa-exclamation-triangle': toast.type === 'warning'
                    }"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-black uppercase tracking-widest opacity-70 mb-0.5" x-text="toast.type === 'success' ? 'Éxito' : 'Atención'"></p>
                    <p class="text-sm font-bold" x-text="toast.message"></p>
                </div>
                <button @click="removeToast(toast.id)" class="ml-4 opacity-50 hover:opacity-100 transition-opacity">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </template>
    </div>
    <!-- Header Principal Compacto -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-sky-500/10 rounded-xl flex items-center justify-center text-sky-500 shadow-inner">
                <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight" x-text="tipoDocumento === 'venta' ? 'NUEVA VENTA' : 'NUEVA COTIZACIÓN'"></h1>
                <p class="text-sm text-gray-500 font-medium flex items-center">
                    <span class="bg-gray-100 px-2 py-0.5 rounded mr-2" x-text="'N° ' + (tipoDocumento === 'venta' ? '<?= $siguiente_factura ?>' : '<?= $siguiente_cotizacion ?>')"></span>
                    <span class="hidden md:inline">•</span>
                    <span class="ml-2 hidden md:inline"><?= date('d M, Y') ?></span>
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3 bg-white/50 backdrop-blur-md p-1.5 rounded-xl border border-gray-200 shadow-sm">
            <button @click="tipoDocumento = 'venta'" :class="tipoDocumento === 'venta' ? 'bg-sky-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 flex items-center">
                <i class="fas fa-cash-register mr-2"></i> Factura
            </button>
            <button @click="tipoDocumento = 'cotizacion'" :class="tipoDocumento === 'cotizacion' ? 'bg-amber-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 flex items-center">
                <i class="fas fa-file-alt mr-2"></i> Cotización
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- COLUMNA PRINCIPAL: Búsqueda y Tabla -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Selector de Productos de Alta Visibilidad -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-visible">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative group">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Buscar Producto</label>
                        <div class="relative">
                            <input type="text" x-model="query" @input.debounce.300ms="buscarProductos()" 
                                   class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 border rounded-xl focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all text-gray-900 font-medium placeholder-gray-400"
                                   placeholder="Escanea código o escribe nombre...">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 group-focus-within:text-sky-500 transition-colors"></i>
                            </div>
                        </div>

                        <!-- Dropdown de Resultados con Glassmorphism -->
                        <div x-show="resultados.length > 0" x-transition.opacity 
                             class="absolute z-50 left-0 right-0 mt-2 bg-white/90 backdrop-blur-xl border border-gray-200 rounded-2xl shadow-2xl max-h-[400px] overflow-y-auto ring-1 ring-black/5">
                            <template x-for="p in resultados" :key="p.producto_id">
                                <div @click="seleccionarProducto(p)" 
                                     class="p-4 border-b border-gray-100 hover:bg-sky-500/5 cursor-pointer transition-all flex items-center justify-between group">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-white shadow-sm">
                                            <i class="fas fa-box text-gray-400 group-hover:text-sky-500"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm" x-text="p.nombre"></div>
                                            <div class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">
                                                <span class="text-sky-500" x-text="p.codigo"></span> • Stock: <span :class="p.stock > 0 ? 'text-green-600' : 'text-red-600'" x-text="p.stock"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-black text-gray-900" x-text="'$' + parseFloat(p.precio_a).toFixed(2)"></div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase">Precio Unit.</div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="w-full md:w-32">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Cant.</label>
                        <input type="number" x-model.number="cantidadBusqueda" @keyup.enter="agregarProductoSiSeleccionado()"
                               class="w-full py-3 bg-gray-50 border-gray-200 border rounded-xl text-center focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none font-black text-lg">
                    </div>

                    <div class="flex items-end">
                        <button @click="agregarProductoSiSeleccionado()" 
                                class="h-[50px] px-6 bg-sky-500 hover:bg-blue-600 text-white rounded-xl shadow-lg shadow-sky-500/20 flex items-center justify-center transition-all active:scale-95 group">
                            <i class="fas fa-plus text-lg group-hover:rotate-90 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Items Mejorada -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden min-h-[400px]">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-16">Item</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Descripción</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-24">Cant.</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center w-32">Unidad</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-32">Precio</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right w-32">Total</th>
                            <th class="px-6 py-4 text-center w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(item, index) in items" :key="item.id">
                            <tr class="group hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-bold text-sky-500 bg-sky-500/5 px-2 py-1 rounded" x-text="index + 1"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm" x-text="item.nombre"></div>
                                    <div class="text-[10px] text-gray-400 font-medium" x-text="item.codigo"></div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="number" x-model.number="item.cantidad" @change="calcularTotales()" 
                                           class="w-16 bg-transparent border-gray-200 border rounded-lg py-1 text-center font-bold text-sm focus:border-sky-500 focus:ring-0">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <select x-model="item.unidad_id" @change="cambiarUnidad(item)"
                                            class="w-full bg-transparent border-gray-200 border rounded-lg py-1 text-xs font-bold focus:border-sky-500 focus:ring-0">
                                        <option value="">Unidad (Base)</option>
                                        <template x-for="u in item.unidades_adicionales" :key="u.unidad_id">
                                            <option :value="u.unidad_id" x-text="u.nombre"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="relative inline-block">
                                        <span class="absolute left-2 top-1.5 text-[10px] text-gray-400">$</span>
                                        <input type="number" x-model.number="item.precio" @change="calcularTotales()" 
                                               class="w-24 pl-5 bg-transparent border-gray-200 border rounded-lg py-1 text-right font-bold text-sm focus:border-sky-500 focus:ring-0" step="0.01">
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black text-gray-900" x-text="'$' + calcularTotalLinea(item).toFixed(2)"></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="eliminarItem(index)" class="text-gray-300 hover:text-red-500 transition-colors p-2">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0">
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                        <i class="fas fa-shopping-basket text-gray-200 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Tu pedido está vacío</p>
                                    <p class="text-xs text-gray-300 mt-1">Busca productos para empezar la venta</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Notas Extra -->
            <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-5 border border-gray-200">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center">
                    <i class="fas fa-comment-dots mr-2 text-sky-500"></i> Notas / Observaciones
                </label>
                <textarea x-model="observaciones" rows="2" 
                          class="w-full bg-white border-gray-200 border rounded-xl p-3 text-sm focus:ring-4 focus:ring-sky-500/10 outline-none transition-all"
                          placeholder="Notas internas o para el cliente..."></textarea>
            </div>
        </div>

        <!-- SIDEBAR: Cliente y Totales -->
        <div class="lg:col-span-4 space-y-6 sticky top-6">
            <!-- Card Cliente -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Información del Cliente</h3>
                    <a href="/clientes/nuevo" class="text-sky-500 hover:text-blue-600 transition-colors">
                        <i class="fas fa-user-plus"></i>
                    </a>
                </div>
                
                <div class="space-y-4" x-data="{ open: false }">
                    <div class="relative group">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Cliente</label>
                        <div class="relative">
                            <input type="text" x-model="searchCliente" @click="open = true" @click.away="open = false"
                                   @input="open = true"
                                   placeholder="Buscar cliente..."
                                   class="w-full pl-10 pr-4 py-3 bg-gray-50 border-gray-200 border rounded-xl focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none font-bold text-sm transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-user-search text-gray-400 group-focus-within:text-sky-500 transition-colors"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-300 text-[10px]"></i>
                            </div>
                        </div>

                        <!-- Dropdown de Clientes -->
                        <div x-show="open" x-transition.opacity 
                             class="absolute z-50 left-0 right-0 mt-2 bg-white border border-gray-200 rounded-2xl shadow-2xl max-h-[300px] overflow-y-auto ring-1 ring-black/5">
                            <div @click="seleccionarCliente('', 'Consumidor Final', 'N/A', 'N/A', 'Consumidor Final'); open = false"
                                 class="p-4 border-b border-gray-100 hover:bg-sky-500/5 cursor-pointer transition-all flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-gray-400"></i>
                                </div>
                                <div class="font-bold text-gray-900 text-sm">Consumidor Final</div>
                            </div>
                            <?php foreach (($clientes ?? []) as $cliente): ?>
                            <div x-show="'<?= addslashes(strtolower($cliente['nombre'])) ?>'.includes(searchCliente.toLowerCase()) || '<?= $cliente['ruc'] ?>'.includes(searchCliente)"
                                 @click="seleccionarCliente('<?= $cliente['cliente_id'] ?>', '<?= addslashes($cliente['nombre']) ?>', '<?= $cliente['ruc'] ?>', '<?= $cliente['telefono'] ?>', '<?= addslashes($cliente['direccion']) ?>'); open = false"
                                 class="p-4 border-b border-gray-100 hover:bg-sky-500/5 cursor-pointer transition-all flex items-center justify-between group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-white shadow-sm">
                                        <i class="fas fa-user text-gray-400 group-hover:text-sky-500"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm"><?= View::e($cliente['nombre']) ?></div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-wider"><?= View::e($cliente['ruc']) ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div x-show="clienteId" x-transition.opacity class="bg-blue-50/50 rounded-xl p-4 border border-blue-100 border-dashed">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-[9px] font-black text-blue-400 uppercase">RUC / Cédula</div>
                                <div class="text-xs font-bold text-blue-900" x-text="clienteRuc"></div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-blue-400 uppercase">Teléfono</div>
                                <div class="text-xs font-bold text-blue-900" x-text="clienteTelefono"></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-[9px] font-black text-blue-400 uppercase">Dirección</div>
                            <div class="text-xs font-bold text-blue-900 truncate" x-text="clienteDireccion"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración del Documento -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-4">Ajustes del Documento</h3>
                <div class="grid grid-cols-1 gap-4">
                    <!-- Estado Dinámico -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Estado de <span x-text="tipoDocumento === 'venta' ? 'Factura' : 'Cotización'"></span></label>
                        <select x-model="estado" class="w-full px-3 py-2 bg-gray-50 border-gray-200 border rounded-lg text-sm font-bold focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 outline-none transition-all">
                            <template x-if="tipoDocumento === 'venta'">
                                <optgroup label="Estados de Venta">
                                    <option value="pagada">Pagada</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="credito">Crédito</option>
                                    <option value="anulada">Anulada</option>
                                </optgroup>
                            </template>
                            <template x-if="tipoDocumento === 'cotizacion'">
                                <optgroup label="Estados de Cotización">
                                    <option value="borrador">Borrador</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="aprobada">Aprobada</option>
                                    <option value="rechazada">Rechazada</option>
                                </optgroup>
                            </template>
                        </select>
                    </div>

                    <div x-show="tipoDocumento === 'venta'">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Forma de Pago</label>
                        <select x-model="formaPago" class="w-full px-3 py-2 bg-gray-50 border-gray-200 border rounded-lg text-sm font-bold focus:border-sky-500 outline-none">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="yappy">Yappy</option>
                            <option value="credito">Crédito</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Vencimiento</label>
                        <input type="date" value="<?= $fecha_validez ?>" class="w-full px-3 py-2 bg-gray-50 border-gray-200 border rounded-lg text-sm font-bold focus:border-sky-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- Card TOTALES -->
            <div class="bg-gray-900 rounded-3xl shadow-2xl p-6 text-white overflow-hidden relative">
                <!-- Efecto de brillo -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-sky-500/20 rounded-full blur-3xl -mr-16 -mt-16"></div>
                
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 relative">Resumen de Venta</h3>
                
                <div class="space-y-4 relative">
                    <div class="flex justify-between items-center text-sm font-medium">
                        <span class="text-gray-400">Subtotal:</span>
                        <span x-text="'$' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-medium">
                        <span class="text-gray-400">Descuento:</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-500 text-[10px]">$</span>
                            <input type="number" x-model.number="descuentoGlobal" @change="calcularTotales()" 
                                   class="w-16 bg-white/10 border-none rounded py-0.5 px-2 text-right text-sm font-bold focus:ring-1 focus:ring-sky-500 outline-none">
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-sm font-medium">
                        <span class="text-gray-400">ITBMS (7%):</span>
                        <span x-text="'$' + itbms.toFixed(2)"></span>
                    </div>
                    
                    <div class="pt-6 mt-4 border-t border-white/10">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-black text-sky-500 uppercase tracking-widest mb-1.5">Total a Pagar</span>
                            <div class="text-3xl font-black text-white" x-text="'$' + total.toFixed(2)"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-3 relative">
                    <button @click="procesarDocumento()" :disabled="processing"
                            class="w-full py-4 rounded-2xl bg-sky-500 hover:bg-blue-600 active:scale-95 transition-all text-white font-black text-sm uppercase tracking-widest shadow-xl shadow-sky-500/20 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <i x-show="!processing" class="fas fa-check-circle mr-2 text-base"></i>
                        <span x-show="!processing" x-text="tipoDocumento === 'venta' ? 'FINALIZAR VENTA' : 'GUARDAR COTIZACIÓN'"></span>
                        <i x-show="processing" class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-show="processing">PROCESANDO...</span>
                    </button>
                    
                    <button @click="vistaPrevia()" class="w-full py-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors text-gray-300 font-bold text-[10px] uppercase tracking-widest">
                        <i class="fas fa-print mr-2"></i> Generar Vista Previa
                    </button>
                </div>
            </div>
            
            <div class="text-center">
                <a href="/ventas" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-sky-500 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function ventasForm() {
        return {
            tipoDocumento: 'venta',
            clienteId: '',
            clienteNombre: 'Consumidor Final',
            searchCliente: 'Consumidor Final',
            clienteRuc: 'N/A',
            clienteTelefono: 'N/A',
            clienteDireccion: 'Consumidor Final',
            depositoId: '<?= $depositos[0]->deposito_id ?? '' ?>',
            formaPago: 'efectivo',
            estado: 'pagada',
            observaciones: '',
            query: '',
            cantidadBusqueda: 1,
            resultados: [],
            productoSeleccionado: null,
            items: [],
            subtotal: 0,
            itbms: 0,
            descuentoGlobal: 0,
            total: 0,
            cotizacionOrigenId: null,
            processing: false,
            toasts: [],

            init() {
                // Pre-cargar datos de cotización si vienen del controlador
                const preload = <?= json_encode($cotizacion_preload ?? null) ?>;
                if (preload) {
                    this.clienteId = preload.cliente_id || '';
                    this.clienteNombre = preload.cliente_nombre;
                    this.searchCliente = preload.cliente_nombre;
                    this.clienteRuc = preload.cliente_ruc;
                    this.clienteTelefono = preload.cliente_telefono;
                    this.clienteDireccion = preload.cliente_direccion;
                    this.observaciones = preload.notas;
                    this.items = preload.items;
                    this.cotizacionOrigenId = preload.cotizacion_id;
                    this.calcularTotales();
                    this.notify('Datos de cotización cargados', 'success');
                }

                this.$watch('tipoDocumento', (val) => {
                    this.estado = val === 'venta' ? 'pagada' : 'pendiente';
                });
            },

            notify(message, type = 'success') {
                const id = Date.now();
                this.toasts.push({ id, message, type, show: true });
                setTimeout(() => this.removeToast(id), 5000);
            },

            removeToast(id) {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index !== -1) {
                    this.toasts[index].show = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 500);
                }
            },

            seleccionarCliente(id, nombre, ruc, tel, dir) {
                this.clienteId = id;
                this.clienteNombre = nombre;
                this.searchCliente = nombre;
                this.clienteRuc = ruc || 'N/A';
                this.clienteTelefono = tel || 'N/A';
                this.clienteDireccion = dir || 'Consumidor Final';
            },

            buscarProductos() {
                if (this.query.length < 1) {
                    this.resultados = [];
                    return;
                }
                fetch(`/api/productos/buscar?q=${encodeURIComponent(this.query)}`)
                    .then(r => r.json())
                    .then(data => {
                        this.resultados = data.productos || [];
                        if (this.resultados.length === 0) {
                            this.notify('No se encontraron productos', 'warning');
                        }
                    });
            },

            seleccionarProducto(p) {
                this.productoSeleccionado = p;
                this.query = p.nombre;
                this.resultados = [];
            },

            agregarProductoSiSeleccionado() {
                if (!this.productoSeleccionado) {
                    this.notify('Selecciona un producto de la lista', 'warning');
                    return;
                }
                this.agregarItem(this.productoSeleccionado, this.cantidadBusqueda);
                this.productoSeleccionado = null;
                this.query = '';
                this.cantidadBusqueda = 1;
            },

            agregarItem(p, cant) {
                const index = this.items.findIndex(i => i.id === p.producto_id);
                if (index !== -1) {
                    this.items[index].cantidad += cant;
                } else {
                    this.items.push({
                        id: p.producto_id,
                        codigo: p.codigo,
                        nombre: p.nombre,
                        cantidad: cant,
                        unidad_id: '',
                        unidades_adicionales: p.unidades || [],
                        precio_base_a: parseFloat(p.precio_a) || 0,
                        precio_base_b: parseFloat(p.precio_b) || 0,
                        precio: parseFloat(p.precio_a) || 0,
                        descuento: 0,
                        applica_itbms: p.itbms == 1
                    });
                }
                this.calcularTotales();
                this.notify(`Producto añadido: ${p.nombre}`, 'success');
            },

            cambiarUnidad(item) {
                if (!item.unidad_id) {
                    // Volver a unidad base
                    item.precio = item.precio_base_a;
                } else {
                    const unit = item.unidades_adicionales.find(u => u.unidad_id == item.unidad_id);
                    if (unit) {
                        // Usar precio A de la unidad (podría mejorarse para seguir el tipo de cliente)
                        item.precio = parseFloat(unit.precio_a) || 0;
                    }
                }
                this.calcularTotales();
            },

            eliminarItem(index) {
                this.items.splice(index, 1);
                this.calcularTotales();
            },

            calcularTotalLinea(item) {
                return (item.cantidad * item.precio) - item.descuento;
            },

            calcularTotales() {
                this.subtotal = this.items.reduce((sum, item) => sum + this.calcularTotalLinea(item), 0);
                
                let gravable = this.items.reduce((sum, item) => {
                    if (item.applica_itbms) {
                        return sum + this.calcularTotalLinea(item);
                    }
                    return sum;
                }, 0);

                this.itbms = (gravable - (this.descuentoGlobal * (gravable/this.subtotal || 0))) * 0.07;
                if (this.itbms < 0) this.itbms = 0;
                
                this.total = this.subtotal - this.descuentoGlobal + this.itbms;
            },

            async procesarDocumento() {
                if (this.items.length === 0) {
                    this.notify('Agrega al menos un producto.', 'error');
                    return;
                }
                
                this.processing = true;
                
                const payload = {
                    tipo: this.tipoDocumento,
                    cliente_id: this.clienteId,
                    deposito_id: this.depositoId,
                    forma_pago: this.formaPago,
                    estado: this.estado,
                    descuento: this.descuentoGlobal,
                    notas: this.observaciones,
                    cotizacion_origen_id: this.cotizacionOrigenId,
                    items: this.items.map(i => ({
                        producto_id: i.id,
                        cantidad: i.cantidad,
                        unidad_id: i.unidad_id,
                        precio: i.precio,
                        descuento: i.descuento
                    }))
                };

                try {
                    const endpoint = this.tipoDocumento === 'venta' ? '/ventas/procesar' : '/ventas/cotizaciones/guardar';
                    const res = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= $_SESSION['csrf_token'] ?? '' ?>'
                        },
                        body: JSON.stringify(payload)
                    });
                    
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.error || 'Error al procesar');
                    
                    this.notify('Guardado con éxito', 'success');
                    setTimeout(() => {
                        window.location.href = this.tipoDocumento === 'venta' ? `/ventas/venta/${data.venta_id}` : `/ventas/cotizaciones/${data.cotizacion_id}`;
                    }, 1500);
                    
                } catch (e) {
                    this.notify(e.message, 'error');
                } finally {
                    this.processing = false;
                }
            },

            vistaPrevia() {
                this.notify('Vista previa no disponible por el momento.', 'warning');
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
    
    /* Scrollbar personalizada para el dropdown premium */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #CBD5E1; }

    /* Animación sutil para las filas */
    .detalle-producto {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<?php View::endSection('content'); ?>
