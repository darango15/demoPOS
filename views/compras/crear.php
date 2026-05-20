<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div x-data="compraForm()">

    <!-- Breadcrumb -->
    <p class="text-xs text-gray-400 mb-2">
        <a href="/compras" class="hover:text-sky-500 transition-colors">Compras</a>
        <span class="mx-1">/</span>
        <span class="text-gray-600 font-medium">Nueva Orden</span>
    </p>

    <!-- Barra de acciones + pipeline de estado -->
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div class="flex items-center gap-2">
            <button @click="saveCompra(false)"
                class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2 px-5 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <i class="fas fa-clock text-xs"></i> Orden Pendiente
            </button>
            <button @click="saveCompra(true)"
                class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold py-2 px-5 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <i class="fas fa-save text-xs"></i> Recibir Ahora
            </button>
            <a href="/compras"
                class="text-sm font-medium text-gray-500 hover:text-red-500 py-2 px-4 rounded-lg border border-gray-200 hover:border-red-200 transition-colors">
                Cancelar
            </a>
        </div>

        <!-- Pipeline de estado (estilo Odoo) -->
        <div class="flex items-stretch text-xs font-semibold select-none">
            <div class="flex items-center bg-sky-500 text-white pl-4 pr-6 py-2 rounded-l-lg relative">
                Solicitud
                <span class="absolute right-0 top-0 h-full w-3 overflow-hidden translate-x-2.5 z-10">
                    <span class="block w-6 h-6 bg-sky-500 rotate-45 origin-top-left mt-0.5 ml-0.5"></span>
                </span>
            </div>
            <div class="flex items-center bg-gray-100 text-gray-400 pl-7 pr-5 py-2 rounded-r-lg">
                Recibida
            </div>
        </div>
    </div>

    <!-- Documento principal -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Título grande -->
        <div class="px-8 pt-7 pb-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Nueva Orden de Compra</p>
            <h1 class="text-3xl font-bold text-gray-800">Nueva Compra</h1>
        </div>

        <!-- Campos del encabezado: 2 columnas -->
        <div class="px-8 pb-6 grid grid-cols-1 md:grid-cols-2 gap-x-20 gap-y-5 border-b border-gray-100">

            <!-- Columna izquierda -->
            <div class="space-y-5">
                <div class="flex items-baseline gap-4">
                    <label class="text-sm font-semibold text-gray-700 w-44 shrink-0">Proveedor</label>
                    <select x-model="compra.proveedor_id"
                        class="flex-1 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none text-gray-800">
                        <option value="">— Seleccionar —</option>
                        <?php foreach ($proveedores as $p): ?>
                        <option value="<?= $p->proveedor_id ?>"><?= htmlspecialchars($p->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-baseline gap-4">
                    <label class="text-sm font-semibold text-gray-700 w-44 shrink-0">N° Factura Proveedor</label>
                    <input type="text" x-model="compra.numero_factura" placeholder="Ej. FAC-001"
                        class="flex-1 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none text-gray-800">
                </div>
            </div>

            <!-- Columna derecha -->
            <div class="space-y-5">
                <div class="flex items-baseline gap-4">
                    <label class="text-sm font-semibold text-gray-700 w-44 shrink-0">Fecha de Compra</label>
                    <input type="date" x-model="compra.fecha_compra"
                        class="flex-1 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none text-gray-800">
                </div>
                <div class="flex items-baseline gap-4">
                    <label class="text-sm font-semibold text-gray-700 w-44 shrink-0">Llegada Prevista</label>
                    <input type="date" x-model="compra.fecha_esperada"
                        class="flex-1 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none text-gray-800">
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div x-data="{ tab: 'productos' }">

            <!-- Cabeceras de tabs -->
            <div class="flex border-b border-gray-100 px-8">
                <button @click="tab = 'productos'"
                    :class="tab === 'productos' ? 'border-b-2 border-sky-500 text-sky-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="text-sm py-3 px-1 mr-8 transition-colors -mb-px">
                    Productos
                </button>
                <button @click="tab = 'notas'"
                    :class="tab === 'notas' ? 'border-b-2 border-sky-500 text-sky-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="text-sm py-3 px-1 transition-colors -mb-px">
                    Notas
                </button>
            </div>

            <!-- Tab: Productos -->
            <div x-show="tab === 'productos'" class="px-6 pt-4 pb-2">

                <!-- Buscador de productos -->
                <div class="relative mb-3" @click.away="resultados = []">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                    <input type="text" x-model="searchQuery" @input.debounce.300ms="buscarProductos()"
                        placeholder="Añadir un producto..."
                        class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 focus:ring-1 focus:ring-sky-400 focus:border-sky-400 outline-none bg-gray-50">

                    <!-- Dropdown resultados -->
                    <div x-show="resultados.length > 0"
                         class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-100 rounded-xl shadow-2xl max-h-80 overflow-y-auto">
                        <template x-for="p in resultados" :key="p.producto_id">
                            <div @click="seleccionarProducto(p)"
                                 class="px-4 py-3 border-b border-gray-50 hover:bg-sky-50 cursor-pointer flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-white">
                                        <i class="fas fa-box text-gray-300 group-hover:text-sky-400 text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-800" x-text="p.nombre"></div>
                                        <div class="text-xs text-gray-400">
                                            <span class="text-sky-500" x-text="p.codigo"></span>
                                            <span x-show="p.codigo_barras"> · <span x-text="p.codigo_barras"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-gray-600" x-text="'$' + (parseFloat(p.costo)||0).toFixed(4)"></div>
                                    <div class="text-[10px] text-gray-400 uppercase tracking-wide">Costo</div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Tabla de productos -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="py-2.5 pr-4 text-left text-xs font-semibold text-gray-500">Producto</th>
                                <th class="py-2.5 px-3 text-center text-xs font-semibold text-gray-500 w-24">Cantidad</th>
                                <th class="py-2.5 px-3 text-center text-xs font-semibold text-gray-500 w-28">Costo Unit.</th>
                                <th class="py-2.5 px-3 text-center text-xs font-semibold text-gray-500 w-24">Margen %</th>
                                <th class="py-2.5 px-3 text-center text-xs font-semibold text-gray-500 w-24">P. Venta</th>
                                <th class="py-2.5 px-3 text-center text-xs font-semibold text-gray-500">Depósito</th>
                                <th class="py-2.5 pl-3 text-right text-xs font-semibold text-gray-500 w-24">Importe</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-3 pr-4">
                                        <div class="text-sm font-semibold text-gray-800" x-text="item.nombre"></div>
                                        <div class="text-xs text-gray-400" x-text="item.codigo"></div>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <input type="number" x-model.number="item.cantidad" @input="updateTotals()" min="1"
                                            class="w-20 text-center text-sm py-1 bg-gray-50 rounded border-gray-200 focus:border-sky-400 focus:ring-0">
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <input type="number" step="0.0001" x-model.number="item.costo" @input="updateTotals()"
                                            class="w-24 text-center text-sm py-1 bg-gray-50 rounded border-gray-200 focus:border-sky-400 focus:ring-0 font-semibold text-sky-600">
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <div class="relative inline-flex items-center">
                                            <input type="number" step="0.1" min="0" x-model.number="item.margen_pct" @input="updateTotals()"
                                                class="w-20 text-center text-sm py-1 bg-emerald-50 rounded border-emerald-200 focus:border-emerald-400 focus:ring-0 font-semibold text-emerald-700 pr-5">
                                            <span class="absolute right-2 text-xs text-emerald-500 pointer-events-none font-bold">%</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="text-sm font-semibold text-gray-700" x-text="'$' + precioVenta(item).toFixed(2)"></span>
                                        <div class="text-[10px] text-gray-400" x-show="item.margen_pct > 0" x-text="'(' + item.margen_pct + '%)'"></div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <select x-model="item.deposito_id"
                                            class="text-xs py-1 bg-gray-50 rounded border-gray-200 focus:border-sky-400 focus:ring-0">
                                            <?php foreach ($depositos as $d): ?>
                                            <option value="<?= $d['deposito_id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="py-3 pl-3 text-right text-sm font-semibold text-gray-700"
                                        x-text="'$' + (item.cantidad * item.costo).toFixed(2)"></td>
                                    <td class="py-3 text-center">
                                        <button @click="removeItem(index)" class="text-gray-300 hover:text-red-400 transition-colors text-sm">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="items.length === 0">
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-sm text-gray-400 italic">
                                        <i class="fas fa-shopping-basket text-gray-200 text-2xl block mb-2"></i>
                                        No hay productos en esta orden
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Notas -->
            <div x-show="tab === 'notas'" class="px-8 py-5">
                <textarea x-model="compra.notas" rows="5"
                    placeholder="Defina sus términos, condiciones u observaciones de la compra..."
                    class="w-full text-sm text-gray-700 bg-transparent border-0 resize-none outline-none placeholder-gray-300 focus:ring-0 p-0"></textarea>
            </div>

        </div><!-- /tabs -->

        <!-- Pie del documento: notas rápidas + totales -->
        <div class="px-8 py-5 border-t border-gray-100 flex items-end justify-between gap-8">
            <div class="flex-1 max-w-sm">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Términos y condiciones</p>
                <textarea x-model="compra.terminos" rows="2"
                    placeholder="Defina sus términos y condiciones..."
                    class="w-full text-sm text-gray-500 bg-transparent border-0 resize-none outline-none placeholder-gray-300 focus:ring-0 p-0"></textarea>
            </div>

            <div class="text-right space-y-1.5 shrink-0">
                <div class="flex items-center justify-between gap-16 text-sm text-gray-500">
                    <span>Importe base:</span>
                    <span class="font-semibold text-gray-700" x-text="'$' + totals.subtotal.toFixed(2)">$0.00</span>
                </div>
                <div class="flex items-center justify-between gap-16 text-sm text-gray-500">
                    <span>ITBMS (7%):</span>
                    <span class="font-semibold text-gray-700" x-text="'$' + totals.itbms.toFixed(2)">$0.00</span>
                </div>
                <div class="flex items-center justify-between gap-16 pt-2 border-t border-gray-100 text-sm">
                    <span class="font-semibold text-gray-700">Total:</span>
                    <span class="text-xl font-extrabold text-sky-600" x-text="'$' + totals.total.toFixed(2)">$0.00</span>
                </div>
            </div>
        </div>

    </div><!-- /documento -->

</div><!-- /x-data -->

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
            fecha_esperada: '',
            terminos: '',
            notas: ''
        },
        totals: { subtotal: 0, itbms: 0, total: 0 },

        async buscarProductos() {
            if (this.searchQuery.length < 2) { this.resultados = []; return; }
            try {
                const r = await fetch(`/api/productos/buscar?q=${encodeURIComponent(this.searchQuery)}`);
                const d = await r.json();
                this.resultados = d.productos || [];
            } catch (e) { console.error(e); }
        },

        precioVenta(item) {
            return (parseFloat(item.costo) || 0) * (1 + (parseFloat(item.margen_pct) || 0) / 100);
        },

        seleccionarProducto(p) {
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
                    margen_pct: 35,
                    deposito_id: <?= $depositos[0]['deposito_id'] ?? 'null' ?>,
                    itbms: 0
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

        updateTotals() {
            let sub = 0;
            this.items.forEach(i => { sub += i.cantidad * i.costo; });
            this.totals.subtotal = sub;
            this.totals.itbms    = sub * 0.07;
            this.totals.total    = sub + this.totals.itbms;
        },

        async saveCompra(recibir = true) {
            if (!this.compra.proveedor_id) return alert('Seleccione un proveedor');
            if (this.items.length === 0)   return alert('Agregue al menos un producto');

            const accion = recibir ? 'registrar esta entrada de inventario' : 'guardar como orden pendiente';
            if (!confirm('¿Seguro que desea ' + accion + '?')) return;

            try {
                const res = await fetch('/compras/guardar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        ...this.compra,
                        ...this.totals,
                        items: this.items,
                        guardar_como_pendiente: !recibir
                    })
                });
                const json = await res.json();
                if (json.success) {
                    window.location.href = recibir ? '/compras' : `/compras/${json.compra_id}`;
                } else {
                    alert('Error: ' + json.error);
                }
            } catch (e) {
                alert('Ocurrió un error al guardar la compra');
            }
        }
    }
}
</script>

<?php View::endSection('content'); ?>
