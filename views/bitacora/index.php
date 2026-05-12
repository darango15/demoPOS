<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Bitácora de Auditoría</h2>
            <p class="text-slate-500 text-sm">Registro de acciones del sistema — <?= number_format($total) ?> eventos</p>
        </div>
    </div>

    <form method="GET" action="/bitacora" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Módulo</label>
                <select name="modulo" class="w-full rounded-lg border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">Todos</option>
                    <?php foreach ($modulos as $mod): ?>
                        <option value="<?= View::e($mod) ?>" <?= $filtros['modulo'] === $mod ? 'selected' : '' ?>>
                            <?= View::e(ucfirst($mod)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Usuario</label>
                <input type="text" name="usuario" value="<?= View::e($filtros['usuario']) ?>"
                       placeholder="Nombre de usuario"
                       class="w-full rounded-lg border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
                <input type="date" name="desde" value="<?= View::e($filtros['desde']) ?>"
                       class="w-full rounded-lg border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
                <input type="date" name="hasta" value="<?= View::e($filtros['hasta']) ?>"
                       class="w-full rounded-lg border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
        </div>
        <div class="flex gap-2 mt-3">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Filtrar
            </button>
            <a href="/bitacora" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200">
                Limpiar
            </a>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Módulo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Acción</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                                <i class="fas fa-history text-3xl mb-2 block opacity-30"></i>
                                No hay eventos registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <?php
                                $moduloColors = [
                                    'auth'        => 'bg-blue-100 text-blue-700',
                                    'ventas'      => 'bg-green-100 text-green-700',
                                    'compras'     => 'bg-orange-100 text-orange-700',
                                    'productos'   => 'bg-purple-100 text-purple-700',
                                    'inventario'  => 'bg-teal-100 text-teal-700',
                                    'usuarios'    => 'bg-rose-100 text-rose-700',
                                    'conteos'     => 'bg-amber-100 text-amber-700',
                                ];
                                $color = $moduloColors[$log['modulo']] ?? 'bg-slate-100 text-slate-600';
                            ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap">
                                    <?= date('d/m/Y H:i', strtotime($log['fecha_registro'])) ?>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    <?= View::e($log['username'] ?? '—') ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $color ?>">
                                        <?= View::e(ucfirst($log['modulo'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">
                                    <?= View::e($log['accion']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-700 max-w-xs truncate" title="<?= View::e($log['descripcion']) ?>">
                                    <?= View::e($log['descripcion']) ?>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-400">
                                    <?= View::e($log['ip'] ?? '') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_paginas > 1): ?>
            <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between text-sm text-slate-600">
                <span>Página <?= $pagina ?> de <?= $total_paginas ?> — <?= number_format($total) ?> eventos</span>
                <div class="flex gap-1">
                    <?php if ($pagina > 1): ?>
                        <a href="?<?= http_build_query(array_merge($filtros, ['page' => $pagina - 1])) ?>"
                           class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    <?php for ($p = max(1, $pagina - 2); $p <= min($total_paginas, $pagina + 2); $p++): ?>
                        <a href="?<?= http_build_query(array_merge($filtros, ['page' => $p])) ?>"
                           class="px-3 py-1 rounded-lg <?= $p === $pagina ? 'bg-indigo-600 text-white' : 'bg-slate-100 hover:bg-slate-200' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($pagina < $total_paginas): ?>
                        <a href="?<?= http_build_query(array_merge($filtros, ['page' => $pagina + 1])) ?>"
                           class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection('content'); ?>
