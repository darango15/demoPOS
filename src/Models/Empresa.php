<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Empresa extends Model
{
    protected string $table = 'companies';
    protected string $primaryKey = 'empresa_id';
    protected array $fillable = [
        'codigo', 'razon_social', 'nombre_comercial', 'ruc', 'dv',
        'direccion', 'telefono', 'email', 'sitio_web', 'activa', 'logo',
        'plan_id', 'ai_enabled', 'status_suscripcion', 'fecha_vencimiento'
    ];
}
