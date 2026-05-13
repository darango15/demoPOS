<?php
/**
 * Rutas del Sistema POS - Empresa Única
 * Sin SaaS, sin multi-tenancy, sin límites de plan.
 */

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProductoController;
use App\Controllers\CategoriaController;
use App\Controllers\ProveedorController;
use App\Controllers\DepositoController;
use App\Controllers\ClienteController;
use App\Controllers\VentaController;
use App\Controllers\CotizacionController;
use App\Controllers\ReporteController;
use App\Controllers\ConfiguracionController;
use App\Controllers\UsuarioController;
use App\Controllers\SucursalController;
use App\Controllers\MarcaController;
use App\Controllers\AlertaController;
use App\Controllers\ConteoController;
use App\Middleware\AuthMiddleware;
use App\Middleware\SucursalMiddleware;

// API Controllers
use App\Controllers\Api\ApiAuthController;
use App\Controllers\Api\ApiProductoController;
use App\Controllers\Api\ApiClientController;
use App\Controllers\Api\ApiVentaController;
use App\Controllers\Api\ApiCategoriaController;
use App\Controllers\Api\ApiProveedorController;
use App\Controllers\Api\ApiDepositoController;
use App\Controllers\Api\ApiSucursalController;
use App\Controllers\Api\ApiDashboardController;
use App\Controllers\Api\ApiUsuarioController;
use App\Controllers\Api\ApiConfiguracionController;
use App\Controllers\Api\ApiCotizacionController;
use App\Controllers\Api\ApiCompraController;
use App\Controllers\Api\ApiTrasladoController;
use App\Controllers\Api\ApiReporteController;
use App\Controllers\Api\ApiInventoryController;

$router = $app->getRouter();

// ═══════════════════════════════════════════════════════════
// RUTAS PÚBLICAS (sin autenticación)
// ═══════════════════════════════════════════════════════════

$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// 2FA — verificación (pública, sin AuthMiddleware)
$router->get('/auth/2fa',            [App\Controllers\Auth2faController::class, 'show']);
$router->post('/auth/2fa/verificar', [App\Controllers\Auth2faController::class, 'verificar']);

// ═══════════════════════════════════════════════════════════
// API REST v1
// ═══════════════════════════════════════════════════════════

// Estado de la API (sin autenticación)
$router->get('/api/v1', function () {
    header('Content-Type: application/json');
    echo json_encode([
        'status'  => 'success',
        'message' => 'API POS v1 - Empresa Única',
        'version' => '1.0.0',
    ]);
});

// Login / Logout API
$router->post('/api/v1/login',  [ApiAuthController::class, 'login']);
$router->post('/api/v1/logout', [ApiAuthController::class, 'logout']);

