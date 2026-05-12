<?php use App\Core\View; View::layout('app'); ?>
<?php View::section('content'); ?>

<!-- Alertas de Lotes -->
<?php if (($lotesVencidos ?? 0) > 0 || ($lotesVenciendo ?? 0) > 0): ?>
<div class="mb-6 space-y-3">
    <?php if (($lotesVencidos ?? 0) > 0): ?>
    <div class="flex items-center gap-4 bg-red-50 border border-red-200 rounded-xl px-5 py-4">
        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
            <i class="fas fa-skull-crossbones text-red-600"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-red-800">
                <?= $lotesVencidos ?> lote<?= $lotesVencidos > 1 ? 's' : '' ?> vencido<?= $lotesVencidos > 1 ? 's' : '' ?> aún marcado<?= $lotesVencidos > 1 ? 's' : '' ?> como activo<?= $lotesVencidos > 1 ? 's' : '' ?>.
            </p>
            <p class="text-xs text-red-600 mt-0.5">Estos lotes deben retirarse del inventario disponible.</p>
        </div>
        <a href="/inventario/lotes" class="shrink-0 text-xs font-bold text-red-700 hover:text-red-900 underline underline-offset-2">
            Ver Lotes <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    <?php endif; ?>
    <?php if (($lotesVenciendo ?? 0) > 0): ?>
    <div class="flex items-center gap-4 bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">
        <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
            <i class="fas fa-clock text-amber-600"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-amber-800">
                <?= $lotesVenciendo ?> lote<?= $lotesVenciendo > 1 ? 's' : '' ?> vence<?= $lotesVenciendo > 1 ? 'n' : '' ?> en los próximos 30 días.
            </p>
            <p class="text-xs text-amber-600 mt-0.5">Revisa y prioriza la venta de estos productos.</p>
        </div>
        <a href="/inventario/lotes" class="shrink-0 text-xs font-bold text-amber-700 hover:text-amber-900 underline underline-offset-2">
            Ver Lotes <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Insights Inteligentes -->
<?php if ($ai_enabled && !empty($insights)): ?>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-500">
            <i class="fas fa-brain text-sm"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Business Intelligence (AI)</h3>
        <span class="px-2 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-bold rounded-full uppercase">Premium</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($insights as $insight): ?>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex gap-5 transition-all hover:shadow-md border-l-4 <?= $insight['type'] === 'warning' ? 'border-l-amber-400' : ($insight['type'] === 'success' ? 'border-l-emerald-400' : 'border-l-blue-400') ?>">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 <?= $insight['type'] === 'warning' ? 'bg-amber-50 text-amber-500' : ($insight['type'] === 'success' ? 'bg-emerald-50 text-emerald-500' : 'bg-blue-50 text-blue-500') ?>">
                <i class="<?= $insight['icon'] ?> text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mb-1"><?= $insight['title'] ?></p>
                <p class="text-sm font-bold text-slate-700 leading-tight mb-2"><?= $insight['message'] ?></p>
                <p class="text-xs text-slate-400 italic mb-3"><?= $insight['detail'] ?></p>
                <div class="flex justify-end">
                    <a href="<?= $insight['action_url'] ?>" class="text-[10px] font-bold text-blue-500 hover:underline uppercase flex items-center gap-1">
                        <?= $insight['action_label'] ?> <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Métricas principales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Ventas del día -->
    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-green-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-600">Ventas Hoy</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">$<?= number_format((float)($ventasHoy['total'] ?? 0), 2) ?></h3>
                <p class="text-xs text-gray-500 mt-2"><?= $ventasHoy['cantidad'] ?? 0 ?> transacciones</p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Productos -->
    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Productos</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= number_format((int)($totalProductos ?? 0)) ?></h3>
                <p class="text-xs text-gray-500 mt-2">En inventario</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-box text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Stock Bajo -->
    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-yellow-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-600">Stock Bajo</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= $stockBajo ?? 0 ?></h3>
                <p class="text-xs text-yellow-600 mt-2">Necesitan atención</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-lg">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Sin Stock -->
    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-red-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-600">Sin Stock</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1"><?= $agotados ?? 0 ?></h3>
                <p class="text-xs text-red-600 mt-2">Productos agotados</p>
            </div>
            <div class="bg-red-100 p-3 rounded-lg">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos y estadísticas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Gráfico de ventas -->
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas de la Semana</h3>
        <div class="h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Stock Crítico -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-rose-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Stock Crítico</h3>
            <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[10px] font-bold rounded-full uppercase tracking-wider">Acción Requerida</span>
        </div>
        <div class="space-y-3">
            <?php foreach (($listaStockBajo ?? []) as $i => $item): ?>
            <div class="flex items-center justify-between p-3 bg-rose-50/50 rounded-xl border border-rose-50 border-dashed">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-triangle text-rose-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-700"><?= htmlspecialchars($item['nombre']) ?></p>
                        <p class="text-[10px] text-slate-500"><?= htmlspecialchars($item['deposito_nombre']) ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm font-extrabold text-rose-600"><?= $item['existencia'] ?></span>
                    <p class="text-[9px] text-slate-400 uppercase font-bold">Mín: <?= $item['minimo'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($listaStockBajo)): ?>
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-check text-emerald-500"></i>
                </div>
                <p class="text-slate-400 text-sm italic">Todo el stock está saludable</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Actividad reciente -->
