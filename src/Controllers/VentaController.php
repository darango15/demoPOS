<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Services\AuditService;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\Deposito;
use App\Models\Cliente;
use App\Models\Cotizacion;

class VentaController extends Controller
{
    public function index(): void
    {
        $page = (int) ($this->request->get('page', '1'));
        $buscar = $this->request->get('buscar', '');
        $estado = $this->request->get('estado', '');
        $fecha_desde = $this->request->get('fecha_inicio', '');
        $fecha_hasta = $this->request->get('fecha_fin', '');
        $cliente_id = $this->request->get('cliente', '');
        $perPage = 25;

        $sucursalId = $this->sucursalId();
        $where = 'v.sucursal_id = ?';
        $params = [$sucursalId];

        if ($buscar) {
            $where .= " AND (v.numero_factura LIKE ? OR c.nombre LIKE ?)";
            $params[] = "%{$buscar}%";
            $params[] = "%{$buscar}%";
        }
        if ($estado) {
            $where .= " AND v.estado = ?";
            $params[] = $estado;
        }
        if ($fecha_desde) {
            $where .= " AND DATE(v.fecha) >= ?";
            $params[] = $fecha_desde;
        }
        if ($fecha_hasta) {
            $where .= " AND DATE(v.fecha) <= ?";
            $params[] = $fecha_hasta;
        }
        if ($cliente_id) {
            $where .= " AND v.cliente_id = ?";
            $params[] = $cliente_id;
        }

        $offset = ($page - 1) * $perPage;
        
        // Paginación y lista de ventas
        $total = Database::query("SELECT COUNT(*) as t FROM ventas v LEFT JOIN clientes c ON v.cliente_id = c.cliente_id WHERE {$where}", $params)->fetch()['t'];
        $ventas = Database::query(
            "SELECT v.*, c.nombre as cliente_nombre, u.username as vendedor_nombre
             FROM ventas v
             LEFT JOIN clientes c ON v.cliente_id = c.cliente_id
             LEFT JOIN users u ON v.vendedor_id = u.id
             WHERE {$where}
             ORDER BY v.fecha DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        )->fetchAll();

        $totalPages = (int) ceil((int)$total / $perPage);
        
        // Métricas
        $metricas = Database::query(
            "SELECT 
                COUNT(*) as cantidad_ventas, 
                COALESCE(SUM(total), 0) as total_ventas 
             FROM ventas v LEFT JOIN clientes c ON v.cliente_id = c.cliente_id 
             WHERE {$where} AND v.estado != 'anulada'", 
            $params
        )->fetch();
        
        $ventasHoy = Database::query("SELECT COUNT(*) as t FROM ventas WHERE sucursal_id = ? AND DATE(fecha) = CURDATE() AND estado != 'anulada'", [$sucursalId])->fetch()['t'];
        
        $cantidadVentas = (int) $metricas['cantidad_ventas'];
        $totalVentas = (float) $metricas['total_ventas'];
        $promedioVenta = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;

        // Lista de clientes para el filtro
        $clientesList = Cliente::where('estado', 'activo');

        $this->view('ventas.lista', [
            'page_title' => 'Ventas',
            'page_subtitle' => 'Gestión de ventas y cotizaciones',
            'ventas' => $ventas,
            'clientes' => $clientesList,
            'buscar' => $buscar,
            'estado_actual' => $estado,
            'fecha_inicio_actual' => $fecha_desde,
            'fecha_fin_actual' => $fecha_hasta,
            'cliente_actual' => $cliente_id,
            'total_ventas' => $totalVentas,
            'cantidad_ventas' => $cantidadVentas,
            'promedio_venta' => $promedioVenta,
            'ventas_hoy_count' => $ventasHoy,
            'pagination' => [
                'total' => (int)$total,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
                'previous_page' => $page - 1,
                'next_page' => $page + 1,
            ],
        ]);
    }