// ── Rutas protegidas de la API ──
$router->group(['prefix' => '/api/v1'], function ($r) {

    // Dashboard
    $r->get('/dashboard', [ApiDashboardController::class, 'index']);

    // ── Sucursales ──
    $r->get('/sucursales',          [ApiSucursalController::class, 'index']);
    $r->get('/sucursales/{id}',     [ApiSucursalController::class, 'detalle']);
    $r->post('/sucursales',         [ApiSucursalController::class, 'guardar']);
    $r->put('/sucursales/{id}',     [ApiSucursalController::class, 'actualizar']);
    $r->delete('/sucursales/{id}',  [ApiSucursalController::class, 'eliminar']);
    $r->post('/sucursales/{id}/cambiar', [ApiSucursalController::class, 'cambiar']);

    // ── Depósitos ──
    $r->get('/depositos',           [ApiDepositoController::class, 'index']);
    $r->get('/depositos/{id}',      [ApiDepositoController::class, 'detalle']);
    $r->post('/depositos',          [ApiDepositoController::class, 'guardar']);
    $r->put('/depositos/{id}',      [ApiDepositoController::class, 'actualizar']);
    $r->delete('/depositos/{id}',   [ApiDepositoController::class, 'eliminar']);

    // ── Productos ──
    $r->get('/productos',              [ApiProductoController::class, 'index']);
    $r->get('/productos/buscar',       [ApiProductoController::class, 'buscar']);
    $r->get('/productos/{id}',      [ApiProductoController::class, 'detalle']);
    $r->post('/productos',          [ApiProductoController::class, 'guardar']);
    $r->put('/productos/{id}',      [ApiProductoController::class, 'actualizar']);
    $r->delete('/productos/{id}',   [ApiProductoController::class, 'eliminar']);

    // ── Categorías ──
    $r->get('/categorias',          [ApiCategoriaController::class, 'index']);
    $r->get('/categorias/{id}',     [ApiCategoriaController::class, 'detalle']);
    $r->post('/categorias',         [ApiCategoriaController::class, 'guardar']);
    $r->put('/categorias/{id}',     [ApiCategoriaController::class, 'actualizar']);
    $r->delete('/categorias/{id}',  [ApiCategoriaController::class, 'eliminar']);

    // ── Proveedores ──
    $r->get('/proveedores',         [ApiProveedorController::class, 'index']);
    $r->get('/proveedores/buscar',  [ApiProveedorController::class, 'buscar']);
    $r->get('/proveedores/{id}',    [ApiProveedorController::class, 'detalle']);
    $r->post('/proveedores',        [ApiProveedorController::class, 'guardar']);
    $r->put('/proveedores/{id}',    [ApiProveedorController::class, 'actualizar']);
    $r->delete('/proveedores/{id}', [ApiProveedorController::class, 'eliminar']);

    // ── Inventario ──
    $r->get('/inventario/producto/{id}',  [ApiInventoryController::class, 'stockByProduct']);
    $r->get('/inventario/deposito/{id}',  [ApiInventoryController::class, 'inventoryByDeposit']);
    $r->post('/inventario/ajuste',        [ApiInventoryController::class, 'adjustStock']);

    // ── Compras ──
    $r->get('/compras',             [ApiCompraController::class, 'index']);
    $r->get('/compras/{id}',        [ApiCompraController::class, 'detalle']);
    $r->post('/compras',            [ApiCompraController::class, 'guardar']);

    // ── Traslados entre depósitos/sucursales ──
    $r->get('/traslados',           [ApiTrasladoController::class, 'index']);
    $r->get('/traslados/{id}',      [ApiTrasladoController::class, 'detalle']);
    $r->post('/traslados',          [ApiTrasladoController::class, 'guardar']);
    $r->post('/traslados/{id}/recibir', [ApiTrasladoController::class, 'recibir']);

    // ── Clientes ──
    $r->get('/clientes',            [ApiClientController::class, 'index']);
    $r->get('/clientes/buscar',     [ApiClientController::class, 'buscar']);
    $r->get('/clientes/buscar',     [ApiClientController::class, 'buscar']);
    $r->get('/clientes/{id}',       [ApiClientController::class, 'detalle']);
    $r->post('/clientes',           [ApiClientController::class, 'guardar']);
    $r->put('/clientes/{id}',       [ApiClientController::class, 'actualizar']);
    $r->delete('/clientes/{id}',    [ApiClientController::class, 'eliminar']);

    // ── Ventas ──
    $r->get('/ventas/historial',    [ApiVentaController::class, 'historial']);
    $r->get('/ventas/{id}',         [ApiVentaController::class, 'detalle']);
    $r->post('/ventas/procesar',    [ApiVentaController::class, 'procesar']);
    $r->post('/ventas/{id}/anular', [ApiVentaController::class, 'anular']);

    // ── Cotizaciones ──
    $r->get('/cotizaciones',            [ApiCotizacionController::class, 'index']);
    $r->get('/cotizaciones/{id}',       [ApiCotizacionController::class, 'detalle']);
    $r->post('/cotizaciones',           [ApiCotizacionController::class, 'guardar']);
    $r->post('/cotizaciones/{id}/convertir', [ApiCotizacionController::class, 'convertir']);

    // ── Reportes ──
    $r->get('/reportes/ventas',         [ApiReporteController::class, 'ventas']);
    $r->get('/reportes/productos-top',  [ApiReporteController::class, 'productosTop']);
    $r->get('/reportes/stock-critico',  [ApiReporteController::class, 'inventarioCritico']);
    $r->get('/reportes/compras',        [ApiReporteController::class, 'compras']);

    // ── Configuración ──
    $r->get('/configuracion/empresa',       [ApiConfiguracionController::class, 'empresa']);
    $r->put('/configuracion/empresa',       [ApiConfiguracionController::class, 'actualizarEmpresa']);
    $r->get('/configuracion/sucursal/{id}', [ApiConfiguracionController::class, 'sucursal']);

    // ── Usuarios ──
    $r->get('/usuarios',            [ApiUsuarioController::class, 'index']);
    $r->get('/usuarios/perfil',     [ApiUsuarioController::class, 'perfil']);
    $r->get('/usuarios/{id}',       [ApiUsuarioController::class, 'detalle']);
    $r->post('/usuarios',           [ApiUsuarioController::class, 'guardar']);
    $r->put('/usuarios/{id}',       [ApiUsuarioController::class, 'actualizar']);
});

