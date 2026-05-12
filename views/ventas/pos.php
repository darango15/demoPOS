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
                        <a href="/inventario" class="flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-pos hover:bg-blue-50 rounded-lg transition-all group">
                            <i class="fas fa-boxes mr-2 text-slate-400 group-hover:text-pos"></i>Inventario
                        </a>
                        <a href="/clientes" class="flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-pos hover:bg-blue-50 rounded-lg transition-all group">
                            <i class="fas fa-users mr-2 text-slate-400 group-hover:text-pos"></i>Clientes
                        </a>
                        <a href="/ventas" class="flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 hover:text-pos hover:bg-blue-50 rounded-lg transition-all group">
                            <i class="fas fa-list-ul mr-2 text-slate-400 group-hover:text-pos"></i>Ventas
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
                <div class="md:col-span-4">
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
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <select id="txcliente"
                        class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pos focus:border-transparent">
                        <option value="">Consumidor Final</option>
                        <?php foreach (($clientes ?? []) as $cliente): ?>
                        <option value="<?= $cliente['cliente_id'] ?>"><?= View::e(mb_substr($cliente['nombre'], 0, 30)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Selector Tipo de Precio -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Precio</label>
                    <select id="txprecio" onchange="mostrarResultados()"
                        class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pos focus:border-transparent font-semibold">
                        <option value="a" class="text-indigo-700">A — Principal</option>
                        <option value="b" class="text-emerald-700">B — Mayorista</option>
                    </select>
                </div>

                <!-- Selector Método Pago -->
                <div class="md:col-span-2">
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
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Subtotal</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="posTable" class="bg-white divide-y divide-gray-200 receipt-font">
                                    <tr id="sin-productos">
                                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Totales</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Subtotal:</span>
                            <span id="subtotal" class="text-sm font-bold">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">
                                <input type="checkbox" id="aplicar_itbms" class="mr-2"> ITBMS (7%)
                            </span>
                            <span id="itbms" class="text-sm font-bold text-green-600">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Descuento:</span>
                            <span id="descuento" class="text-sm font-bold">$0.00</span>
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
                total: document.getElementById('total'),
                aplicarItbms: document.getElementById('aplicar_itbms'),
                itemCount: document.getElementById('item-count')
            };

            elementosDOM.txcodigo.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    if (e.target.value.trim().length > 0) {
                        buscarProductos(e.target.value.trim());
                    }
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
                    carrito = JSON.parse(s);
                    actualizarTabla();
                }
            } catch (e) {}
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
                        const tp = document.getElementById('txprecio').value;
                        const pr = tp === 'a' ? parseFloat(p.precio_a) : parseFloat(p.precio_b);
                        agregarAlCarrito(p, tp, pr, null, 'Unid.');
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
                    const prB = parseFloat(p.precio_b || 0);
                    const unidades = p.unidades || [];

                    const tipoPrecio = document.getElementById('txprecio').value; // 'a' o 'b'
                    const colores = { a: ['bg-indigo-500 hover:bg-indigo-600', 'bg-indigo-700 hover:bg-indigo-800'], b: ['bg-emerald-500 hover:bg-emerald-600', 'bg-emerald-700 hover:bg-emerald-800'] };
                    const [colorBase, colorPres] = colores[tipoPrecio] || colores['a'];
                    const labelTipo = tipoPrecio.toUpperCase();
                    const precioBase = tipoPrecio === 'a' ? prA : prB;

                    // Fila base: unidad
                    let filasPrecios = `
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-gray-400 w-14 shrink-0 text-right font-medium">Unidad</span>
                            <button onclick="seleccionarYAgregar(${p.producto_id}, '${tipoPrecio}', ${precioBase}, null, 'Unid.')" class="px-4 py-1.5 ${colorBase} text-white text-sm font-bold rounded-lg transition-colors shadow-sm">$${precioBase.toFixed(2)}</button>
                        </div>`;

                    // Filas por presentación
                    unidades.forEach(u => {
                        const uid = u.unidad_id;
                        const uNombre = u.nombre;
                        const uFactor = parseFloat(u.factor_conversion);
                        const uPrecioMap = { a: parseFloat(u.precio_a || 0) || prA * uFactor, b: parseFloat(u.precio_b || 0) || prB * uFactor };
                        const uPrecio = uPrecioMap[tipoPrecio];
                        filasPrecios += `
                        <div class="flex items-center gap-2 pt-1 border-t border-gray-100">
                            <span class="text-[10px] text-gray-500 w-14 shrink-0 text-right font-semibold truncate" title="${uNombre}">${uNombre}</span>
                            <button onclick="seleccionarYAgregar(${p.producto_id}, '${tipoPrecio}', ${uPrecio}, ${uid}, '${uNombre}')" class="px-4 py-1.5 ${colorPres} text-white text-sm font-bold rounded-lg transition-colors shadow-sm">$${uPrecio.toFixed(2)}</button>
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
                            <button onclick="seleccionarYAgregar(${p.producto_id}, '${tipoPrecio}', ${precioBase}, null, 'Unid.')" class="w-8 h-8 rounded-lg bg-gray-800 text-white flex items-center justify-center hover:bg-gray-700 transition-colors mx-auto shadow"><i class="fas fa-plus text-xs"></i></button>
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
            elementosDOM.txcodigo.focus();
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
            
            if (carrito.length === 0) {
                tbody.innerHTML = `<tr id="sin-productos">
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                        <i class="fas fa-shopping-cart text-3xl text-gray-300 mb-2"></i>
                        <p>No hay productos en el carrito</p>
                        <p class="text-xs mt-2">Busque productos usando el campo de búsqueda superior</p>
                    </td>
                </tr>`;
                elementosDOM.itemCount.textContent = '0';
            } else {
                carrito.forEach((item, i) => {
                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-gray-50 transition receipt-font";
                    const subtotal = item.cantidad * item.precio;
                    tr.innerHTML = `
                        <td class="px-4 py-3 text-sm">${i + 1}</td>
                        <td class="px-4 py-3 text-sm font-mono">${item.codigo}</td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900 line-clamp-1">${item.nombre}</div>
                            <div class="text-xs text-gray-500 mt-0.5">${item.presentacion || 'Unid.'} · Precio ${item.tipo.toUpperCase()}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" min="1" step="1" value="${item.cantidad}" class="w-16 text-center border border-gray-300 rounded text-sm py-1 focus:outline-none focus:border-pos" onchange="cambiarCantidad(${i}, this.value)" onkeyup="if(event.key==='Enter') this.blur();">
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            <input type="number" step="0.01" min="0" value="${item.precio.toFixed(2)}" class="w-20 text-right border border-gray-300 rounded text-sm py-1 focus:outline-none focus:border-pos bg-gray-50" onchange="cambiarPrecio(${i}, this.value)" onkeyup="if(event.key==='Enter') this.blur();">
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-bold">$${subtotal.toFixed(2)}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="eliminar(${i})" class="w-8 h-8 rounded text-gray-400 hover:text-red-500 transition-colors bg-white hover:bg-red-50"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                elementosDOM.itemCount.textContent = carrito.length;
            }
            actualizarTotales();
            guardarCarritoLocal();
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

        function eliminar(i) {
            carrito.splice(i, 1);
            actualizarTabla();
        }

        function actualizarTotales() {
            let subtotal = carrito.reduce((s, item) => s + (item.cantidad * item.precio), 0);
            let itbms = elementosDOM.aplicarItbms.checked ? subtotal * TASA_ITBMS : 0;
            let total = subtotal + itbms;

            elementosDOM.subtotal.textContent = `$${subtotal.toFixed(2)}`;
            elementosDOM.itbms.textContent = `$${itbms.toFixed(2)}`;
            elementosDOM.total.textContent = `$${total.toFixed(2)}`;
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
                    descuento: 0,
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

        // Add keyboard esc to hide search
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !elementosDOM.contenedorBusqueda.classList.contains('hidden')) {
                volverAlCarrito();
            }
        });
    </script>
</body>
</html>