    /**
     * Formulario de venta tradicional
     */
    public function crear(): void
    {
        $clientes = Cliente::where('estado', 'activo');
        $depositos = Deposito::where('estado', 'activo');
        
        $cotizacionId = (int) $this->request->get('cotizacion_id');
        $cotizacionPreload = null;

        if ($cotizacionId > 0) {
            $cot = Database::query(
                "SELECT co.*, c.nombre as cliente_nombre, c.ruc as cliente_ruc,
                        c.telefono as cliente_telefono, c.direccion as cliente_direccion
                 FROM cotizaciones co
                 LEFT JOIN clientes c ON co.cliente_id = c.cliente_id
                 WHERE co.cotizacion_id = ? AND co.sucursal_id = ?",
                [$cotizacionId, $this->sucursalId()]
            )->fetch();

            if ($cot) {
                $detalles = Database::query(
                    "SELECT d.*, p.nombre as producto_nombre, p.codigo as producto_codigo, p.itbms as p_itbms
                     FROM cotizaciones_detalle d
                     JOIN productos p ON d.producto_id = p.producto_id
                     WHERE d.cotizacion_id = ?",
                    [$cotizacionId]
                )->fetchAll();

                $cotizacionPreload = [
                    'cotizacion_id' => (int)$cot['cotizacion_id'],
                    'cliente_id' => $cot['cliente_id'],
                    'cliente_nombre' => $cot['cliente_nombre'] ?? 'Consumidor Final',
                    'cliente_ruc' => $cot['cliente_ruc'] ?? 'N/A',
                    'cliente_telefono' => $cot['cliente_telefono'] ?? 'N/A',
                    'cliente_direccion' => $cot['cliente_direccion'] ?? 'Consumidor Final',
                    'notas' => $cot['notas'] ?? '',
                    'items' => array_map(function($d) {
                        return [
                            'id' => (int)$d['producto_id'],
                            'codigo' => $d['producto_codigo'],
                            'nombre' => $d['producto_nombre'],
                            'cantidad' => (float)$d['cantidad'],
                            'precio' => (float)$d['precio'],
                            'descuento' => (float)$d['descuento'],
                            'applica_itbms' => $d['p_itbms'] == 1,
                        ];
                    }, $detalles)
                ];
            }
        }

        $this->view('ventas.crear', [
            'page_title' => 'Nueva Venta',
            'page_subtitle' => 'Crear factura',
            'clientes' => $clientes,
            'depositos' => $depositos,
            'siguiente_factura' => Venta::generarNumeroFactura(),
            'siguiente_cotizacion' => Cotizacion::generarNumeroCotizacion(),
            'hoy' => date('Y-m-d'),
            'fecha_validez' => date('Y-m-d', strtotime('+30 days')),
            'cotizacion_preload' => $cotizacionPreload,
        ]);
    }