// ═══════════════════════════════════════════════════════════
// RUTAS WEB PROTEGIDAS (sesión)
// ═══════════════════════════════════════════════════════════

$router->group([
    'middleware' => [AuthMiddleware::class, SucursalMiddleware::class],
], function ($r) {

    // Dashboard
    $r->get('/',          [DashboardController::class, 'index']);
    $r->get('/dashboard', [DashboardController::class, 'index']);

    // ── Inventario ──
    $r->get('/inventario',                           [ProductoController::class, 'index']);
    $r->get('/inventario/exportar-excel',            [ProductoController::class, 'exportarExcel']);
    $r->get('/inventario/nuevo',                     [ProductoController::class, 'crear']);
    $r->post('/inventario/nuevo',                    [ProductoController::class, 'guardar']);
    $r->get('/inventario/{producto_id}',             [ProductoController::class, 'detalle']);
    $r->get('/inventario/{producto_id}/editar',      [ProductoController::class, 'editar']);
    $r->post('/inventario/{producto_id}/editar',     [ProductoController::class, 'actualizar']);
    $r->post('/inventario/{producto_id}/eliminar',   [ProductoController::class, 'eliminar']);
    $r->get('/inventario/{producto_id}/precios',     [ProductoController::class, 'precios']);
    $r->post('/inventario/{producto_id}/precios',    [ProductoController::class, 'precios']);
    $r->post('/inventario/{producto_id}/stock',      [ProductoController::class, 'agregarStock']);
    $r->get('/inventario/kardex',                    [ProductoController::class, 'kardex']);

    // ── Categorías ──
    $r->get('/inventario/categorias',                        [CategoriaController::class, 'index']);
    $r->get('/inventario/categorias/nueva',                  [CategoriaController::class, 'crear']);
    $r->post('/inventario/categorias/nueva',                 [CategoriaController::class, 'guardar']);
    $r->get('/inventario/categorias/{categoria_id}/editar',  [CategoriaController::class, 'editar']);
    $r->post('/inventario/categorias/{categoria_id}/editar', [CategoriaController::class, 'actualizar']);
    $r->post('/inventario/categorias/{categoria_id}/eliminar', [CategoriaController::class, 'eliminar']);

    // ── Marcas ──
    $r->get('/inventario/marcas',                          [MarcaController::class, 'index']);
    $r->post('/inventario/marcas/guardar',                 [MarcaController::class, 'guardar']);
    $r->post('/inventario/marcas/{marca_id}/actualizar',   [MarcaController::class, 'actualizar']);
    $r->post('/inventario/marcas/{marca_id}/eliminar',     [MarcaController::class, 'eliminar']);

    // ── Proveedores ──
    $r->get('/inventario/proveedores',                           [ProveedorController::class, 'index']);
    $r->get('/inventario/proveedores/nuevo',                     [ProveedorController::class, 'crear']);
    $r->post('/inventario/proveedores/nuevo',                    [ProveedorController::class, 'guardar']);
    $r->get('/inventario/proveedores/{proveedor_id}/editar',     [ProveedorController::class, 'editar']);
    $r->post('/inventario/proveedores/{proveedor_id}/editar',    [ProveedorController::class, 'actualizar']);
    $r->post('/inventario/proveedores/{proveedor_id}/eliminar',  [ProveedorController::class, 'eliminar']);
    $r->get('/inventario/proveedores/{proveedor_id}/evaluaciones', [ProveedorController::class, 'evaluaciones']);
    $r->post('/inventario/proveedores/evaluacion/guardar',         [ProveedorController::class, 'guardarEvaluacion']);

    // ── Depósitos ──
    $r->get('/inventario/depositos',                             [DepositoController::class, 'index']);
    $r->get('/inventario/depositos/nuevo',                       [DepositoController::class, 'crear']);
    $r->post('/inventario/depositos/nuevo',                      [DepositoController::class, 'guardar']);
    $r->get('/inventario/depositos/{deposito_id}',               [DepositoController::class, 'detalle']);
    $r->get('/inventario/depositos/{deposito_id}/editar',        [DepositoController::class, 'editar']);
    $r->post('/inventario/depositos/{deposito_id}/editar',       [DepositoController::class, 'actualizar']);
    $r->post('/inventario/depositos/{deposito_id}/eliminar',     [DepositoController::class, 'eliminar']);

    // ── Compras ──
    $r->get('/compras',              [App\Controllers\CompraController::class, 'index']);
    $r->get('/compras/nueva',        [App\Controllers\CompraController::class, 'crear']);
    $r->post('/compras/guardar',     [App\Controllers\CompraController::class, 'guardar']);

    // Rutas literales antes que los patrones con {id}
    $r->get('/compras/sugerencias',                                    [App\Controllers\OrdenCompraAutoController::class, 'index']);
    $r->post('/compras/sugerencias/generar',                           [App\Controllers\OrdenCompraAutoController::class, 'generar']);
    $r->post('/compras/sugerencias/{sugerencia_id}/convertir',         [App\Controllers\OrdenCompraAutoController::class, 'convertir']);
    $r->post('/compras/sugerencias/{sugerencia_id}/descartar',         [App\Controllers\OrdenCompraAutoController::class, 'descartar']);

    $r->get('/compras/{id}',                    [App\Controllers\CompraController::class, 'detalle']);
    $r->get('/compras/{id}/recibir',            [App\Controllers\CompraController::class, 'recibir']);
    $r->post('/compras/{id}/procesar-recepcion',[App\Controllers\CompraController::class, 'procesarRecepcion']);

    // ── Lotes ──
    $r->get('/inventario/lotes',                    [App\Controllers\LoteController::class, 'index']);
    $r->post('/inventario/lotes/marcar-vencidos',   [App\Controllers\LoteController::class, 'marcarVencidos']);

    // ── Alertas de Inventario ──
    $r->get('/inventario/alertas',                          [AlertaController::class, 'index']);
    $r->post('/inventario/alertas/resolver-todas',          [AlertaController::class, 'resolverTodas']);
    $r->post('/inventario/alertas/{alerta_id}/resolver',    [AlertaController::class, 'resolver']);

    // ── Conteos / Inventario Físico ──
    $r->get('/inventario/conteos',                          [ConteoController::class, 'index']);
    $r->get('/inventario/conteos/nuevo',                    [ConteoController::class, 'crear']);
    $r->post('/inventario/conteos',                         [ConteoController::class, 'guardar']);
    $r->get('/inventario/conteos/{conteo_id}/contar',       [ConteoController::class, 'contar']);
    $r->post('/inventario/conteos/{conteo_id}/contar',      [ConteoController::class, 'guardarConteo']);
    $r->get('/inventario/conteos/{conteo_id}/reconciliar',  [ConteoController::class, 'reconciliar']);
    $r->post('/inventario/conteos/{conteo_id}/ajustar',     [ConteoController::class, 'aplicarAjustes']);
    $r->post('/inventario/conteos/{conteo_id}/cancelar',    [ConteoController::class, 'cancelar']);

    // ── Traslados ──
    $r->get('/inventario/traslados',          [App\Controllers\TrasladoController::class, 'index']);
    $r->get('/inventario/traslados/nuevo',    [App\Controllers\TrasladoController::class, 'crear']);
    $r->post('/inventario/traslados/guardar', [App\Controllers\TrasladoController::class, 'guardar']);
    $r->post('/inventario/traslados/{id}/recibir', [App\Controllers\TrasladoController::class, 'recibir']);

    // ── Clientes ──
    $r->get('/clientes',                         [ClienteController::class, 'index']);
    $r->get('/clientes/nuevo',                   [ClienteController::class, 'crear']);
    $r->post('/clientes/nuevo',                  [ClienteController::class, 'guardar']);
    $r->get('/clientes/cuentas-por-cobrar',      [ClienteController::class, 'cuentasPorCobrar']);
    $r->get('/clientes/{cliente_id}',            [ClienteController::class, 'detalle']);
    $r->get('/clientes/{cliente_id}/editar',     [ClienteController::class, 'editar']);
    $r->post('/clientes/{cliente_id}/editar',    [ClienteController::class, 'actualizar']);
    $r->post('/clientes/{cliente_id}/estado',    [ClienteController::class, 'cambiarEstado']);
    $r->post('/clientes/{cliente_id}/eliminar',  [ClienteController::class, 'eliminar']);
    $r->post('/api/clientes/crear-rapido',       [ClienteController::class, 'crearRapido']);
    $r->get('/api/clientes/buscar',              [ClienteController::class, 'buscarAjax']);

    // ── Ventas / POS ──
    $r->get('/ventas',                           [VentaController::class, 'index']);
    $r->get('/ventas/nueva',                     [VentaController::class, 'crear']);
    $r->get('/ventas/pos',                       [VentaController::class, 'puntoVenta']);
    $r->post('/ventas/procesar',                 [VentaController::class, 'procesar']);
    $r->get('/ventas/diarias',                   [VentaController::class, 'reporteVentasDiarias']);
    $r->get('/ventas/venta/{venta_id}',          [VentaController::class, 'detalle']);
    $r->post('/ventas/venta/{venta_id}/anular',  [VentaController::class, 'anular']);
    $r->post('/ventas/venta/{venta_id}/pagar',   [VentaController::class, 'registrarPago']);
    $r->get('/api/productos/buscar',             [VentaController::class, 'apiBuscarProductos']);
    $r->get('/api/productos/{producto_id}/stock',[VentaController::class, 'apiVerificarStock']);
    $r->get('/api/depositos',                    [VentaController::class, 'apiDepositos']);

    // ── Cotizaciones ──
    $r->get('/ventas/cotizaciones',                              [CotizacionController::class, 'index']);
    $r->get('/ventas/cotizaciones/nueva',                        [CotizacionController::class, 'crear']);
    $r->post('/ventas/cotizaciones/guardar',                     [CotizacionController::class, 'guardar']);
    $r->get('/ventas/cotizaciones/{cotizacion_id}',              [CotizacionController::class, 'detalle']);
    $r->post('/ventas/cotizaciones/{cotizacion_id}/convertir',   [CotizacionController::class, 'convertir']);
    $r->post('/ventas/cotizaciones/{cotizacion_id}/estado',      [CotizacionController::class, 'cambiarEstado']);

    // ── Reportes ──
    $r->get('/reportes',                     [ReporteController::class, 'index']);
    $r->get('/reportes/ventas',              [ReporteController::class, 'ventasPorPeriodo']);
    $r->get('/reportes/productos-vendidos',  [ReporteController::class, 'productosMasVendidos']);
    $r->get('/reportes/inventario',          [ReporteController::class, 'inventarioActual']);
    $r->get('/reportes/clientes-top',        [ReporteController::class, 'clientesTop']);

    // ── Usuarios ──
    $r->get('/usuarios',                    [UsuarioController::class, 'index']);
    $r->get('/usuarios/nuevo',              [UsuarioController::class, 'crear']);
    $r->post('/usuarios/nuevo',             [UsuarioController::class, 'guardar']);
    $r->get('/usuarios/perfil',             [UsuarioController::class, 'perfil']);
    $r->post('/usuarios/perfil',            [UsuarioController::class, 'perfil']);
    $r->get('/usuarios/{user_id}',          [UsuarioController::class, 'detalle']);
    $r->get('/usuarios/{user_id}/editar',   [UsuarioController::class, 'editar']);
    $r->post('/usuarios/{user_id}/editar',  [UsuarioController::class, 'actualizar']);
    $r->post('/usuarios/{user_id}/estado',  [UsuarioController::class, 'cambiarEstado']);
    $r->post('/usuarios/{user_id}/eliminar',[UsuarioController::class, 'eliminar']);
    $r->post('/usuarios/cambiar-rol',          [App\Controllers\UsuarioController::class, 'cambiarRol']);
    $r->post('/usuarios/toggle-activo',        [App\Controllers\UsuarioController::class, 'toggleActivo']);
    $r->get('/usuarios/{user_id}/permisos',    [App\Controllers\UsuarioController::class, 'permisos']);
    $r->post('/usuarios/guardar-permisos',     [App\Controllers\UsuarioController::class, 'guardarPermisos']);

    // ── 2FA Setup (protegido) ──
    $r->get('/auth/2fa/setup',           [App\Controllers\Auth2faController::class, 'setup']);
    $r->post('/auth/2fa/guardar-setup',  [App\Controllers\Auth2faController::class, 'guardarSetup']);
    $r->post('/auth/2fa/desactivar',     [App\Controllers\Auth2faController::class, 'desactivar']);

    // ── Bitácora ──
    $r->get('/bitacora', [App\Controllers\BitacoraController::class, 'index']);

    // ── Configuración ──
    $r->get('/configuracion',                       [ConfiguracionController::class, 'sistema']);
    $r->post('/configuracion/guardar',              [ConfiguracionController::class, 'guardar']);
    $r->get('/configuracion/roles',                 [ConfiguracionController::class, 'roles']);
    $r->get('/configuracion/impresoras',            [ConfiguracionController::class, 'impresoras']);
    
    // ── Sucursales ──
    $r->get('/configuracion/sucursales',                        [SucursalController::class, 'index']);
    $r->get('/configuracion/sucursales/nueva',                  [SucursalController::class, 'crear']);
    $r->post('/configuracion/sucursales/guardar',               [SucursalController::class, 'guardar']);
    $r->get('/configuracion/sucursales/{sucursal_id}/editar',   [SucursalController::class, 'editar']);
    $r->post('/configuracion/sucursales/{sucursal_id}/editar',  [SucursalController::class, 'actualizar']);
    $r->post('/configuracion/sucursales/{sucursal_id}/eliminar',[SucursalController::class, 'eliminar']);
    
    $r->get('/configuracion/sucursal/{sucursal_id}',[ConfiguracionController::class, 'cambiarSucursal']);
    $r->get('/configuracion/deposito/{deposito_id}',[ConfiguracionController::class, 'cambiarDeposito']);
});
