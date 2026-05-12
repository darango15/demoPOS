<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoriaProducto;

class CategoriaController extends Controller
{
    public function index(): void
    {
        $categorias = CategoriaProducto::where(['empresa_id' => $this->empresaId()], 'nombre ASC');
        $this->view('categorias.lista', [
            'page_title' => 'Categorías',
            'page_subtitle' => 'Gestión de categorías de productos',
            'categorias' => $categorias,
        ]);
    }

    public function crear(): void
    {
        $categorias = CategoriaProducto::where(['empresa_id' => $this->empresaId()], 'nombre ASC');
        $this->view('categorias.crear', [
            'page_title' => 'Nueva Categoría',
            'page_subtitle' => 'Crear categoría de productos',
            'categorias' => $categorias,
        ]);
    }

    public function guardar(): void
    {
        if (!$this->verifyCsrf()) return;

        $data = [
            'empresa_id' => $this->empresaId(),
            'nombre' => $this->request->post('nombre', ''),
            'padre_id' => $this->request->post('padre_id') ?: null,
            'descripcion' => $this->request->post('descripcion', ''),
        ];

        $imagen = $this->handleUpload('imagen', 'categorias');
        if ($imagen) $data['imagen'] = $imagen;

        CategoriaProducto::create($data);
        $this->success('Categoría creada exitosamente.');
        $this->redirect('/inventario/categorias');
    }

    public function editar(int $categoria_id): void
    {
        $empresaId = $this->empresaId();
        $categoria = CategoriaProducto::whereFirst([
            'categoria_id' => $categoria_id,
            'empresa_id' => $empresaId
        ]);

        if (!$categoria) {
            $this->error('Categoría no encontrada o no autorizada.');
            $this->redirect('/inventario/categorias');
            return;
        }

        $categorias = CategoriaProducto::where(['empresa_id' => $empresaId], 'nombre ASC');

        $this->view('categorias.editar', [
            'page_title' => 'Editar Categoría',
            'page_subtitle' => $categoria->nombre,
            'categoria' => $categoria,
            'categorias' => $categorias,
            'action' => "/inventario/categorias/{$categoria->categoria_id}/editar"
        ]);
    }

    public function actualizar(int $categoria_id): void
    {
        if (!$this->verifyCsrf()) return;

        $empresaId = $this->empresaId();
        $categoria = CategoriaProducto::whereFirst([
            'categoria_id' => $categoria_id,
            'empresa_id' => $empresaId
        ]);

        if (!$categoria) {
            $this->error('Acción no permitida.');
            $this->redirect('/inventario/categorias');
            return;
        }

        $data = [
            'nombre' => $this->request->post('nombre', ''),
            'padre_id' => $this->request->post('padre_id') ?: null,
            'descripcion' => $this->request->post('descripcion', ''),
        ];

        $imagen = $this->handleUpload('imagen', 'categorias');
        if ($imagen) $data['imagen'] = $imagen;

        $categoria->update($data);
        $this->success('Categoría actualizada.');
        $this->redirect('/inventario/categorias');
    }

    public function eliminar(int $categoria_id): void
    {
        if (!$this->verifyCsrf()) return;
        
        $empresaId = $this->empresaId();
        $categoria = CategoriaProducto::whereFirst([
            'categoria_id' => $categoria_id,
            'empresa_id' => $empresaId
        ]);

        if (!$categoria) {
            $this->error('Acción no permitida.');
            $this->redirect('/inventario/categorias');
            return;
        }

        $categoria->delete();
        $this->success('Categoría eliminada.');
        $this->redirect('/inventario/categorias');
    }
}