    /**
     * Punto de venta (POS)
     */
    public function puntoVenta(): void
    {
        $clientes = Cliente::where('estado', 'activo');
        $depositos = Deposito::where('estado', 'activo');

        $this->view('ventas.pos', [
            'page_title' => 'Punto de Venta',
            'page_subtitle' => 'POS',
            'depositos' => $depositos,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Procesar venta (POST via JSON)
     */
    public function procesar(): void
    {
        if (!$this->request->isPost()) {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }

        $data = $this->request->json();
        $tipo = $data['tipo'] ?? 'venta'; // 'venta' o 'cotizacion'

        try {
            Database::beginTransaction();

            if ($tipo === 'venta') {
                $result = $this->procesarVentaNormal($data);
            } else {
                // Delegar a CotizacionController
                $result = ['error' => 'Use /ventas/cotizaciones/nueva para cotizaciones'];
            }

            Database::commit();

            if (isset($result['venta_id'])) {
                AuditService::log(
                    'ventas.crear',
                    "Venta #{$result['venta_id']} creada por $" . number_format($result['total'] ?? 0, 2),
                    $result['venta_id'], 'venta',
                    ['total' => $result['total'] ?? 0],
                    $this->empresaId()
                );
            }

            $this->json($result);

        } catch (\Exception $e) {
            Database::rollback();
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function procesarVentaNormal(array $data): array
    {
        $items = $data['items'] ?? [];
        if (empty($items)) {
            throw new \Exception('No hay productos en la venta.');
        }

        $itbmsRate = (float) ($_ENV['ITBMS_RATE'] ?? 0.07);
        $subtotal = 0;
        $costoTotal = 0;

        // Generar número de factura
        $numeroFactura = Venta::generarNumeroFactura();

        // Crear venta
        $venta = Venta::create([
            'sucursal_id' => $this->sucursalId(),
            'empresa_id' => $this->empresaId(),
            'numero_factura' => $numeroFactura,
            'cliente_id' => !empty($data['cliente_id']) ? $data['cliente_id'] : null,
            'vendedor_id' => Auth::id(),
            'forma_pago' => $data['forma_pago'] ?? 'efectivo',
            'estado' => strtolower($data['forma_pago'] ?? '') === 'credito' ? 'pendiente' : 'pagada',
            'notas' => $data['notas'] ?? '',
            'ip' => $this->request->ip(),
            'subtotal' => 0,
            'descuento' => $data['descuento'] ?? 0,
            'itbms' => 0,
            'total' => 0,
            'costo' => 0,
            'fecha' => date('Y-m-d H:i:s'),
        ]);

        foreach ($items as $item) {
            $productoId = (int) $item['producto_id'];
            $cantidad = (float) $item['cantidad'];

            // Factor de conversión por unidad de medida
            $factor = 1.0;
            if (!empty($item['unidad_id'])) {
                $u = Database::query("SELECT factor_conversion FROM productos_unidades WHERE unidad_id = ?", [$item['unidad_id']])->fetch();
                if ($u) $factor = (float)$u['factor_conversion'];
            }
            $cantidadBase = $cantidad * $factor;

            $precio = (float) $item['precio'];
            $descuento = (float) ($item['descuento'] ?? 0);

            $totalLinea = ($cantidad * $precio) - $descuento;
            $itbmsLinea = $totalLinea * $itbmsRate;
            $subtotal += $totalLinea;

            // Obtener costo del depósito específico
            $costo = 0;
            if (!empty($item['deposito_id'])) {
                $inv = Database::query(
                    "SELECT ultimo_costo FROM inventario WHERE producto_id = ? AND deposito_id = ? LIMIT 1",
                    [$productoId, $item['deposito_id']]
                )->fetch();
                $costo = $inv ? (float)($inv['ultimo_costo'] ?? 0) : 0;
            }
            $costoTotal += $costo * $cantidadBase;

            // Crear detalle
            VentaDetalle::create([
                'venta_id' => $venta->venta_id,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'unidad_id' => $item['unidad_id'] ?? null,
                'precio' => $precio,
                'costo' => $costo,
                'itbms' => $itbmsLinea,
                'descuento' => $descuento,
                'total_linea' => $totalLinea + $itbmsLinea,
                'deposito_id' => $item['deposito_id'] ?? null,
            ]);

            // Descontar inventario (Kardex auditado)
            $depositoId = $item['deposito_id'] ?? null;
            if ($depositoId) {
                $invRow = Database::query(
                    "SELECT inventario_id FROM inventario WHERE producto_id = ? AND deposito_id = ? LIMIT 1",
                    [$productoId, $depositoId]
                )->fetch();

                if ($invRow) {
                    \App\Models\Movimiento::registrar(
                        (int)$invRow['inventario_id'],
                        'salida',
                        $cantidadBase,
                        (int)$venta->venta_id,
                        'ventas',
                        "Venta #{$numeroFactura}"
                    );
                }

                // Descontar lotes FEFO si el producto maneja lotes
                $prodInfo = Database::query("SELECT maneja_lotes FROM productos WHERE producto_id=? LIMIT 1", [$productoId])->fetch();
                if ($prodInfo && $prodInfo['maneja_lotes'] && $depositoId) {
                    $consumed = \App\Models\Lote::deductFefo($productoId, (int)$depositoId, $cantidadBase, (int)$venta->venta_id, 'ventas');
                    if (!empty($consumed)) {
                        $firstLoteId = array_key_first($consumed);
                        Database::query(
                            "UPDATE ventas_detalle SET lote_id=? WHERE venta_id=? AND producto_id=? ORDER BY detalle_id DESC LIMIT 1",
                            [$firstLoteId, (int)$venta->venta_id, $productoId]
                        );
                    }
                }
            }
        }

        // Actualizar totales de la venta
        $descuentoGlobal = (float) ($data['descuento'] ?? 0);
        $itbmsTotal = $subtotal * $itbmsRate;
        $total = $subtotal + $itbmsTotal - $descuentoGlobal;

        // Lógica de Crédito
        $formaPago = strtolower($data['forma_pago'] ?? 'efectivo');
        $clienteId = !empty($data['cliente_id']) ? (int)$data['cliente_id'] : null;

        if ($formaPago === 'credito') {
            if (!$clienteId) {
                throw new \Exception('Debe seleccionar un cliente para ventas a crédito.');
            }

            $cliente = Cliente::find($clienteId);
            if (!$cliente) {
                throw new \Exception('Cliente no encontrado.');
            }

            $creditoDisponible = (float)$cliente->limite_credito - (float)$cliente->saldo_pendiente;
            if ($total > $creditoDisponible) {
                throw new \Exception("Crédito insuficiente. Disponible: $" . number_format($creditoDisponible, 2));
            }

            // Actualizar saldo del cliente
            Database::query(
                "UPDATE clientes SET saldo_pendiente = saldo_pendiente + ? WHERE cliente_id = ?",
                [$total, $clienteId]
            );
        }

        $venta->update([
            'subtotal' => round($subtotal, 2),
            'itbms' => round($itbmsTotal, 2),
            'total' => round($total, 2),
            'costo' => round($costoTotal, 2),
            'cotizacion_origen_id' => !empty($data['cotizacion_origen_id']) ? (int)$data['cotizacion_origen_id'] : null,
        ]);

        // Si viene de una cotización, marcarla como convertida
        if (!empty($data['cotizacion_origen_id'])) {
            Database::query(
                "UPDATE cotizaciones SET estado = 'convertida' WHERE cotizacion_id = ?",
                [(int)$data['cotizacion_origen_id']]
            );
        }

        return [
            'success' => true,
            'venta_id' => $venta->venta_id,
            'numero_factura' => $numeroFactura,
            'total' => round($total, 2),
        ];
    }

    /**
     * Detalle de venta
     */
    public function detalle(int $venta_id): void
    {
        $venta = Database::query(
            "SELECT v.*, c.nombre as cliente_nombre, c.ruc as cliente_ruc,
                    u.username as vendedor_nombre
             FROM ventas v
             LEFT JOIN clientes c ON v.cliente_id = c.cliente_id
             LEFT JOIN users u ON v.vendedor_id = u.id
             WHERE v.venta_id = ?",
            [$venta_id]
        )->fetch();

        if (!$venta) {
            $this->error('Venta no encontrada.');
            $this->redirect('/ventas');
            return;
        }

        $detalles = Database::query(
            "SELECT d.*, p.nombre as producto_nombre, p.codigo as producto_codigo,
                    dep.nombre as deposito_nombre
             FROM ventas_detalle d
             JOIN productos p ON d.producto_id = p.producto_id
             LEFT JOIN depositos dep ON d.deposito_id = dep.deposito_id
             WHERE d.venta_id = ?",
            [$venta_id]
        )->fetchAll();

        $this->view('ventas.detalle', [
            'page_title' => 'Venta ' . $venta['numero_factura'],
            'page_subtitle' => 'Detalle de la venta',
            'venta' => $venta,
            'detalles' => $detalles,
        ]);
    }

    /**
     * Anular venta
     */
    public function anular(int $venta_id): void
    {
        if (!$this->verifyCsrf()) return;
        if (!$this->requirePermission('ventas.anular')) return;

        $venta = Venta::findOrFail($venta_id);

        if ($venta->estado === 'anulada') {
            $this->error('Esta venta ya fue anulada.');
            $this->redirect("/ventas/venta/{$venta_id}");
            return;
        }

        Database::beginTransaction();
        try {
            // Revertir inventario
            $detalles = VentaDetalle::where('venta_id', $venta_id);
            foreach ($detalles as $detalle) {
                if ($detalle->deposito_id) {
                    Database::query(
                        "UPDATE inventario SET existencia = existencia + ?
                         WHERE producto_id = ? AND deposito_id = ?",
                        [$detalle->cantidad, $detalle->producto_id, $detalle->deposito_id]
                    );
                }
            }

            $venta->update(['estado' => 'anulada']);

            // Generar número de nota de crédito
            $count = Database::query("SELECT COUNT(*) as t FROM notas_credito WHERE DATE(fecha) = CURDATE()")->fetch()['t'];
            $numeroNota = 'NC' . date('Ymd') . str_pad((string)($count + 1), 4, '0', STR_PAD_LEFT);

            Database::query(
                "INSERT INTO notas_credito (venta_id, sucursal_id, empresa_id, numero, motivo, monto, usuario_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $venta_id,
                    $this->sucursalId(),
                    $this->empresaId(),
                    $numeroNota,
                    $this->request->post('motivo_tipo', '') === 'otro'
                        ? $this->request->post('motivo', 'Anulación de venta')
                        : $this->request->post('motivo_tipo', 'Anulación de venta'),
                    $venta->total,
                    Auth::id(),
                ]
            );

            Database::commit();
            AuditService::log(
                'ventas.anular',
                "Venta #{$venta_id} anulada — NC {$numeroNota}",
                $venta_id, 'venta',
                ['nota_credito' => $numeroNota, 'total' => $venta->total],
                $this->empresaId()
            );
            $this->success("Venta anulada. Nota de crédito {$numeroNota} generada.");
        } catch (\Exception $e) {
            Database::rollback();
            $this->error('Error al anular la venta: ' . $e->getMessage());
        }

        $this->redirect("/ventas/venta/{$venta_id}");
    }

    /**
     * Registrar pago de venta pendiente
     */
    public function registrarPago(int $venta_id): void
    {
        if (!$this->verifyCsrf()) return;

        $venta = Venta::findOrFail($venta_id);

        if ($venta->estado !== 'pendiente') {
            $this->error('Solo se pueden pagar ventas en estado pendiente.');
            $this->redirect("/ventas/venta/{$venta_id}");
            return;
        }

        Database::beginTransaction();
        try {
            $venta->update(['estado' => 'pagada']);

            // Reducir saldo pendiente del cliente
            if ($venta->cliente_id) {
                Database::query(
                    "UPDATE clientes SET saldo_pendiente = GREATEST(0, saldo_pendiente - ?) WHERE cliente_id = ?",
                    [$venta->total, $venta->cliente_id]
                );
            }

            Database::commit();
            $this->success('Pago registrado correctamente. Venta marcada como pagada.');
        } catch (\Exception $e) {
            Database::rollback();
            $this->error('Error al registrar el pago: ' . $e->getMessage());
        }

        $this->redirect("/ventas/venta/{$venta_id}");
    }

    /**
     * API: Buscar productos
     */
    public function apiBuscarProductos(): void
    {
        $q = $this->request->get('q', '');
        $sucursalId = $this->sucursalId();

        $productos = Database::query(
            "SELECT p.producto_id, p.codigo, p.nombre, p.codigo_barras, p.itbms,
                    p.maneja_lotes, p.imagen_principal, p.marca as lugar2,
                    (SELECT d.nombre FROM inventario i JOIN depositos d ON i.deposito_id = d.deposito_id
                     WHERE i.producto_id = p.producto_id AND d.sucursal_id = ? AND i.existencia > 0
                     ORDER BY d.es_principal DESC LIMIT 1) as lugar,
                    COALESCE((SELECT SUM(i.existencia) FROM inventario i JOIN depositos d ON i.deposito_id = d.deposito_id
                              WHERE i.producto_id = p.producto_id AND d.sucursal_id = ?), 0) as stock,
                    COALESCE(pp_a.precio, 0) as precio_a,
                    COALESCE(pp_b.precio, 0) as precio_b
             FROM productos p
             LEFT JOIN precios_productos pp_a ON p.producto_id = pp_a.producto_id AND pp_a.tipo_precio = 'a'
             LEFT JOIN precios_productos pp_b ON p.producto_id = pp_b.producto_id AND pp_b.tipo_precio = 'b'
             WHERE p.estado = 'activo' AND p.empresa_id = ?
             AND EXISTS (SELECT 1 FROM inventario i2 JOIN depositos d2 ON i2.deposito_id = d2.deposito_id
                         WHERE i2.producto_id = p.producto_id AND d2.sucursal_id = ?)
             AND (p.nombre LIKE ? OR p.codigo LIKE ? OR p.codigo_barras LIKE ?)
             LIMIT 100",
            [$sucursalId, $sucursalId, $this->empresaId(), $sucursalId, "%{$q}%", "%{$q}%", "%{$q}%"]
        )->fetchAll();

        foreach ($productos as &$p) {
            $p['unidades'] = Database::query(
                "SELECT * FROM productos_unidades WHERE producto_id = ?",
                [$p['producto_id']]
            )->fetchAll();
        }

        $this->json(['productos' => $productos]);
    }

    /**
     * API: Verificar stock
     */
    public function apiVerificarStock(int $producto_id): void
    {
        $inventarios = Database::query(
            "SELECT i.*, d.nombre as deposito_nombre 
             FROM inventario i 
             JOIN depositos d ON i.deposito_id = d.deposito_id
             JOIN productos p ON i.producto_id = p.producto_id
             WHERE i.producto_id = ? AND d.sucursal_id = ? AND p.empresa_id = ?",
            [$producto_id, $this->sucursalId(), $this->empresaId()]
        )->fetchAll();

        $totalStock = array_sum(array_column($inventarios, 'existencia'));

        $this->json([
            'producto_id' => $producto_id,
            'stock_total' => $totalStock,
            'inventarios' => $inventarios,
        ]);
    }

    /**
     * API: Lista de depósitos
     */
    public function apiDepositos(): void
    {
        $depositos = Database::query(
            "SELECT deposito_id, codigo, nombre, es_principal FROM depositos WHERE estado = 'activo' AND sucursal_id = ? ORDER BY nombre",
            [$this->sucursalId()]
        )->fetchAll();

        $this->json(['depositos' => $depositos]);
    }

    /**
     * Reporte ventas diarias
     */
    public function reporteVentasDiarias(): void
    {
        $ventas = Database::query(
            "SELECT v.*, c.nombre as cliente_nombre 
             FROM ventas v 
             LEFT JOIN clientes c ON v.cliente_id = c.cliente_id
             WHERE DATE(v.fecha) = CURDATE() AND v.estado != 'anulada' AND v.sucursal_id = ?
             ORDER BY v.fecha DESC",
             [$this->sucursalId()]
        )->fetchAll();

        $totales = Database::query(
            "SELECT COUNT(*) as cantidad, COALESCE(SUM(total), 0) as total,
                    COALESCE(SUM(subtotal), 0) as subtotal, COALESCE(SUM(itbms), 0) as itbms
             FROM ventas WHERE DATE(fecha) = CURDATE() AND estado != 'anulada' AND sucursal_id = ?",
             [$this->sucursalId()]
        )->fetch();

        $this->view('reportes.ventas', [
            'page_title' => 'Reporte de Ventas',
            'page_subtitle' => 'Ventas del día',
            'ventas' => $ventas,
            'totales' => $totales,
        ]);
    }
}
