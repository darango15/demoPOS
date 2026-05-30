<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>POS | Sistema POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#3B82F6",
                        secondary: "#8B5CF6",
                        dark: "#1F2937",
                        light: "#F9FAFB",
                        success: "#10B981",
                        danger: "#EF4444",
                        warning: "#F59E0B",
                        info: "#06B6D4",
                        pos: "#0EA5E9",
                    },
                },
            },
        };
    </script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

        body {
            font-family: "Inter", sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }

        .receipt-font {
            font-family: "Courier New", monospace;
        }

        .btn-cotizar {
            background-color: #2563eb;
        }

        .btn-pagar {
            background-color: #16a34a;
        }

        .btn-cancelar {
            background-color: #dc2626;
        }

        .btn-imprimir {
            background-color: #f59e0b;
        }

        /* Scroll personalizado */
        .scroll-custom::-webkit-scrollbar {
            width: 6px;
        }

        .scroll-custom::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .scroll-custom::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .action-btn {
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: scale(1.05);
        }

        /* Select2 Tailwind styling */
        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            height: 38px;
            padding-top: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #0EA5E9;
            box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
        }
    </style>
    <style>
        #toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.75rem; }
        .toast { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; border-radius: 0.75rem; box-shadow: 0 8px 24px rgba(0,0,0,0.18); font-size: 1rem; font-weight: 600; min-width: 320px; max-width: 460px; animation: toastIn 0.3s ease; }
        .toast i { font-size: 1.4rem; flex-shrink: 0; }
        .toast.success { background: #dcfce7; color: #14532d; border-left: 6px solid #16a34a; }
        .toast.error   { background: #fee2e2; color: #7f1d1d; border-left: 6px solid #dc2626; }
        .toast.info    { background: #dbeafe; color: #1e3a8a; border-left: 6px solid #2563eb; }
        @keyframes toastIn { from { opacity:0; transform:translateX(2rem); } to { opacity:1; transform:translateX(0); } }
    </style>
</head>

<body class="min-h-screen bg-gray-50 flex flex-col">
    <div id="toast-container"></div>
    <!-- Header fijo premium -->
    <header class="bg-white shadow-md border-b border-gray-200 sticky top-0 z-50">
        <div class="px-4 py-2">
            <div class="flex justify-between items-center">
                <!-- Logo y Empresa -->
                <div class="flex items-center space-x-4">
                    <div class="bg-pos p-2 rounded-xl shadow-lg shadow-pos/20">
                        <i class="fas fa-cash-register text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-black text-slate-800 uppercase tracking-tight leading-none"><?= View::e($empresa_actual['nombre_comercial'] ?? $empresa_actual['razon_social'] ?? 'Mi Empresa') ?></h1>
                        <p class="text-[10px] text-pos font-bold uppercase tracking-widest mt-0.5"><i class="fas fa-map-marker-alt mr-1"></i>Sede Principal</p>
                    </div>
                </div>

                <!-- Centro: Reloj y Accesos Rápidos -->
                <div class="hidden lg:flex items-center space-x-8">
                    <div class="text-center">
                        <p id="clock-date" class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter"></p>
                        <p id="clock-time" class="text-lg font-black text-slate-700 leading-none tabular-nums"></p>
                    </div>
                    <div class="h-8 w-[1px] bg-gray-200"></div>
                    <nav class="flex items-center space-x-2">
                        <!-- Panel de cotizaciones activas -->
                        <div class="relative">
                            <button id="btn-cotizaciones"
                                class="flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all group">
                                <i class="fas fa-file-invoice-dollar mr-2 text-slate-400 group-hover:text-amber-500"></i>
                                Cotizaciones
                                <span id="badge-cotizaciones"
                                    class="ml-1.5 bg-amber-400 text-white text-[10px] font-black rounded-full px-1.5 py-0.5 leading-none hidden">0</span>
                            </button>

                            <!-- Dropdown panel -->
                            <div id="panel-cotizaciones"
                                class="hidden absolute top-full mt-2 left-0 w-[420px] bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">

                                <!-- Cabecera -->
                                <div class="px-4 py-3 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-file-invoice-dollar text-amber-500 text-sm"></i>
                                        <span class="text-sm font-bold text-amber-800">Cotizaciones Activas</span>
                                    </div>
                                    <a href="/ventas/cotizaciones" target="_blank"
                                        class="text-[10px] font-semibold text-amber-600 hover:text-amber-800 hover:underline">
                                        Ver todas →
                                    </a>
                                </div>

                                <!-- Buscador -->
                                <div class="px-3 py-2 border-b border-gray-100">
                                    <div class="relative">
                                        <i class="fas fa-search absolute left-2.5 top-2 text-gray-300 text-xs"></i>
                                        <input type="text" id="buscar-cotizacion"
                                            placeholder="Número o nombre de cliente..."
                                            autocomplete="off"
                                            class="w-full pl-7 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-200 bg-gray-50">
                                    </div>
                                </div>

                                <!-- Lista -->
                                <div id="lista-cotizaciones" class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                    <div class="px-4 py-6 text-center text-xs text-gray-400">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Cargando...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="/configuracion" class="flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all group" title="Configurar descuentos POS">
                            <i class="fas fa-cog mr-2 text-slate-400 group-hover:text-amber-500"></i>Configuración
                        </a>
                    </nav>
                </div>

                <!-- Usuario y Salida -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center bg-slate-50 border border-slate-100 p-1 rounded-full pr-4 group hover:bg-white hover:shadow-sm transition-all cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-pos flex items-center justify-center text-white font-black text-xs shadow-md shadow-pos/20 ring-2 ring-white">
                            <?= strtoupper(substr($auth['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="ml-2">
                            <p class="text-[11px] font-black text-slate-800 leading-none"><?= View::e($auth['name'] ?? 'Usuario') ?></p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5"><?= ($auth['isSuperuser'] ?? false) ? 'Administrador' : 'Vendedor' ?></p>
                        </div>
                    </div>
                    
                    <a href="/logout" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all border border-transparent hover:border-red-100" title="Cerrar Sesión">
                        <i class="fas fa-power-off"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal - Ocupa todo el espacio disponible -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Barra de búsqueda y controles -->
        <div class="bg-white border-b border-gray-200 p-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <!-- Campo Código/Nombre -->
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código/Nombre</label>
                    <div class="relative">
                        <input type="text" id="txcodigo" placeholder="Buscar producto por código o nombre..."
                            class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pos focus:border-transparent"
                            autofocus />
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    </div>
                    <input type="hidden" id="txhcodigo" />
                </div>

                <!-- Selector Cliente -->
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <div class="flex gap-2">
                        <div class="flex-1 min-w-0">
                            <select id="txcliente" style="width:100%"
                                class="px-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pos focus:border-transparent">
                                <option value="">Consumidor Final</option>
                                <?php foreach (($clientes ?? []) as $cliente): ?>
                                <option value="<?= $cliente['cliente_id'] ?>"><?= View::e(mb_substr($cliente['nombre'], 0, 40)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" id="btn-nuevo-cliente" title="Agregar nuevo cliente"
                            class="px-3 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition shrink-0 text-sm">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Selector Método Pago -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Método Pago</label>
                    <select id="txpago"
                        class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pos focus:border-transparent">
                        <option value="EFECTIVO">EFECTIVO</option>
                        <option value="TARJETA">TARJETA</option>
                        <option value="YAPPY">YAPPY</option>
                        <option value="CHEQUE">CHEQUE</option>
                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                        <option value="CREDITO">CREDITO</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Área de contenido dividida -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Lista de productos en carrito (2/3 del ancho) -->
            <div class="flex-1 overflow-auto p-4 scroll-custom">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-full">
                    <div class="border-b border-gray-200 p-4 flex justify-between">
                        <h3 class="text-lg font-semibold text-gray-900" id="titulo-tabla">Carrito de Venta</h3>
                        <div class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-bold"><span id="item-count">0</span> items</div>
                    </div>
                    <div class="overflow-auto h-[calc(100%-60px)]">
                        <div id="contenedor-carrito">
                            <table class="min-w-full divide-y divide-gray-200" id="tabla-carrito">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Código</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Descripción</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Cantidad</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Precio</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Desc %</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Subtotal</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="posTable" class="bg-white divide-y divide-gray-200 receipt-font">
                                    <tr id="sin-productos">
                                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                            <i class="fas fa-shopping-cart text-3xl text-gray-300 mb-2"></i>
                                            <p>No hay productos en el carrito</p>
                                            <p class="text-xs mt-2">Busque productos usando el campo de búsqueda
                                                superior</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="contenedor-busqueda" class="hidden">
                            <table class="min-w-full divide-y divide-gray-200" id="tabla-busqueda">
                                <thead class="bg-pos text-white">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Foto</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Código</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Descripción</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Lugar</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase">Precios</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium uppercase">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpo-busqueda" class="bg-white divide-y divide-gray-200">
                                    <!-- Los resultados de búsqueda se cargarán aquí -->
                                </tbody>
                            </table>

                            <!-- Paginación para resultados de búsqueda -->
                            <div id="paginacion-busqueda" class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-700" id="info-paginacion"></div>
                                    <div class="flex space-x-1" id="controles-paginacion"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Panel lateral derecho (1/3 del ancho) -->
            <div class="w-96 border-l border-gray-200 flex flex-col">
                <!-- Totales -->
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                        Totales
                        <span class="text-xs font-normal text-gray-400 ml-2">Desc. máx: <?= (int)($max_descuento_pct ?? 10) ?>%</span>
                    </h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Subtotal:</span>
                            <span id="subtotal" class="text-sm font-bold">$0.00</span>
                        </div>
                        <div id="fila-descuento" class="flex justify-between items-center py-1 px-2 rounded-lg transition-colors">
                            <span class="text-sm font-semibold" id="label-descuento">Descuento:</span>
                            <span id="descuento" class="text-sm font-bold">- $0.00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">
                                <input type="checkbox" id="aplicar_itbms" class="mr-2" checked> ITBMS (7%)
                            </span>
                            <span id="itbms" class="text-sm font-bold text-green-600">$0.00</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200 mt-2">
                            <span class="text-lg font-bold text-gray-900">TOTAL:</span>
                            <span id="total" class="text-lg font-bold text-pos">$0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Comentarios -->
                <div class="p-4 border-b border-gray-200 flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Referencia FISCAL</label>
                    <textarea id="txreferencia"
                        class="w-full h-24 p-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pos focus:border-transparent"
                        placeholder="Notas o referencia para la factura..."></textarea>
                </div>

                <!-- Botones de acción -->
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3">
                        <button id="btncotizar"
                            class="btn-cotizar w-full text-white text-sm py-3 rounded-lg font-bold hover:opacity-90 action-btn">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>COTIZAR
                        </button>
                        <button id="btnpagar"
                            class="btn-pagar w-full text-white text-sm py-3 rounded-lg font-bold hover:opacity-90 action-btn">
                            <i class="fas fa-cash-register mr-2"></i>PAGAR
                        </button>
                        <button id="btncancelar"
                            class="btn-cancelar w-full text-white text-sm py-3 rounded-lg font-bold hover:opacity-90 action-btn">
                            <i class="fas fa-times mr-2"></i>CANCELAR
                        </button>
                        <button id="btnimprimir"
                            class="btn-imprimir w-full text-white text-sm py-3 rounded-lg font-bold hover:opacity-90 action-btn">
                            <i class="fas fa-print mr-2"></i>IMPRIMIR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Nuevo Cliente -->
    <div id="modal-nuevo-cliente" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-plus text-emerald-500 text-sm"></i> Nuevo Cliente
                </h3>
                <button onclick="cerrarModalCliente()" class="text-gray-300 hover:text-gray-500 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Nombre / Razón Social <span class="text-red-500">*</span></label>
                    <input type="text" id="nc-nombre" placeholder="Ej: Juan Pérez"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">RUC</label>
                        <input type="text" id="nc-ruc" placeholder="RUC opcional"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Teléfono</label>
                        <input type="text" id="nc-telefono" placeholder="6000-0000"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-5">
                <button onclick="cerrarModalCliente()"
                    class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button onclick="guardarNuevoCliente()" id="btn-guardar-cliente"
                    class="px-4 py-2 bg-emerald-500 text-white rounded-lg text-sm font-semibold hover:bg-emerald-600 transition">
                    <i class="fas fa-save mr-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toast(msg, type = 'info', duration = 5000) {
            const icons = { success: 'fa-check-circle', error: 'fa-times-circle', info: 'fa-info-circle' };
            const el = document.createElement('div');
            el.className = `toast ${type}`;
            el.innerHTML = `<i class="fas ${icons[type] || icons.info}"></i><span>${msg}</span>`;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 0.3s'; setTimeout(() => el.remove(), 300); }, duration);
        }

        let carrito = [];
        let resultadosBusqueda = [];
        let paginaActual = 1;
        const resultadosPorPagina = 5;
        const TASA_ITBMS = 0.07;
        const MAX_DESCUENTO_PCT = <?= (int)($max_descuento_pct ?? 10) ?>;
        let elementosDOM = {};

        document.addEventListener('DOMContentLoaded', function () {
            elementosDOM = {
                txcodigo: document.getElementById('txcodigo'),
                txcliente: document.getElementById('txcliente'),
                txpago: document.getElementById('txpago'),
                txreferencia: document.getElementById('txreferencia'),
                tituloTabla: document.getElementById('titulo-tabla'),
                contenedorCarrito: document.getElementById('contenedor-carrito'),
                contenedorBusqueda: document.getElementById('contenedor-busqueda'),
                subtotal: document.getElementById('subtotal'),
                itbms: document.getElementById('itbms'),
                descuento: document.getElementById('descuento'),
                total: document.getElementById('total'),
                aplicarItbms: document.getElementById('aplicar_itbms'),
                itemCount: document.getElementById('item-count')
            };

            elementosDOM.txcodigo.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    clearTimeout(window._posSearchTimer);
                    if (e.target.value.trim().length > 0) {
                        buscarProductos(e.target.value.trim());
                    }
                }
            });

            elementosDOM.txcodigo.addEventListener('input', function() {
                clearTimeout(window._posSearchTimer);
                const q = this.value.trim();
                if (q.length >= 2) {
                    window._posSearchTimer = setTimeout(() => buscarProductos(q), 400);
                } else if (q.length === 0) {
                    volverAlCarrito();
                }
            });
            
            document.getElementById('btnpagar').onclick = () => procesarDocumento('venta');
            document.getElementById('btncotizar').onclick = () => procesarDocumento('cotizacion');
            document.getElementById('btncancelar').onclick = limpiarCarrito;
            elementosDOM.aplicarItbms.onchange = actualizarTotales;

            // Initialize Select2
            $(document).ready(function() {
                $('#txcliente').select2({
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "No se encontraron clientes";
                        }
                    }
                });
            });

            cargarCarritoLocal();
            initClock();
        });

        function initClock() {
            const dateEl = document.getElementById('clock-date');
            const timeEl = document.getElementById('clock-time');
            
            function update() {
                const now = new Date();
                const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                
                dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]}`;
                timeEl.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            }
            
            update();
            setInterval(update, 1000);
        }

        function cargarCarritoLocal() {
            try {
                let s = localStorage.getItem('pos_cart');
                if (s) {
                    carrito = JSON.parse(s).map(item => ({
                        ...item,
                        descuento_pct: item.descuento_pct ?? 0
                    }));
                    actualizarTabla();
                }
            } catch (e) { carrito = []; }
        }
        function guardarCarritoLocal() {
            localStorage.setItem('pos_cart', JSON.stringify(carrito));
        }

        function buscarProductos(q) {
            fetch(`/api/productos/buscar?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    resultadosBusqueda = data.productos || [];
                    if (resultadosBusqueda.length === 1) {
                        const p = resultadosBusqueda[0];
                        const pr = parseFloat(p.precio_a) || 0;
                        agregarAlCarrito(p, 'a', pr, null, 'Unid.');
                        volverAlCarrito();
                    } else if (resultadosBusqueda.length > 1) {
                        paginaActual = 1;
                        mostrarResultados();
                    } else {
                        toast('No se encontraron productos', 'info');
                        elementosDOM.txcodigo.select();
                    }
                })
                .catch(e => {
                    toast('Error en búsqueda', 'error');
                });
        }

        function mostrarResultados() {
            elementosDOM.tituloTabla.textContent = `Resultados: ${resultadosBusqueda.length} encontrados`;
            elementosDOM.contenedorCarrito.classList.add('hidden');
            elementosDOM.contenedorBusqueda.classList.remove('hidden');

            const tbody = document.getElementById('cuerpo-busqueda');
            tbody.innerHTML = '';
            
            const inicio = (paginaActual - 1) * resultadosPorPagina;
            const fin = inicio + resultadosPorPagina;
            const pagina = resultadosBusqueda.slice(inicio, fin);

            if (pagina.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="p-4 text-center text-gray-500">Sin resultados</td></tr>`;
            } else {
                pagina.forEach(p => {
                    const imgUrl = p.imagen_principal ? '/assets/uploads/' + p.imagen_principal : '/assets/img/no-image.svg';
                    const prA = parseFloat(p.precio_a || 0);
                    const unidades = p.unidades || [];

                    // Fila base: unidad
                    let filasPrecios = `
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-gray-400 w-14 shrink-0 text-right font-medium">Unidad</span>
                            <button onclick="seleccionarYAgregar(${p.producto_id}, 'a', ${prA}, null, 'Unid.')" class="px-4 py-1.5 bg-pos hover:bg-sky-600 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">$${prA.toFixed(2)}</button>
                        </div>`;

                    // Filas por presentación
                    unidades.forEach(u => {
                        const uid = u.unidad_id;
                        const uNombre = u.nombre;
                        const uFactor = parseFloat(u.factor_conversion);
                        const uPrecio = parseFloat(u.precio_a || 0) || prA * uFactor;
                        filasPrecios += `
                        <div class="flex items-center gap-2 pt-1 border-t border-gray-100">
                            <span class="text-[10px] text-gray-500 w-14 shrink-0 text-right font-semibold truncate" title="${uNombre}">${uNombre}</span>
                            <button onclick="seleccionarYAgregar(${p.producto_id}, 'a', ${uPrecio}, ${uid}, '${uNombre}')" class="px-4 py-1.5 bg-sky-700 hover:bg-sky-800 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">$${uPrecio.toFixed(2)}</button>
                        </div>`;
                    });

                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-gray-50 transition-colors";
                    tr.innerHTML = `
                        <td class="px-3 py-2"><img src="${imgUrl}" onerror="this.onerror=null;this.src='/assets/img/no-image.svg'" class="w-12 h-12 object-cover rounded shadow-sm border border-gray-200"></td>
                        <td class="px-3 py-2 text-xs font-mono text-gray-500">${p.codigo}</td>
                        <td class="px-3 py-2">
                            <div class="text-sm font-semibold text-gray-900">${p.nombre}</div>
                            <div class="text-xs text-gray-400 mt-0.5">${p.lugar || ''}</div>
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-400 text-center">${p.lugar || '—'}</td>
                        <td class="px-3 py-2">${filasPrecios}</td>
                        <td class="px-3 py-2 text-center">
                            <button onclick="seleccionarYAgregar(${p.producto_id}, 'a', ${prA}, null, 'Unid.')" class="w-8 h-8 rounded-lg bg-gray-800 text-white flex items-center justify-center hover:bg-gray-700 transition-colors mx-auto shadow"><i class="fas fa-plus text-xs"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
            actualizarPaginacion();
        }

        function seleccionarYAgregar(id, tipo, precio, unidadId = null, presentacion = 'Unid.') {
            const p = resultadosBusqueda.find(item => item.producto_id == id);
            if (p) {
                agregarAlCarrito(p, tipo, precio, unidadId, presentacion);
                volverAlCarrito();
            }
        }

        function volverAlCarrito() {
            elementosDOM.tituloTabla.textContent = 'Carrito de Venta';
            elementosDOM.contenedorCarrito.classList.remove('hidden');
            elementosDOM.contenedorBusqueda.classList.add('hidden');
            elementosDOM.txcodigo.value = '';
            setTimeout(() => {
                const inp = document.getElementById('input-nueva-linea');
                if (inp) inp.focus();
                else elementosDOM.txcodigo.focus();
            }, 30);
        }

        function agregarAlCarrito(p, tipo, precio, unidadId = null, presentacion = 'Unid.') {
            const index = carrito.findIndex(item => item.id == p.producto_id && item.tipo == tipo && item.unidad_id == unidadId);
            if (index !== -1) {
                carrito[index].cantidad++;
            } else {
                carrito.push({
                    id: p.producto_id,
                    codigo: p.codigo,
                    nombre: p.nombre,
                    precio: parseFloat(precio),
                    tipo: tipo,
                    cantidad: 1,
                    descuento_pct: 0,
                    unidad_id: unidadId,
                    presentacion: presentacion,
                    aplica_itbms: p.itbms == 1
                });
            }
            actualizarTabla();
        }

        function actualizarTabla() {
            const tbody = document.getElementById('posTable');
            tbody.innerHTML = '';

            carrito.forEach((item, i) => {
                const tr = document.createElement('tr');
                tr.className = "bg-white hover:bg-sky-50/40 transition receipt-font";
                const descPct = item.descuento_pct || 0;
                const descMonto = item.cantidad * item.precio * (descPct / 100);
                const linea = (item.cantidad * item.precio) - descMonto;
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-400">${i + 1}</td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-600">${item.codigo}</td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900 line-clamp-1">${item.nombre}</div>
                        <div class="text-xs text-gray-400 mt-0.5">${item.presentacion || 'Unid.'}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" min="1" step="1" value="${item.cantidad}" class="w-16 text-center border border-gray-300 rounded text-sm py-1 focus:outline-none focus:border-pos" onchange="cambiarCantidad(${i}, this.value)" onkeyup="if(event.key==='Enter') this.blur();">
                    </td>
                    <td class="px-4 py-3 text-right text-sm">
                        <input type="number" step="0.01" min="0" value="${item.precio.toFixed(2)}" class="w-20 text-right border border-gray-300 rounded text-sm py-1 focus:outline-none focus:border-pos bg-gray-50" onchange="cambiarPrecio(${i}, this.value)" onkeyup="if(event.key==='Enter') this.blur();">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="relative inline-flex items-center">
                            <input type="number" step="0.5" min="0" max="${MAX_DESCUENTO_PCT}" value="${descPct.toFixed(1)}" class="w-16 text-center border border-amber-200 rounded text-sm py-1 focus:outline-none focus:border-amber-400 bg-amber-50 text-amber-700 font-semibold pr-4" onchange="cambiarDescuento(${i}, this.value)" onkeyup="if(event.key==='Enter') this.blur();">
                            <span class="absolute right-1.5 text-xs text-amber-500 pointer-events-none font-bold">%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-800">$${linea.toFixed(2)}</td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="eliminar(${i})" class="w-8 h-8 rounded text-gray-300 hover:text-red-500 transition-colors bg-white hover:bg-red-50"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Fila de entrada de código — siempre visible al final
            const trNueva = document.createElement('tr');
            trNueva.id = 'fila-nueva-linea';
            trNueva.className = 'bg-white';
            trNueva.innerHTML = `
                <td class="px-4 py-2 text-xs text-gray-300">${carrito.length + 1}</td>
                <td class="px-4 py-2">
                    <input type="text" id="input-nueva-linea"
                        placeholder="Código..."
                        autocomplete="off"
                        class="w-28 border-b border-dashed border-gray-300 text-sm font-mono text-gray-700 focus:outline-none focus:border-pos py-1 bg-transparent placeholder-gray-300">
                </td>
                <td colspan="6" class="px-4 py-2 text-xs text-gray-300 italic">Escriba un código y presione Enter para agregar</td>
            `;
            tbody.appendChild(trNueva);

            document.getElementById('input-nueva-linea').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    agregarPorCodigo(this.value);
                }
            });

            elementosDOM.itemCount.textContent = carrito.length;
            actualizarTotales();
            guardarCarritoLocal();
        }

        function agregarPorCodigo(codigo) {
            const q = (codigo || '').trim();
            if (!q) return;
            fetch(`/api/productos/buscar?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    const productos = data.productos || [];
                    if (productos.length === 1) {
                        const p = productos[0];
                        const pr = parseFloat(p.precio_a) || 0;
                        agregarAlCarrito(p, 'a', pr, null, 'Unid.');
                        setTimeout(() => {
                            const inp = document.getElementById('input-nueva-linea');
                            if (inp) { inp.value = ''; inp.focus(); }
                        }, 30);
                    } else if (productos.length > 1) {
                        resultadosBusqueda = productos;
                        paginaActual = 1;
                        mostrarResultados();
                    } else {
                        toast('Producto no encontrado: ' + q, 'info');
                        setTimeout(() => {
                            const inp = document.getElementById('input-nueva-linea');
                            if (inp) inp.select();
                        }, 30);
                    }
                })
                .catch(() => toast('Error al buscar producto', 'error'));
        }

        function cambiarCantidad(i, v) {
            carrito[i].cantidad = parseFloat(v) || 0;
            if (carrito[i].cantidad <= 0) eliminar(i);
            else actualizarTabla();
        }

        function cambiarPrecio(i, v) {
            carrito[i].precio = parseFloat(v) || 0;
            if (carrito[i].precio < 0) carrito[i].precio = 0;
            actualizarTabla();
        }

        function cambiarDescuento(i, v) {
            const pct = Math.min(MAX_DESCUENTO_PCT, Math.max(0, parseFloat(v) || 0));
            carrito[i].descuento_pct = pct;
            actualizarTabla();
        }

        function eliminar(i) {
            carrito.splice(i, 1);
            actualizarTabla();
        }

        function actualizarTotales() {
            let subtotalBruto = carrito.reduce((s, item) => s + (item.cantidad * item.precio), 0);
            let descuentoTotal = carrito.reduce((s, item) => {
                return s + (item.cantidad * item.precio * ((item.descuento_pct || 0) / 100));
            }, 0);
            let subtotalNeto = subtotalBruto - descuentoTotal;
            let itbms = elementosDOM.aplicarItbms.checked ? subtotalNeto * TASA_ITBMS : 0;
            let total = subtotalNeto + itbms;

            elementosDOM.subtotal.textContent = `$${subtotalBruto.toFixed(2)}`;
            elementosDOM.descuento.textContent = `- $${descuentoTotal.toFixed(2)}`;
            elementosDOM.itbms.textContent = `$${itbms.toFixed(2)}`;
            elementosDOM.total.textContent = `$${total.toFixed(2)}`;

            // Highlight descuento row when active
            const filaDesc = document.getElementById('fila-descuento');
            const labelDesc = document.getElementById('label-descuento');
            if (descuentoTotal > 0) {
                filaDesc.classList.add('bg-amber-50');
                labelDesc.classList.add('text-amber-600');
                labelDesc.classList.remove('text-gray-600');
                elementosDOM.descuento.classList.add('text-amber-600');
                elementosDOM.descuento.classList.remove('text-gray-800');
            } else {
                filaDesc.classList.remove('bg-amber-50');
                labelDesc.classList.remove('text-amber-600');
                labelDesc.classList.add('text-gray-600');
                elementosDOM.descuento.classList.remove('text-amber-600');
                elementosDOM.descuento.classList.add('text-gray-800');
            }
        }

        function limpiarCarrito() {
            if (carrito.length === 0) return;
            if (confirm('¿Desea limpiar el carrito actual?')) {
                carrito = [];
                actualizarTabla();
                elementosDOM.txreferencia.value = '';
                volverAlCarrito();
                guardarCarritoLocal();
            }
        }

        async function procesarDocumento(tipoDoc = 'venta') {
            if (carrito.length === 0) return toast('No hay productos para procesar', 'info');
            
            const payload = {
                tipo: tipoDoc,
                cliente_id: elementosDOM.txcliente.value || null,
                forma_pago: elementosDOM.txpago.value,
                notas: elementosDOM.txreferencia.value,
                aplicar_itbms: elementosDOM.aplicarItbms.checked,
                items: carrito.map(i => ({
                    producto_id: i.id,
                    cantidad: i.cantidad,
                    precio: i.precio,
                    descuento: i.cantidad * i.precio * ((i.descuento_pct || 0) / 100),
                    unidad_id: i.unidad_id || null
                }))
            };

            try {
                const endpoint = tipoDoc === 'venta' ? '/ventas/procesar' : '/ventas/cotizaciones/guardar';
                const r = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= $_SESSION['csrf_token'] ?? '' ?>' },
                    body: JSON.stringify(payload)
                });
                const res = await r.json();
                if (!r.ok) throw new Error(res.error || 'Error al procesar la operación');
                toast(tipoDoc === 'venta' ? 'Venta procesada exitosamente' : 'Cotización guardada', 'success');
                carrito = [];
                guardarCarritoLocal();
                location.reload();
            } catch (e) { toast(e.message, 'error'); }
        }

        function cerrarModalCliente() {
            document.getElementById('modal-nuevo-cliente').classList.add('hidden');
        }

        async function guardarNuevoCliente() {
            const nombre = document.getElementById('nc-nombre').value.trim();
            if (!nombre) { document.getElementById('nc-nombre').focus(); return; }

            const btn = document.getElementById('btn-guardar-cliente');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

            try {
                const r = await fetch('/ventas/clientes/rapido', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?= $_SESSION['csrf_token'] ?? '' ?>'
                    },
                    body: JSON.stringify({
                        nombre:   nombre,
                        ruc:      document.getElementById('nc-ruc').value.trim(),
                        telefono: document.getElementById('nc-telefono').value.trim()
                    })
                });
                const res = await r.json();
                if (!r.ok) throw new Error(res.error || 'Error al guardar');

                // Agregar al Select2 y seleccionarlo
                const opt = new Option(res.nombre, res.cliente_id, true, true);
                $('#txcliente').append(opt).trigger('change');

                cerrarModalCliente();
                toast('Cliente "' + res.nombre + '" creado', 'success');
            } catch (e) {
                toast(e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar';
            }
        }

        function actualizarPaginacion() {
            const cont = document.getElementById('controles-paginacion');
            if (!cont) return;
            cont.innerHTML = '';
            const totalPaginas = Math.ceil(resultadosBusqueda.length / resultadosPorPagina);
            if (totalPaginas <= 1) return;

            // Simple pagination for now
            for (let i = 1; i <= totalPaginas; i++) {
                const b = document.createElement('button');
                b.className = `px-3 py-1 border rounded text-sm font-medium ${i === paginaActual ? 'bg-pos text-white border-pos' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'}`;
                b.textContent = i;
                b.onclick = () => { paginaActual = i; mostrarResultados(); };
                cont.appendChild(b);
            }
        }

        // Esc: cerrar búsqueda o modal cliente
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!document.getElementById('modal-nuevo-cliente').classList.contains('hidden')) {
                    cerrarModalCliente();
                } else if (!elementosDOM.contenedorBusqueda.classList.contains('hidden')) {
                    volverAlCarrito();
                }
            }
        });

        // Botón nuevo cliente
        document.getElementById('btn-nuevo-cliente').addEventListener('click', function() {
            document.getElementById('nc-nombre').value   = '';
            document.getElementById('nc-ruc').value      = '';
            document.getElementById('nc-telefono').value = '';
            document.getElementById('modal-nuevo-cliente').classList.remove('hidden');
            setTimeout(() => document.getElementById('nc-nombre').focus(), 80);
        });

        // Enter dentro del modal
        document.getElementById('modal-nuevo-cliente').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') guardarNuevoCliente();
        });

        /* ── Panel de cotizaciones activas ── */
        var panelCotizacionesAbierto = false;
        var cotizacionesCache = null;
        var buscarCotizacionTimer = null;

        document.getElementById('btn-cotizaciones').addEventListener('click', function(e) {
            e.stopPropagation();
            togglePanelCotizaciones();
        });

        function togglePanelCotizaciones() {
            panelCotizacionesAbierto = !panelCotizacionesAbierto;
            var panel = document.getElementById('panel-cotizaciones');
            if (panelCotizacionesAbierto) {
                panel.classList.remove('hidden');
                if (!cotizacionesCache) cargarCotizacionesPendientes('');
                setTimeout(function() { document.getElementById('buscar-cotizacion').focus(); }, 80);
            } else {
                panel.classList.add('hidden');
            }
        }

        function cerrarPanelCotizaciones() {
            panelCotizacionesAbierto = false;
            document.getElementById('panel-cotizaciones').classList.add('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!document.getElementById('panel-cotizaciones').contains(e.target) &&
                e.target !== document.getElementById('btn-cotizaciones')) {
                cerrarPanelCotizaciones();
            }
        });

        document.getElementById('buscar-cotizacion').addEventListener('input', function() {
            clearTimeout(buscarCotizacionTimer);
            var q = this.value.trim();
            buscarCotizacionTimer = setTimeout(function() {
                cotizacionesCache = null;
                cargarCotizacionesPendientes(q);
            }, 350);
        });

        function cargarCotizacionesPendientes(q) {
            var lista = document.getElementById('lista-cotizaciones');
            lista.innerHTML = '<div class="px-4 py-6 text-center text-xs text-gray-400"><i class="fas fa-spinner fa-spin mr-1"></i> Cargando...</div>';
            fetch('/ventas/cotizaciones/pos/pendientes?q=' + encodeURIComponent(q))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    cotizacionesCache = data.cotizaciones || [];
                    renderizarCotizaciones(cotizacionesCache);
                })
                .catch(function() {
                    lista.innerHTML = '<div class="px-4 py-4 text-center text-xs text-red-400">Error al cargar cotizaciones</div>';
                });
        }

        function renderizarCotizaciones(lista) {
            var cont = document.getElementById('lista-cotizaciones');
            var badge = document.getElementById('badge-cotizaciones');

            badge.textContent = lista.length;
            if (lista.length > 0) badge.classList.remove('hidden');
            else badge.classList.add('hidden');

            if (lista.length === 0) {
                cont.innerHTML = '<div class="px-4 py-8 text-center"><i class="fas fa-file-invoice text-2xl text-gray-200 mb-2 block"></i><p class="text-xs text-gray-400">No hay cotizaciones activas</p></div>';
                return;
            }

            cont.innerHTML = '';
            lista.forEach(function(c) {
                var fechaStr = c.fecha ? c.fecha.substring(0, 10) : '';
                var estadoBadge = c.estado === 'aprobada'
                    ? '<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Aprobada</span>'
                    : '<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Pendiente</span>';

                var div = document.createElement('div');
                div.className = 'flex items-center gap-3 px-4 py-2.5 hover:bg-amber-50/60 transition-colors';
                div.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-gray-800 font-mono">${c.numero}</span>
                            ${estadoBadge}
                        </div>
                        <p class="text-[11px] text-gray-500 truncate mt-0.5">${c.cliente_nombre}</p>
                        <p class="text-[10px] text-gray-300 mt-0.5">${fechaStr}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-gray-800">$${parseFloat(c.total).toFixed(2)}</p>
                        <button onclick="cargarCotizacionAlCarrito(${c.cotizacion_id}, '${c.numero}')"
                            class="mt-1 px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold rounded-lg transition-colors">
                            <i class="fas fa-cart-plus mr-1"></i>Cargar
                        </button>
                    </div>
                `;
                cont.appendChild(div);
            });
        }

        async function cargarCotizacionAlCarrito(id, numero) {
            if (carrito.length > 0) {
                if (!confirm('¿Reemplazar el carrito actual con la cotización ' + numero + '?')) return;
            }
            try {
                const r = await fetch('/ventas/cotizaciones/' + id + '/items-pos');
                const data = await r.json();
                if (!data.items || data.items.length === 0) {
                    toast('La cotización no tiene items', 'info');
                    return;
                }
                carrito = data.items.map(function(item) {
                    return {
                        id:           item.producto_id,
                        codigo:       item.codigo,
                        nombre:       item.nombre,
                        precio:       parseFloat(item.precio),
                        tipo:         'a',
                        cantidad:     parseFloat(item.cantidad),
                        descuento_pct: 0,
                        unidad_id:    null,
                        presentacion: 'Unid.',
                        aplica_itbms: item.aplica_itbms == 1
                    };
                });
                actualizarTabla();
                cerrarPanelCotizaciones();
                toast('Cotización ' + numero + ' cargada en el carrito', 'success');
            } catch(e) {
                toast('Error al cargar la cotización', 'error');
            }
        }
    </script>
</body>
</html>
