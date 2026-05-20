<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Filtros + acciones -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <form method="get" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-56">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Buscar</label>
            <div class="relative">
                <i class="fas fa-search absolute left-0 top-2 text-gray-400 text-xs"></i>
                <input type="text" name="buscar" value="<?= View::e($_GET['buscar'] ?? '') ?>"
                       placeholder="Código, nombre o RUC..."
                       class="w-full pl-5 py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none">
            </div>
        </div>
        <div class="min-w-32">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Estado</label>
            <select name="estado" class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todos</option>
                <option value="activo" <?= ($_GET['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activos</option>
                <option value="inactivo" <?= ($_GET['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
            </select>
        </div>
        <div class="min-w-32">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tipo</label>
            <select name="tipo" class="w-full py-1.5 px-0 text-sm bg-transparent border-0 border-b border-gray-200 focus:border-sky-400 focus:ring-0 outline-none appearance-none">
                <option value="">Todos</option>
                <option value="natural" <?= ($_GET['tipo'] ?? '') === 'natural' ? 'selected' : '' ?>>Natural</option>
                <option value="juridico" <?= ($_GET['tipo'] ?? '') === 'juridico' ? 'selected' : '' ?>>Jurídico</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <?php if (!empty($_GET['buscar']) || !empty($_GET['estado']) || !empty($_GET['tipo'])): ?>
            <a href="/clientes" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-200 text-gray-500 rounded-lg text-sm hover:bg-gray-50 transition">
                <i class="fas fa-times"></i>
            </a>
            <?php endif; ?>
            <a href="/clientes/nuevo" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">
                <i class="fas fa-user-plus"></i> Nuevo
            </a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Código</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contacto</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Saldo</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach (($clientes ?? []) as $cliente): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-500"><?= View::e($cliente->codigo) ?></td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-sky-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900"><?= View::e($cliente->nombre) ?></div>
                            <div class="text-xs text-gray-400"><?= View::e($cliente->tipo_cliente ?? ucfirst($cliente->tipo ?? 'N/A')) ?></div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                    <div><?= View::e($cliente->telefono ?: '—') ?></div>
                    <div class="text-xs text-gray-400"><?= View::e($cliente->email ?: '—') ?></div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full <?= $cliente->estado === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' ?>">
                        <?= ucfirst($cliente->estado) ?>
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold <?= $cliente->saldo > 0 ? 'text-red-600' : 'text-gray-700' ?>">
                    $<?= number_format((float)$cliente->saldo, 2) ?>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                    <div class="flex justify-end gap-2">
                        <a href="/clientes/<?= $cliente->cliente_id ?>" class="text-blue-500 hover:text-blue-700" title="Ver"><i class="fas fa-eye"></i></a>
                        <a href="/ventas/nueva?cliente=<?= $cliente->cliente_id ?>" class="text-emerald-500 hover:text-emerald-700" title="Nueva Venta"><i class="fas fa-shopping-cart"></i></a>
                        <a href="/clientes/<?= $cliente->cliente_id ?>/editar" class="text-gray-400 hover:text-blue-600" title="Editar"><i class="fas fa-edit"></i></a>
                        <form method="post" action="/clientes/<?= $cliente->cliente_id ?>/eliminar" class="inline"
                              onsubmit="return confirm('¿Eliminar a <?= View::e($cliente->nombre) ?>?')">
                            <?= View::csrf() ?>
                            <button type="submit" class="text-gray-300 hover:text-red-600" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($clientes)): ?>
            <tr>
                <td colspan="6" class="px-4 py-10 text-center">
                    <i class="fas fa-users text-3xl mb-2 block text-gray-200"></i>
                    <p class="text-sm text-gray-400">No se encontraron clientes.</p>
                    <a href="/clientes/nuevo" class="text-sky-500 hover:underline mt-2 inline-block text-sm font-medium">Crear nuevo</a>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if (!empty($clientes)): ?>
    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
        <?php View::include('partials.pagination', ['pagination' => $pagination ?? []]); ?>
    </div>
    <?php endif; ?>
</div>

<?php View::endSection('content'); ?>