<div class="bg-white rounded-xl p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividad Reciente</h3>
    <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Factura</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach (($ultimasVentas ?? []) as $venta): ?>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <a href="/ventas/venta/<?= $venta['venta_id'] ?>" class="text-sm font-medium text-sky-600 hover:underline">
                            <?= View::e($venta['numero_factura']) ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?= View::e($venta['cliente_nombre'] ?? 'Consumidor Final') ?></td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">$<?= number_format((float)$venta['total'], 2) ?></td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <?php
                        $badgeColor = match($venta['estado']) {
                            'pagada' => 'bg-green-100 text-green-800',
                            'anulada' => 'bg-red-100 text-red-800',
                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badgeColor ?>"><?= ucfirst($venta['estado']) ?></span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($ultimasVentas)): ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No hay ventas recientes</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- AI Assistant Floating Button -->
<button id="btn-ai-assistant" class="fixed bottom-6 right-6 w-14 h-14 bg-blue-500 text-white rounded-full shadow-lg hover:scale-110 transition-all flex items-center justify-center z-50 group">
    <i class="fas fa-robot text-xl group-hover:hidden"></i>
    <i class="fas fa-comment-dots text-xl hidden group-hover:block"></i>
    <div class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 border-2 border-white rounded-full animate-pulse"></div>
</button>

<!-- AI Assistant Sidebar -->
<div id="ai-sidebar" class="fixed top-0 right-0 w-80 h-full bg-white shadow-2xl translate-x-full transition-transform duration-300 z-[60] flex flex-col border-l border-slate-100">
    <div class="p-6 bg-blue-500 text-white flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-brain"></i>
            </div>
            <span class="font-bold">Asistente POS</span>
        </div>
        <button id="close-ai-sidebar" class="hover:rotate-90 transition-all">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="flex-1 p-6 overflow-y-auto space-y-6 scroll-custom">
        <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
            <p class="text-xs text-blue-700 leading-relaxed font-medium">
                "¡Hola! Soy tu asistente de datos. He analizado hoy y he notado que las ventas de la tarde subieron un 15% respecto a ayer."
            </p>
        </div>
        
        <div class="space-y-4">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sugerencias Rápidas</h4>
            <div class="grid grid-cols-1 gap-2">
                <button class="text-left p-3 text-xs bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-100 transition-all flex items-center gap-2">
                    <i class="fas fa-search text-blue-500"></i> ¿Qué producto subió de precio?
                </button>
                <button class="text-left p-3 text-xs bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-100 transition-all flex items-center gap-2">
                    <i class="fas fa-chart-pie text-blue-500"></i> Resumen de margen semanal
                </button>
                <button class="text-left p-3 text-xs bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-100 transition-all flex items-center gap-2">
                    <i class="fas fa-user-tag text-blue-500"></i> ¿Quién es mi mejor cliente hoy?
                </button>
            </div>
        </div>
        
        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
            <div class="flex items-center gap-2 mb-2 text-emerald-700">
                <i class="fas fa-bolt text-xs"></i>
                <span class="text-[10px] font-black uppercase">Dato Curioso</span>
            </div>
            <p class="text-[10px] text-emerald-800 leading-normal">
                El 40% de tus ventas de hoy se han realizado con <strong>YAPPY</strong>. Considera incentivar pagos digitales.
            </p>
        </div>
    </div>
    <div class="p-4 border-t border-slate-100">
        <div class="relative">
            <input type="text" placeholder="Pregúntame algo..." class="w-full pl-4 pr-10 py-2 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            <button class="absolute right-2 top-1.5 text-blue-500 hover:scale-110 transition-all">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnOpen = document.getElementById('btn-ai-assistant');
        const btnClose = document.getElementById('close-ai-sidebar');
        const sidebar = document.getElementById('ai-sidebar');

        btnOpen.addEventListener('click', () => {
            sidebar.classList.remove('translate-x-full');
        });

        btnClose.addEventListener('click', () => {
            sidebar.classList.add('translate-x-full');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !btnOpen.contains(e.target) && !sidebar.classList.contains('translate-x-full')) {
                sidebar.classList.add('translate-x-full');
            }
        });

        // Gráfico de ventas semanal
        const canvas = document.getElementById('salesChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const dataRaw = <?= json_encode($ventasSemanales ?? []) ?>;
            const labels = dataRaw.map(v => v.label);
            const data = dataRaw.map(v => v.total);

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(14, 165, 233, 0.4)');
            gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: data,
                        borderColor: '#0EA5E9',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0EA5E9',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#0EA5E9',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { size: 12, weight: 'bold', family: 'Inter' },
                            bodyFont: { size: 12, family: 'Inter' },
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, family: 'Inter' }, color: '#94A3B8' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9', drawBorder: false },
                            ticks: {
                                font: { size: 11, family: 'Inter' },
                                color: '#94A3B8',
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    animations: {
                        tension: {
                            duration: 1000,
                            easing: 'linear',
                            from: 1,
                            to: 0.4,
                            loop: false
                        }
                    }
                }
            });
        }
    });
</script>

<?php View::endSection('content'); ?>
