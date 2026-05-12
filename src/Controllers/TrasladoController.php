<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use App\Models\Deposito;
use App\Models\Producto;
use App\Models\Movimiento;

class TrasladoController extends Controller
{
    /**
     * Listado de traslados
     */
    public function index(): void
    {
        $sucursalId = $this->sucursalId();
        
        // Traslados donde origen o destino pertenecen a la sucursal actual
        $traslados = Database::query(
            "SELECT t.*, 
                    do.nombre as origen_nombre, 
                    dd.nombre as destino_nombre,
                    CONCAT(ue.first_name, ' ', ue.last_name) as usuario_envia_nombre
             FROM traslados t
             JOIN depositos do ON t.deposito_origen_id = do.deposito_id
             JOIN depositos dd ON t.deposito_destino_id = dd.deposito_id
             LEFT JOIN users ue ON t.usuario_envia_id = ue.id
             WHERE do.sucursal_id = ? OR dd.sucursal_id = ?
             ORDER BY t.traslado_id DESC",
            [$sucursalId, $sucursalId]
        )->fetchAll();

        $this->view('traslados.index', [
            'page_title' => 'Traslados entre Almacenes',
            'page_subtitle' => 'Mover mercancía de forma controlada',
            'traslados' => $traslados
        ]);
    }

    /**
     * Vista para crear traslado
     */
    public function crear(): void
    {
        $depositos = Deposito::where('sucursal_id', $this->sucursalId());
        $todosDepositos = Deposito::all('nombre ASC'); // Para destino puede ser otra sucursal

        $this->view('traslados.crear', [
            'page_title' => 'Nuevo Traslado',
            'page_subtitle' => 'Selecciona origen y destino',
            'depositos_origen' => $depositos,
            'depositos_destino' => $todosDepositos
        ]);
    }

    /**
     * Guardar traslado (AJAX)
     */
    public function guardar(): void
    {
        if (!$this->request->isPost()) {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }

        $data = $this->request->json();
        
        try {
            Database::beginTransaction();

            if ($data['origen_id'] == $data['destino_id']) {
                throw new \Exception('Origen y destino no pueden ser iguales.');
            }

            // 1. Crear cabecera
            $traslado = Traslado::create([
                'deposito_origen_id' => $data['origen_id'],
                'deposito_destino_id' => $data['destino_id'],
                'usuario_envia_id' => Auth::id(),
                'estado' => 'en_transito', // Lo enviamos directamente por ahora
                'fecha_envio' => date('Y-m-d H:i:s'),
                'notas' => $data['notas'] ?? ''
            ]);

            // 2. Procesar ítems y descontar de origen
            foreach ($data['items'] as $item) {
                $productoId = $item['producto_id'];
                $cantidad = $item['cantidad'];

                TrasladoDetalle::create([
                    'traslado_id' => $traslado->traslado_id,
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad
                ]);

                // Registrar salida de origen en Kardex
                $invOrigen = Database::query(
                    "SELECT inventario_id FROM inventario WHERE producto_id = ? AND deposito_id = ? LIMIT 1",
                    [$productoId, $data['origen_id']]
                )->fetch();

                if ($invOrigen) {
                    Movimiento::registrar(
                        (int)$invOrigen['inventario_id'],
                        'traslado_out',
                        (float)$cantidad,
                        (int)$traslado->traslado_id,
                        'traslados',
                        "Traslado #{$traslado->traslado_id} -> Destino"
                    );
                } else {
                    throw new \Exception("El producto ID {$productoId} no existe en el depósito de origen.");
                }
            }

            Database::commit();
            $this->json(['success' => true, 'traslado_id' => $traslado->traslado_id]);

        } catch (\Exception $e) {
            Database::rollback();
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Recibir traslado
     */
    public function recibir(int $id): void
    {
        try {
            Database::beginTransaction();

            $traslado = Traslado::findOrFail($id);
            
            if ($traslado->estado !== 'en_transito') {
                throw new \Exception('Este traslado no está en tránsito o ya fue recibido.');
            }

            // Actualizar cabecera
            $traslado->update([
                'estado' => 'recibido',
                'usuario_recibe_id' => Auth::id(),
                'fecha_recepcion' => date('Y-m-d H:i:s')
            ]);

            // Cargar ítems para sumar a destino
            $detalles = TrasladoDetalle::where('traslado_id', $id);

            foreach ($detalles as $detalle) {
                // Buscar o crear inventario en destino
                $invDestino = Database::query(
                    "SELECT inventario_id FROM inventario WHERE producto_id = ? AND deposito_id = ? LIMIT 1",
                    [$detalle->producto_id, $traslado->deposito_destino_id]
                )->fetch();

                if (!$invDestino) {
                    // Crear registro de inventario si no existe en destino
                    Database::query(
                        "INSERT INTO inventario (producto_id, deposito_id, existencia) VALUES (?, ?, 0)",
                        [$detalle->producto_id, $traslado->deposito_destino_id]
                    );
                    $invDestinoId = (int)Database::lastInsertId();
                } else {
                    $invDestinoId = (int)$invDestino['inventario_id'];
                }

                // Registrar entrada en destino Kardex
                Movimiento::registrar(
                    $invDestinoId,
                    'traslado_en',
                    (float)$detalle->cantidad,
                    (int)$traslado->traslado_id,
                    'traslados',
                    "Traslado #{$traslado->traslado_id} recibido"
                );
            }

            Database::commit();
            $this->json(['success' => true]);

        } catch (\Exception $e) {
            Database::rollback();
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
