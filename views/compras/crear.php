<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div x-data="compraForm()" class="space-y-6">
    <div class="flex justify-between items-center bg-white/50 backdrop-blur-sm p-4 rounded-2xl border border-white/20">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Nueva Compra</h2>
            <p class="text-slate-500 text-sm">Registra la entrada de productos a inventario</p>
        </div>
        <div class="flex gap-3">
            <button @click="saveCompra(false)" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-2xl shadow-xl shadow-amber-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-clock"></i> Orden Pendiente
            </button>
            <button @click="saveCompra(true)" class="bg-blue-500 hover:bg-blue-500/90 text-white font-bold py-3 px-8 rounded-2xl shadow-xl shadow-blue-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-save"></i> Recibir Ahora
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
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="buscarProductos()" placeholder="Buscar producto por nombre o código..." 
                                class="w-full pl-12 pr-4 py-3 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                            
                            <!-- Dropdown de Resultados -->
                            <div x-show="resultados.length > 0" 
                                 class="absolute z-50 left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl max-h-[400px] overflow-y-auto overflow-x-hidden">
                                <template x-for="p in resultados" :key="p.producto_id">
                                    <div @click="seleccionarProducto(p)" 
                                         class="p-4 border-b border-slate-50 hover:bg-blue-50 cursor-pointer transition-all flex items-center justify-between group">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center group-hover:bg-white shadow-sm transition-colors">
                                                <i class="fas fa-box text-slate-400 group-hover:text-blue-500"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-sm" x-text="p.nombre"></div>
                                                <div class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">
                                                    <span class="text-blue-500" x-text="p.codigo"></span> • <span x-text="p.codigo_barras || 'Sin código de barras'"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs font-black text-slate-600" x-text="'$' + (parseFloat(p.costo) || 0).toFixed(4)"></div>
                                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Costo Base</div>
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
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">Presentación</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase" title="Unidades base que contiene cada unidad de compra">Factor</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">Cantidad</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">Costo</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">Depósito</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">N° Lote</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">Vencimiento</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase">Total</th>
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
                                        <select x-model="item.unidad_id" @change="onUnidadChange(item)" class="text-xs py-2 bg-slate-50 rounded-lg border-transparent focus:border-blue-500 focus:ring-0 min-w-[110px]">
                                            <option value="">Unidad Base</option>
                                            <template x-for="u in item.unidades" :key="u.unidad_id">
                                                <option :value="u.unidad_id" x-text="u.nombre"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" x-model.number="item.factor" @input="updateTotals()" min="1" step="1"
                                            title="Unidades base por paquete comprado"
                                            class="w-16 text-center py-2 bg-slate-50 rounded-lg border-transparent focus:border-blue-500 focus:ring-0 font-bold text-slate-700">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" x-model.number="item.cantidad" @input="updateTotals()" class="w-20 text-center py-2 bg-slate-50 rounded-lg border-transparent focus:border-blue-500 focus:ring-0">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <input type="number" step="0.01" x-model.number="item.costo" @input="updateTotals()" class="w-24 text-center py-2 bg-slate-50 rounded-lg border-transparent focus:border-blue-500 focus:ring-0 font-bold text-blue-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <select x-model="item.deposito_id" class="text-xs py-2 bg-slate-50 rounded-lg border-transparent focus:border-blue-500 focus:ring-0">
                                            <?php foreach ($depositos as $d): ?>
                                            <option value="<?= $d['deposito_id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <template x-if="item.maneja_lotes">
                                            <input type="text" x-model="item.numero_lote" placeholder="Requerido" class="w-28 text-center py-2 bg-slate-50 rounded-lg border-transparent focus:border-blue-500 focus:ring-0 text-sm border-amber-300">
                                        </template>
                                        <template x-if="!item.maneja_lotes">
                                            <div class="text-[10px] text-slate-300 font-bold uppercase text-center bg-slate-100/50 py-2 rounded-lg">N/A</div>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4">
                                        <template x-if="item.maneja_lotes">
                                            <input type="date" x-model="item.fecha_vencimiento" class="w-36 py-2 bg-slate-50 rounded-lg border-transparent focus:border-blue-500 focus:ring-0 text-sm">
                                        </template>
                                        <template x-if="!item.maneja_lotes">
                                            <div class="text-[10px] text-slate-300 font-bold uppercase text-center bg-slate-100/50 py-2 rounded-lg">Sin Lote</div>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-slate-700" x-text="'$' + (item.cantidad * item.costo).toFixed(2)"></td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="removeItem(index)" class="text-slate-300 hover:text-rose-500 transition-colors">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="items.length === 0">
                                <tr>
                                    <td colspan="8" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300">
                                                <i class="fas fa-shopping-basket text-2xl"></i>
                                            </div>
                                            <p class="text-slate-400 italic">No hay productos agregados</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar: Info y Totales -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Proveedor</label>
                    <select x-model="compra.proveedor_id" class="w-full py-3 px-4 rounded-xl bg-slate-50 border-transparent focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all font-medium">
                        <option value="">Seleccione Proveedor</option>
                        <?php foreach ($proveedores as $p): ?>
                        <option value="<?= $p->proveedor_id ?>"><?= htmlspecialchars($p->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Número Factura</label>
                    <input type="text" x-model="compra.numero_factura" class="w-full py-3 px-4 rounded-xl bg-slate-50 border-transparent focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all font-bold">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Fecha de Compra</label>
                    <input type="date" x-model="compra.fecha_compra" class="w-full py-3 px-4 rounded-xl bg-slate-50 border-transparent focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="bg-slate-900 rounded-3xl p-8 shadow-2xl text-white space-y-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/20 rounded-full blur-3xl"></div>
                
                <div class="flex justify-between items-center text-slate-400">
                    <span>Subtotal</span>
                    <span class="font-bold" x-text="'$' + totals.subtotal.toFixed(2)">$0.00</span>
                </div>
                <div class="flex justify-between items-center text-slate-400">
                    <span>ITBMS (7%)</span>
                    <span class="font-bold" x-text="'$' + totals.itbms.toFixed(2)">$0.00</span>
                </div>
                <div class="pt-4 border-t border-white/10 flex justify-between items-end">
                    <span class="text-xl font-bold">Total Pagar</span>
                    <span class="text-4xl font-extrabold text-blue-500" x-text="'$' + totals.total.toFixed(2)">$0.00</span>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 block">Notas</label>
                <textarea x-model="compra.notas" class="w-full p-4 rounded-xl bg-slate-50 border-transparent focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none h-24" placeholder="Observaciones de la entrada..."></textarea>
            </div>
        </div>
    </div>
</div>

<script>
function compraForm() {
    return {
        searchQuery: '',
        resultados: [],
        items: [],
        compra: {
            proveedor_id: '',
            numero_factura: '',
            fecha_compra: new Date().toISOString().split('T')[0],
            notas: ''
        },
        totals: {
            subtotal: 0,
            itbms: 0,
            total: 0
        },
        async buscarProductos() {
            if (this.searchQuery.length < 2) {
                this.resultados = [];
                return;
            }
            try {
                const response = await fetch(`/api/productos/buscar?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                this.resultados = data.productos || [];
            } catch (error) {
                console.error('Error buscando productos:', error);
            }
        },
        seleccionarProducto(p) {
            // Evitar duplicados
            const exists = this.items.find(i => i.producto_id === p.producto_id);
            if (exists) {
                exists.cantidad++;
            } else {
                this.items.push({
                    producto_id: p.producto_id,
                    nombre: p.nombre,
                    codigo: p.codigo,
                    cantidad: 1,
                    costo: parseFloat(p.costo || 0),
                    deposito_id: <?= $depositos[0]['deposito_id'] ?? 'null' ?>,
                    itbms: 0,
                    numero_lote: '',
                    fecha_vencimiento: '',
                    maneja_lotes: parseInt(p.maneja_lotes) === 1,
                    unidades: p.unidades || [],
                    unidad_id: '',
                    factor: 1
                });
            }
            this.searchQuery = '';
            this.resultados = [];
            this.updateTotals();
        },
        removeItem(index) {
            this.items.splice(index, 1);
            this.updateTotals();
        },
        onUnidadChange(item) {
            if (!item.unidad_id) {
                item.factor = 1;
                return;
            }
            const u = item.unidades.find(u => String(u.unidad_id) === String(item.unidad_id));
            if (u) item.factor = parseFloat(u.factor_conversion) || 1;
        },
        updateTotals() {
            let subtotal = 0;
            this.items.forEach(item => {
                subtotal += item.cantidad * item.costo;
            });
            this.totals.subtotal = subtotal;
            this.totals.itbms = subtotal * 0.07;
            this.totals.total = subtotal + this.totals.itbms;
        },
        async saveCompra(recibir = true) {
            if (!this.compra.proveedor_id) return alert('Seleccione un proveedor');
            if (this.items.length === 0) return alert('Agregue al menos un producto');

            if (recibir) {
                for (let item of this.items) {
                    if (item.maneja_lotes && (!item.numero_lote || !item.fecha_vencimiento)) {
                        return alert('El producto ' + item.nombre + ' maneja lotes. Debe ingresar N° Lote y Fecha de Vencimiento.');
                    }
                }
            }

            const accion = recibir ? 'registrar esta entrada de inventario' : 'guardar como orden pendiente (sin afectar stock)';
            if (!confirm('¿Seguro que desea ' + accion + '?')) return;

            try {
                const response = await fetch('/compras/guardar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        ...this.compra,
                        ...this.totals,
                        items: this.items,
                        guardar_como_pendiente: !recibir
                    })
                });

                const res = await response.json();
                if (res.success) {
                    window.location.href = recibir ? '/compras' : `/compras/${res.compra_id}`;
                } else {
                    alert('Error: ' + res.error);
                }
            } catch (error) {
                alert('Ocurrió un error al guardar la compra');
            }
        }
    }
}
</script>

<?php View::endSection('content'); ?>
