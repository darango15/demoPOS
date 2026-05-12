<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div x-data="trasladoForm()" class="space-y-6">
    <div class="flex justify-between items-center bg-white/50 backdrop-blur-sm p-4 rounded-2xl border border-white/20">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Nuevo Traslado</h2>
            <p class="text-slate-500 text-sm">Transfiere productos entre depósitos registrados</p>
        </div>
        <div class="flex gap-4">
            <button @click="saveTraslado()" class="bg-blue-500 hover:bg-blue-500/90 text-white font-bold py-3 px-8 rounded-2xl shadow-xl shadow-blue-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-paper-plane"></i> Enviar Mercancía
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Panel Principal: Items -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex gap-4">
                        <div class="flex-1 relative" @click.away="resultados = []">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="buscarProductos()" placeholder="Buscar producto para trasladar..." 
                                class="w-full pl-12 pr-4 py-3 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                            
                            <!-- Dropdown de Resultados -->
                            <div x-show="resultados.length > 0" 
                                 class="absolute z-50 left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl max-h-[300px] overflow-y-auto overflow-x-hidden">
                                <template x-for="p in resultados" :key="p.producto_id">
                                    <div @click="seleccionarProducto(p)" 
                                         class="p-4 border-b border-slate-50 hover:bg-blue-50 cursor-pointer transition-all flex items-center justify-between group">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center group-hover:bg-white shadow-sm transition-colors">
                                                <i class="fas fa-box text-slate-400 group-hover:text-blue-500"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-sm" x-text="p.nombre"></div>
                                                <div class="text-[10px] text-slate-400 font-medium tracking-tight">
                                                    <span class="text-blue-500 font-bold" x-text="p.codigo"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right px-2">
                                            <div class="text-blue-600">
                                                <i class="fas fa-plus-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase">Producto</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">Cantidad a Mover</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-700" x-text="item.nombre"></div>
                                        <div class="text-[10px] text-slate-400" x-text="item.codigo"></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center items-center gap-3">
                                            <button @click="item.cantidad > 1 ? item.cantidad-- : null" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200">-</button>
                                            <input type="number" x-model.number="item.cantidad" class="w-20 text-center py-2 bg-slate-50 rounded-lg border-transparent focus:border-blue-500 focus:ring-0 font-bold">
                                            <button @click="item.cantidad++" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200">+</button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="removeItem(index)" class="text-slate-300 hover:text-rose-500 transition-colors">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="items.length === 0">
                                <tr>
                                    <td colspan="3" class="px-6 py-20 text-center text-slate-400 italic">
                                        No hay productos seleccionados para el traslado.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar: Configuración -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-6">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Almacén de Origen</label>
                    <div class="relative">
                        <i class="fas fa-sign-out-alt absolute left-4 top-1/2 -translate-y-1/2 text-rose-400"></i>
                        <select x-model="traslado.origen_id" class="w-full py-3 pl-12 pr-4 rounded-xl bg-slate-50 border-transparent focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all font-bold text-slate-700">
                            <option value="">Seleccione Origen</option>
                            <?php foreach ($depositos_origen as $d): ?>
                            <option value="<?= $d['deposito_id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="flex justify-center -my-3 relative z-10">
                    <div class="w-10 h-10 bg-slate-50 rounded-full border border-slate-100 flex items-center justify-center text-slate-300 shadow-sm">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Almacén de Destino</label>
                    <div class="relative">
                        <i class="fas fa-sign-in-alt absolute left-4 top-1/2 -translate-y-1/2 text-emerald-400"></i>
                        <select x-model="traslado.destino_id" class="w-full py-3 pl-12 pr-4 rounded-xl bg-slate-50 border-transparent focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-bold text-slate-700">
                            <option value="">Seleccione Destino</option>
                            <?php foreach ($depositos_destino as $d): ?>
                            <option value="<?= $d['deposito_id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Notas / Observaciones</label>
                <textarea x-model="traslado.notas" class="w-full p-4 rounded-xl bg-slate-50 border-transparent focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none h-24" placeholder="¿Algún motivo especial para este traslado?"></textarea>
            </div>
            
            <div class="p-6 bg-blue-50 rounded-3xl border border-blue-100">
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 flex-shrink-0">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h4 class="text-blue-800 font-bold text-sm">Información</h4>
                        <p class="text-blue-600 text-xs mt-1 leading-relaxed">
                            Al enviar, los productos se descontarán del inventario origen y quedarán "En Tránsito" hasta que se confirme su recepción en el destino.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function trasladoForm() {
    return {
        searchQuery: '',
        resultados: [],
        items: [],
        traslado: {
            origen_id: '',
            destino_id: '',
            notas: ''
        },
        async buscarProductos() {
            if (this.searchQuery.length < 2) {
                this.resultados = [];
                return;
            }
            try {
                // Usamos el endpoint global de búsqueda que ya filtra por activos
                const response = await fetch(`/api/productos/buscar?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                this.resultados = data.productos || [];
            } catch (error) {
                console.error('Error buscando productos:', error);
            }
        },
        seleccionarProducto(p) {
            if (this.items.find(i => i.producto_id === p.producto_id)) {
                this.items.find(i => i.producto_id === p.producto_id).cantidad++;
            } else {
                this.items.push({
                    producto_id: p.producto_id,
                    nombre: p.nombre,
                    codigo: p.codigo,
                    cantidad: 1
                });
            }
            this.searchQuery = '';
            this.resultados = [];
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        async saveTraslado() {
            if (!this.traslado.origen_id) return alert('Seleccione un depósito de origen');
            if (!this.traslado.destino_id) return alert('Seleccione un depósito de destino');
            if (this.traslado.origen_id == this.traslado.destino_id) return alert('Origen y destino deben ser diferentes');
            if (this.items.length === 0) return alert('Agregue al menos un producto');
            
            if (!confirm('¿Desea realizar el envío de mercancía?')) return;

            try {
                const response = await fetch('/inventario/traslados/guardar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        ...this.traslado,
                        items: this.items
                    })
                });
                
                const res = await response.json();
                if (res.success) {
                    window.location.href = '/inventario/traslados';
                } else {
                    alert('Error: ' + res.error);
                }
            } catch (error) {
                alert('Ocurrió un error al guardar el traslado');
            }
        }
    }
}
</script>

<?php View::endSection('content'); ?>
