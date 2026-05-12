<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class MarcaController extends Controller
{
    public function index(): void
    {
        $empresaId = $this->empresaId();
        $marcas = Database::query(
            "SELECT m.*, COUNT(p.producto_id) as total_productos
             FROM marcas m
             LEFT JOIN productos p ON p.marca = m.nombre AND p.empresa_id = m.empresa_id
             WHERE m.empresa_id = ?
             GROUP BY m.marca_id
             ORDER BY m.nombre ASC",
            [$empresaId]
        )->fetchAll();

        $this->view('inventario.marcas', [
            'page_title' => 'Marcas',
            'page_subtitle' => 'Gestión de marcas de productos',
            'marcas' => $marcas,
        ]);
    }

    public function guardar(): void
    {
        if (!$this->verifyCsrf()) return;

        $nombre = trim($this->request->post('nombre', ''));
        if ($nombre === '') {
            $this->error('El nombre de la marca es requerido.');
            $this->redirect('/inventario/marcas');
            return;
        }

        try {
            Database::query(
                "INSERT INTO marcas (empresa_id, nombre) VALUES (?, ?)",
                [$this->empresaId(), $nombre]
            );
            $this->success("Marca \"{$nombre}\" creada.");
        } catch (\Exception $e) {
            $this->error('Ya existe una marca con ese nombre.');
        }

        $this->redirect('/inventario/marcas');
    }

    public function actualizar(int $marca_id): void
    {
        if (!$this->verifyCsrf()) return;

        $nombre = trim($this->request->post('nombre', ''));
        if ($nombre === '') {
            $this->error('El nombre es requerido.');
            $this->redirect('/inventario/marcas');
            return;
        }

        $marca = Database::query(
            "SELECT * FROM marcas WHERE marca_id = ? AND empresa_id = ?",
            [$marca_id, $this->empresaId()]
        )->fetch();

        if (!$marca) {
            $this->error('Marca no encontrada.');
            $this->redirect('/inventario/marcas');
            return;
        }

        // Actualizar el campo marca en productos también
        Database::query(
            "UPDATE productos SET marca = ? WHERE marca = ? AND empresa_id = ?",
            [$nombre, $marca['nombre'], $this->empresaId()]
        );
        Database::query(
            "UPDATE marcas SET nombre = ? WHERE marca_id = ?",
            [$nombre, $marca_id]
        );

        $this->success('Marca actualizada.');
        $this->redirect('/inventario/marcas');
    }

    public function eliminar(int $marca_id): void
    {
        if (!$this->verifyCsrf()) return;

        $marca = Database::query(
            "SELECT * FROM marcas WHERE marca_id = ? AND empresa_id = ?",
            [$marca_id, $this->empresaId()]
        )->fetch();

        if (!$marca) {
            $this->error('Marca no encontrada.');
            $this->redirect('/inventario/marcas');
            return;
        }

        // Limpiar campo marca en productos
        Database::query(
            "UPDATE productos SET marca = NULL WHERE marca = ? AND empresa_id = ?",
            [$marca['nombre'], $this->empresaId()]
        );
        Database::query("DELETE FROM marcas WHERE marca_id = ?", [$marca_id]);

        $this->success('Marca eliminada.');
        $this->redirect('/inventario/marcas');
    }
}
