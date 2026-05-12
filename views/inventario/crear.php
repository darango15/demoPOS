<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="max-w-6xl mx-auto">
    <form action="/inventario/nuevo" method="POST" enctype="multipart/form-data"
          x-data='{
              unidades: [],
              agregarUnidad() {
                  this.unidades.push({ nombre: "", factor_conversion: 1, precio_a: 0, precio_b: 0, codigo_barras: "" });
              },
              eliminarUnidad(index) {
                  this.unidades.splice(index, 1);
              }
          }'
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <?= View::csrf() ?>
        <input type="hidden" name="unidades_json" :value="JSON.stringify(unidades)">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código *</label>
                <input type="text" name="codigo" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="nombre" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                <select name="categoria_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Sin categoría</option>
                    <?php foreach (($categorias ?? []) as $cat): ?>
                        <?php $cId = is_array($cat) ? ($cat['categoria_id'] ?? '') : ($cat->categoria_id ?? ''); ?>
                        <?php $cNom = is_array($cat) ? ($cat['nombre'] ?? '') : ($cat->nombre ?? ''); ?>
                        <option value="<?= $cId ?>"><?= View::e($cNom) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                <select name="marca" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Sin marca</option>
                    <?php foreach (($marcas ?? []) as $mar): ?>
                        <option value="<?= View::e($mar['nombre']) ?>"><?= View::e($mar['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras</label>
                <input type="text" name="codigo_barras" placeholder="Auto-generado si vacío" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                <input type="number" name="stock_minimo" value="0" min="0" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Referencia Proveedor</label>
                <input type="text" name="supplier_part_no" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="Ej: REF-123">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                <select name="proveedor_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Ninguno</option>
                    <?php foreach (($proveedores ?? []) as $prov): ?>
                        <?php $pId = is_array($prov) ? ($prov['proveedor_id'] ?? '') : ($prov->proveedor_id ?? ''); ?>
                        <?php $pNom = is_array($prov) ? ($prov['nombre'] ?? '') : ($prov->nombre ?? ''); ?>
                        <option value="<?= $pId ?>"><?= View::e($pNom) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Costo ($) *</label>
                <input type="number" name="costo_inicial" step="0.01" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm font-bold text-gray-800 focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ITBMS ($)</label>
                <input type="number" name="itbms" value="0.00" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            </div>
        </div>

        <!-- Existencias iniciales -->
        <div class="border-t pt-5">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Existencias iniciales por Depósito</h3>
            <div class="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-wider">Depósito</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-wider">Stock Inicial</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-wider w-32">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach (($depositos ?? []) as $dep): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-700"><?= View::e($dep['nombre']) ?></span>
                                <input type="hidden" name="depositos_ids[]" value="<?= $dep['deposito_id'] ?>">
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative w-32">
                                    <input type="number" name="stocks[]" value="0" min="0"
                                           class="w-full pl-3 pr-10 py-2 border border-gray-200 rounded-lg text-sm font-black text-slate-800 focus:ring-2 focus:ring-sky-500 bg-white">
                                    <span class="absolute right-3 top-2.5 text-[9px] font-black text-slate-400 uppercase">UNDS</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[9px] font-black rounded-full uppercase">Activo</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($depositos)): ?>
                        <tr><td colspan="3" class="px-6 py-4 text-center text-xs text-gray-400 italic">No hay depósitos activos disponibles</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">Nota: Se generará un registro de inventario para cada depósito con stock mayor a cero.</p>
        </div>

        <!-- Estructura de precios + presentaciones (tabla unificada) -->
        <div class="border-t pt-5">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Estructura de Precios por Presentación</h3>
                <button type="button" @click="agregarUnidad()" class="px-3 py-1.5 bg-sky-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-sky-600 transition-all shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Agregar Presentación
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-100 rounded-xl overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-4 py-3 text-left">Presentación</th>
                            <th class="px-4 py-3 text-center w-24" title="Cuántas unidades base contiene esta presentación">Conv.</th>
                            <th class="px-4 py-3 text-center text-indigo-500">Precio A</th>
                            <th class="px-4 py-3 text-center text-emerald-500">Precio B</th>
                            <th class="px-4 py-3 text-center">Cód. Barras</th>
                            <th class="w-8"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Fila: Unidad base (siempre visible) -->
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-sm">Unidad base</td>
                            <td class="px-4 py-3 text-center text-gray-400 text-xs">1</td>
                            <td class="px-4 py-2 w-32"><div class="relative"><span class="absolute left-2 top-2 text-gray-400 text-xs">$</span><input type="number" name="precio_a" value="0" step="0.01" min="0" class="w-full pl-5 pr-2 py-1.5 border border-gray-200 rounded-lg text-sm font-bold text-indigo-700 bg-white focus:ring-2 focus:ring-indigo-300"></div></td>
                            <td class="px-4 py-2 w-32"><div class="relative"><span class="absolute left-2 top-2 text-gray-400 text-xs">$</span><input type="number" name="precio_b" value="0" step="0.01" min="0" class="w-full pl-5 pr-2 py-1.5 border border-gray-200 rounded-lg text-sm font-bold text-emerald-700 bg-white focus:ring-2 focus:ring-emerald-300"></div></td>
                            <td class="px-4 py-3 text-center text-gray-300 text-xs">—</td>
                            <td></td>
                        </tr>
                        <!-- Filas dinámicas de presentaciones -->
                        <template x-for="(u, index) in unidades" :key="index">
                            <tr class="bg-white group hover:bg-sky-50/30 transition-all">
                                <td class="px-4 py-2"><input type="text" x-model="u.nombre" placeholder="Ej: Caja de 24" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-semibold focus:ring-2 focus:ring-sky-300 focus:border-sky-400 bg-white"></td>
                                <td class="px-4 py-2"><input type="number" x-model.number="u.factor_conversion" step="1" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-center font-black text-gray-700 focus:ring-2 focus:ring-sky-300 bg-white"></td>
                                <td class="px-4 py-2"><div class="relative"><span class="absolute left-2 top-2 text-gray-400 text-xs">$</span><input type="number" x-model.number="u.precio_a" step="0.01" min="0" class="w-full pl-5 pr-2 py-1.5 border border-gray-200 rounded-lg text-sm font-bold text-indigo-700 bg-white focus:ring-2 focus:ring-indigo-300"></div></td>
                                <td class="px-4 py-2"><div class="relative"><span class="absolute left-2 top-2 text-gray-400 text-xs">$</span><input type="number" x-model.number="u.precio_b" step="0.01" min="0" class="w-full pl-5 pr-2 py-1.5 border border-gray-200 rounded-lg text-sm font-bold text-emerald-700 bg-white focus:ring-2 focus:ring-emerald-300"></div></td>
                                <td class="px-4 py-2"><input type="text" x-model="u.codigo_barras" placeholder="Opcional" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-500 focus:ring-2 focus:ring-sky-300 bg-white"></td>
                                <td class="pr-2 py-2 text-center"><button type="button" @click="eliminarUnidad(index)" class="w-7 h-7 bg-red-100 text-red-500 rounded-full hover:bg-red-500 hover:text-white transition-all flex items-center justify-center text-xs"><i class="fas fa-times"></i></button></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lotes e imagen -->
        <div class="border-t pt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="maneja_lotes" value="1" class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <span class="text-sm font-medium text-gray-700">Maneja Lotes por Fecha de Vencimiento</span>
                </label>
                <p class="text-xs text-gray-400 mt-1 ml-7">Al activar, el sistema pedirá lote y fecha de vencimiento al registrar compras y usará FEFO al descontar en ventas.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del Producto</label>
                <input type="file" name="imagen_principal" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-sky-500 file:bg-opacity-10 file:text-sky-600 file:font-medium hover:file:bg-opacity-20">
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t pt-4">
            <a href="/inventario" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-sky-500 text-white rounded-lg text-sm font-medium hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-save mr-1"></i> Guardar Producto
            </button>
        </div>
    </form>
</div>

<?php View::endSection('content'); ?>
