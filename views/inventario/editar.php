<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="max-w-6xl mx-auto">
    <form action="/inventario/<?= $producto->producto_id ?>/editar" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <?= View::csrf() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Código *</label><input type="text" name="codigo" value="<?= View::e($producto->codigo) ?>" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label><input type="text" name="nombre" value="<?= View::e($producto->nombre) ?>" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label><textarea name="descripcion" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500-500"><?= View::e($producto->descripcion) ?></textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label><select name="categoria_id" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="">Sin categoría</option><?php foreach (($categorias ?? []) as $cat):
                $cId  = is_array($cat) ? $cat['categoria_id'] : ($cat->categoria_id ?? '');
                $cNom = is_array($cat) ? $cat['nombre']       : ($cat->nombre ?? '');
            ?><option value="<?= $cId ?>" <?= (string)$producto->categoria_id === (string)$cId ? 'selected' : '' ?>><?= View::e($cNom) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Marca</label><select name="marca" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="">Sin marca</option><?php foreach (($marcas ?? []) as $mar): ?><option value="<?= View::e($mar['nombre']) ?>" <?= $producto->marca === $mar['nombre'] ? 'selected' : '' ?>><?= View::e($mar['nombre']) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras</label><input type="text" name="codigo_barras" value="<?= View::e($producto->codigo_barras) ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label><input type="number" name="stock_minimo" value="<?= $producto->stock_minimo ?>" min="0" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Referencia Proveedor</label><input type="text" name="supplier_part_no" value="<?= View::e($producto->supplier_part_no ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: REF-123"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label><select name="proveedor_id" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="">Ninguno</option><?php foreach (($proveedores ?? []) as $prov):
                $pId  = is_array($prov) ? $prov['proveedor_id'] : ($prov->proveedor_id ?? '');
                $pNom = is_array($prov) ? $prov['nombre']       : ($prov->nombre ?? '');
            ?><option value="<?= $pId ?>" <?= (string)$producto->proveedor_id === (string)$pId ? 'selected' : '' ?>><?= View::e($pNom) ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Costo ($) *</label><input type="number" name="costo" value="<?= $producto->costo ?>" step="0.01" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm font-bold text-gray-800 focus:ring-2 focus:ring-blue-500-500"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">ITBMS ($)</label><input type="number" name="itbms" value="<?= $producto->itbms ?>" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Estado</label><select name="estado" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="activo" <?= $producto->estado === 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= $producto->estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
        </div>

        <!-- Precios de Venta + Presentaciones — tabla unificada -->
        <?php
        $precios_map = [];
        foreach (($precios ?? []) as $p) {
            $tipo = is_array($p) ? ($p['tipo_precio'] ?? '') : ($p->tipo_precio ?? '');
            $val  = is_array($p) ? ($p['precio'] ?? 0)      : ($p->precio ?? 0);
            $precios_map[$tipo] = $val;
        }
        ?>
        <div class="border-t pt-5" x-data='{
            unidades: <?= json_encode(array_values($unidades ?? [])) ?>,
            agregarUnidad() {
                this.unidades.push({ nombre: "", factor_conversion: 1, precio_a: 0, precio_b: 0, codigo_barras: "" });
            },
            eliminarUnidad(index) {
                this.unidades.splice(index, 1);
            }
        }'>
            <input type="hidden" name="unidades_json" :value="JSON.stringify(unidades)">
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
                            <td class="px-4 py-2 w-32"><div class="relative"><span class="absolute left-2 top-2 text-gray-400 text-xs">$</span><input type="number" name="precio_a" value="<?= $precios_map['a'] ?? '0' ?>" step="0.01" min="0" class="w-full pl-5 pr-2 py-1.5 border border-gray-200 rounded-lg text-sm font-bold text-indigo-700 bg-white focus:ring-2 focus:ring-indigo-300"></div></td>
                            <td class="px-4 py-2 w-32"><div class="relative"><span class="absolute left-2 top-2 text-gray-400 text-xs">$</span><input type="number" name="precio_b" value="<?= $precios_map['b'] ?? '0' ?>" step="0.01" min="0" class="w-full pl-5 pr-2 py-1.5 border border-gray-200 rounded-lg text-sm font-bold text-emerald-700 bg-white focus:ring-2 focus:ring-emerald-300"></div></td>
                            <td class="px-4 py-3 text-center text-gray-300 text-xs">—</td>
                            <td></td>
                        </tr>
                        <!-- Filas dinámicas de presentaciones -->
                        <template x-for="(u, index) in unidades" :key="index">
                            <tr class="bg-white group hover:bg-sky-50/30 transition-all">
                                <td class="px-4 py-2"><input type="text" x-model="u.nombre" placeholder="Ej: Caja de 24" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-semibold focus:ring-2 focus:ring-sky-300 focus:border-sky-400 bg-white"></td>
                                <td class="px-4 py-2"><input type="number" x-model.number="u.factor_conversion" step="0.01" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-center font-black text-gray-700 focus:ring-2 focus:ring-sky-300 bg-white"></td>
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
        <!-- Inventario por Depósito -->
        <div class="border-t pt-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Resumen de Inventario</h3>
                <a href="/inventario/<?= $producto->producto_id ?>" class="text-[10px] font-black text-sky-600 uppercase tracking-widest hover:text-sky-800 transition-colors">
                    Gestionar Movimientos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php 
                // Esto requeriría que el controlador pase los inventarios, 
                // pero podemos hacer una consulta rápida aquí o simplemente mostrar que se puede gestionar aparte.
                // Como ya tenemos el producto_id, buscaremos los inventarios actuales.
                $inventariosActuales = \App\Core\Database::query(
                    "SELECT i.*, d.nombre as deposito_nombre 
                     FROM inventario i 
                     JOIN depositos d ON i.deposito_id = d.deposito_id 
                     WHERE i.producto_id = ?",
                    [$producto->producto_id]
                )->fetchAll();
                ?>
                
                <?php foreach ($inventariosActuales as $inv): ?>
                <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?= View::e($inv['deposito_nombre']) ?></p>
                        <p class="text-lg font-black text-slate-800"><?= number_format((float)$inv['existencia'], 0) ?> <span class="text-xs">UNDS</span></p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-white border border-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if(empty($inventariosActuales)): ?>
                <div class="md:col-span-2 bg-amber-50 border border-amber-100 p-4 rounded-xl text-center">
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-widest">Sin registros de inventario para este producto</p>
                </div>
                <?php endif; ?>
            </div>
        </div>



        <div class="border-t pt-4 flex flex-col md:flex-row md:items-start gap-6">
            <div class="flex-1">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="maneja_lotes" value="1" <?= $producto->maneja_lotes ? 'checked' : '' ?> class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Maneja Lotes por Fecha de Vencimiento</span>
                </label>
                <p class="text-xs text-gray-400 mt-1 ml-7">Al activar esta opción, el sistema pedirá número de lote y fecha de vencimiento al registrar compras y usará FEFO al descontar en ventas.</p>
            </div>
            <div class="shrink-0">
                <?php if (!empty($producto->imagen_principal)): ?>
                    <p class="text-xs text-gray-500 mb-2 font-medium">Imagen actual:</p>
                    <img src="/assets/uploads/<?= View::e($producto->imagen_principal) ?>" class="h-16 rounded-lg mb-2">
                <?php endif; ?>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nueva imagen</label>
                <input type="file" name="imagen_principal" accept="image/*" class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="/inventario/<?= $producto->producto_id ?>" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm"><i class="fas fa-save mr-1"></i> Actualizar</button>
        </div>
    </form>
</div>

<?php View::endSection('content'); ?>
