<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Deposito;

class DepositoController extends Controller
{
    public function index(): void
    {
        $sucursalId = $this->sucursalId();
        $depositos = Database::query(
            "SELECT d.*, s.nombre as sucursal_nombre,
                    (SELECT COUNT(*) FROM inventario WHERE deposito_id = d.deposito_id) as total_productos,
                    (SELECT COALESCE(SUM(existencia), 0) FROM inventario WHERE deposito_id = d.deposito_id) as stock_total
             FROM depositos d
             LEFT JOIN branches s ON d.sucursal_id = s.sucursal_id
             WHERE d.sucursal_id = ?
             ORDER BY d.nombre",
            [$sucursalId]
        )->fetchAll();

        $this->view('depositos.lista', [
            'page_title' => 'Depósitos',
            'page_subtitle' => 'Gestión de almacenes',
            'depositos' => $depositos,
        ]);
    }

    public function crear(): void
    {
        $this->view('depositos.crear', [
            'page_title' => 'Nuevo Depósito',
            'page_subtitle' => 'Crear almacén',
        ]);
    }

    public function guardar(): void
    {
        if (!$this->verifyCsrf()) return;

        Deposito::create([
            'sucursal_id' => $this->sucursalId(),
            'codigo' => $this->request->post('codigo', ''),
            'nombre' => $this->request->post('nombre', ''),
            'descripcion' => $this->request->post('descripcion', ''),
            'es_principal' => $this->request->post('es_principal', '0'),
            'estado' => 'activo',
        ]);

        $this->success('Depósito creado exitosamente.');
        $this->redirect('/inventario/depositos');
    }

    public function detalle(int $deposito_id): void
    {
        $deposito = Deposito::findOrFail($deposito_id);
        if ($deposito->sucursal_id != $this->sucursalId()) {
            $this->error('No autorizado.');
            $this->redirect('/inventario/depositos');
            return;
        }

        $page = (int) ($this->request->get('page', '1'));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        // Contar total de productos en este depósito
        $total = (int) Database::query(
            "SELECT COUNT(*) as t FROM inventario WHERE deposito_id = ?",
            [$deposito_id]
        )->fetch()['t'];

        // Obtener productos paginados
        $inventario = Database::query(
            "SELECT i.*, p.codigo, p.nombre as producto_nombre, p.imagen_principal
             FROM inventario i
             JOIN productos p ON i.producto_id = p.producto_id
             WHERE i.deposito_id = ?
             ORDER BY p.nombre
             LIMIT {$perPage} OFFSET {$offset}",
            [$deposito_id]
        )->fetchAll();

        $totalPages = (int) ceil($total / $perPage);

        $this->view('depositos.detalle', [
            'page_title' => $deposito->nombre,
            'page_subtitle' => 'Detalle del depósito',
            'deposito' => $deposito,
            'inventario' => $inventario,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
                'previous_page' => $page - 1,
                'next_page' => $page + 1,
            ]
        ]);
    }

    public function editar(int $deposito_id): void
    {
        $deposito = Deposito::findOrFail($deposito_id);
        if ($deposito->sucursal_id != $this->sucursalId()) {
            $this->error('No autorizado.');
            $this->redirect('/inventario/depositos');
            return;
        }
        $this->view('depositos.editar', [
            'page_title' => 'Editar Depósito',
            'page_subtitle' => $deposito->nombre,
            'deposito' => $deposito,
            'action' => "/inventario/depositos/{$deposito->deposito_id}/editar"
        ]);
    }

    public function actualizar(int $deposito_id): void
    {
        if (!$this->verifyCsrf()) return;
        $deposito = Deposito::findOrFail($deposito_id);
        if ($deposito->sucursal_id != $this->sucursalId()) {
            $this->error('No autorizado.');
            $this->redirect('/inventario/depositos');
            return;
        }

        $deposito->update([
            'codigo' => $this->request->post('codigo', ''),
            'nombre' => $this->request->post('nombre', ''),
            'descripcion' => $this->request->post('descripcion', ''),
            'es_principal' => $this->request->post('es_principal', '0'),
        ]);

        $this->success('Depósito actualizado.');
        $this->redirect('/inventario/depositos');
    }

    public function eliminar(int $deposito_id): void
    {
        if (!$this->verifyCsrf()) return;
        $deposito = Deposito::findOrFail($deposito_id);
        if ($deposito->sucursal_id != $this->sucursalId()) {
            $this->error('No autorizado.');
            $this->redirect('/inventario/depositos');
            return;
        }
        $stock = Database::query(
            "SELECT COUNT(*) as cuenta FROM inventario WHERE deposito_id = ? AND existencia > 0",
            [$deposito_id]
        )->fetch();

        if (($stock['cuenta'] ?? 0) > 0) {
            $this->error("No se puede eliminar la bodega por que tiene productos con existencias. Debe trasladar los productos a otra bodega primero.");
            $this->redirect('/inventario/depositos');
            return;
        }

        Deposito::destroy($deposito_id);
        $this->success('Depósito eliminado.');
        $this->redirect('/inventario/depositos');
    }
}
