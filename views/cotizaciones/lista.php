<?php use App\Core\View; View::layout('app'); ?>
<?php
View::section('content');
$estado_actual       ??= '';
$cliente_actual      ??= '';
$fecha_inicio_actual ??= '';
$fecha_fin_actual    ??= '';

// JSON para el autocomplete de clientes
$clienteNombreActual = '';
$clientes_json = json_encode(array_map(function($c) use ($cliente_actual, &$clienteNombreActual) {
    $id     = is_object($c) ? $c->cliente_id : ($c['cliente_id'] ?? '');
    $nombre = is_object($c) ? $c->nombre     : ($c['nombre']     ?? '');
    if ((string)$id === (string)$cliente_actual) $clienteNombreActual = $nombre;
    return ['id' => (string)$id, 'nombre' => $nombre];
}, $clientes ?? []));
?>

<!-- Métricas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-indigo-500">
        <p class="text-xs text-gray-500 mb-1">Total Cotizaciones</p>
        <h3 class="text-xl font-bold text-gray-900">$<?= number_format((float)($total_cotizaciones ?? 0), 2) ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 mb-1">Cantidad</p>
        <h3 class="text-xl font-bold text-gray-900"><?= (int)($cantidad_cotizaciones ?? 0) ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
        <p class="text-xs text-gray-500 mb-1">Promedio</p>
        <h3 class="text-xl font-bold text-gray-900">$<?= number_format((float)($promedio_cotizacion ?? 0), 2) ?></h3>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-orange-500">
        <p class="text-xs text-gray-500 mb-1">Hoy</p>
        <h3 class="text-xl font-bold text-gray-900"><?= (int)($cotizaciones_hoy_count ?? 0) ?></h3>
    </div>
</div>

<!-- Filtros + acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="get" id="cotizaciones-search-form" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" value="<?= View::e($fecha_inicio_actual) ?>"
                   class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
        </div>
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="<?= View::e($fecha_fin_actual) ?>"
                   class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
        </div>
        <div class="flex-1 min-w-28">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Estado</label>
            <select name="estado" class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todos</option>
                <option value="pendiente" <?= $estado_actual == 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                <option value="aprobada" <?= $estado_actual == 'aprobada' ? 'selected' : '' ?>>Aprobadas</option>
                <option value="rechazada" <?= $estado_actual == 'rechazada' ? 'selected' : '' ?>>Rechazadas</option>
                <option value="convertida" <?= $estado_actual == 'convertida' ? 'selected' : '' ?>>Convertidas</option>
            </select>
        </div>
        <!-- Autocomplete de clientes -->
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Cliente</label>
            <div x-data="cotizClienteFilter()" @click.outside="open = false" class="relative">
                <input type="hidden" name="cliente" :value="selectedId">
                <div class="relative">
                    <i class="fas fa-user absolute left-0 top-2 text-gray-400 text-xs"></i>
                    <input type="text"
                        x-model="search"
                        @focus="open = true"
                        @input="open = true"
                        placeholder="Buscar cliente..."
                        autocomplete="off"
                        class="w-full pl-5 pr-5 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
                    <button type="button" x-show="selectedId" @click="clear()"
                        class="absolute right-0 top-1.5 text-gray-300 hover:text-gray-500 text-xs">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div x-show="open && filtered.length > 0"
                    class="absolute z-20 mt-1 w-56 bg-white rounded-lg border border-gray-100 shadow-lg overflow-hidden">
                    <div class="max-h-52 overflow-y-auto divide-y divide-gray-50">
                        <button type="button" @click="clear()"
                            class="w-full text-left px-3 py-2 text-xs text-gray-400 hover:bg-sky-50 flex items-center gap-2">
                            <i class="fas fa-users text-gray-300 text-xs"></i> Todos los clientes
                        </button>
                        <template x-for="c in filtered" :key="c.id">
                            <button type="button" @click="select(c)"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-sky-50 flex items-center gap-2 transition-colors"
                                :class="c.id === selectedId ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-gray-700'">
                                <i class="fas fa-user text-gray-300 text-xs shrink-0"></i>
                                <span x-text="c.nombre" class="truncate"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="/ventas/cotizaciones/nueva" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">
                <i class="fas fa-plus"></i> Nueva
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead>
            <tr>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Número</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vence</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach (($cotizaciones ?? []) as $cotizacion): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900"><?= View::e($cotizacion['numero']) ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= date('d/m/Y H:i', strtotime($cotizacion['fecha'])) ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">
                    <?php
                    $fecha_validez = strtotime($cotizacion['fecha_vencimiento'] ?? '');
                    $vencida = $fecha_validez && $fecha_validez < time() && $cotizacion['estado'] === 'pendiente';
                    ?>
                    <span class="<?= $vencida ? 'text-red-600 font-semibold' : 'text-gray-600' ?>">
                        <?= $fecha_validez ? date('d/m/Y', $fecha_validez) : '—' ?>
                        <?php if ($vencida): ?><i class="fas fa-exclamation-circle ml-1 text-xs"></i><?php endif; ?>
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?= View::e($cotizacion['cliente_nombre'] ?? 'Consumidor Final') ?></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">$<?= number_format((float)$cotizacion['total'], 2) ?></td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <?php
                        $badgeClasses = match($cotizacion['estado']) {
                            'aprobada', 'convertida' => 'bg-emerald-100 text-emerald-700',
                            'rechazada' => 'bg-red-100 text-red-600',
                            default     => 'bg-amber-100 text-amber-700',
                        };
                    ?>
                    <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full <?= $badgeClasses ?>">
                        <?= ucfirst(View::e($cotizacion['estado'])) ?>
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                    <div class="flex justify-end gap-2">
                        <a href="/ventas/cotizaciones/<?= $cotizacion['cotizacion_id'] ?>" class="text-blue-500 hover:text-blue-700" title="Ver"><i class="fas fa-eye"></i></a>
                        <button onclick="imprimirCotizacion(<?= $cotizacion['cotizacion_id'] ?>)" class="text-green-500 hover:text-green-700" title="Imprimir"><i class="fas fa-print"></i></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($cotizaciones)): ?>
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">
                    <i class="fas fa-file-invoice text-3xl mb-2 block text-gray-200"></i>
                    No se encontraron cotizaciones
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
        <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
    </div>
</div>

<?php View::endSection('content'); ?>

<?php View::section('extra_js'); ?>
<script>
    function cotizClienteFilter() {
        return {
            open: false,
            search: <?= json_encode($clienteNombreActual) ?>,
            selectedId: <?= json_encode((string)$cliente_actual) ?>,
            clientes: <?= $clientes_json ?>,
            get filtered() {
                if (!this.search) return this.clientes.slice(0, 15);
                var q = this.search.toLowerCase();
                return this.clientes.filter(function(c) {
                    return c.nombre.toLowerCase().indexOf(q) !== -1;
                }).slice(0, 15);
            },
            select: function(c) {
                this.search = c.nombre;
                this.selectedId = c.id;
                this.open = false;
                document.getElementById('cotizaciones-search-form').submit();
            },
            clear: function() {
                this.search = '';
                this.selectedId = '';
                this.open = false;
                document.getElementById('cotizaciones-search-form').submit();
            }
        };
    }

    function imprimirCotizacion(id) { window.open(`/ventas/cotizaciones/${id}/?print=true`, '_blank'); }
</script>
<?php View::endSection('extra_js'); ?>
