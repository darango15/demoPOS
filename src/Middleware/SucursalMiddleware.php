<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;

/**
 * SucursalMiddleware - Detecta empresa y sucursal activa del usuario
 * Replica la lógica de SucursalMiddleware de Django
 */
class SucursalMiddleware
{
    public function handle(Request $request): bool
    {
        if (!Auth::check()) return true;

        $app = Application::getInstance();
        $session = $app->getSession();
        $isSuperuser = Auth::isSuperuser();

        // Sistema empresa única — el superusuario carga la empresa igual que cualquier usuario

        // Si ya tiene sucursal en sesión, verificar que tenga depósitos cargados
        if ($session->has('sucursal_actual') && $session->get('sucursal_actual') !== null) {
            if (!$session->has('depositos_disponibles') || empty($session->get('depositos_disponibles'))) {
                $currSucursal = $session->get('sucursal_actual');
                $depositos = $this->filtrarDepositos(Database::query(
                    "SELECT * FROM depositos WHERE sucursal_id = ? AND estado = 'activo' ORDER BY es_principal DESC",
                    [$currSucursal['sucursal_id']]
                )->fetchAll());
                $session->set('depositos_disponibles', $depositos);

                if (!empty($depositos) && (!$session->has('deposito_actual') || empty($session->get('deposito_actual')))) {
                    $session->set('deposito_actual', $depositos[0]);
                }
            }
            return true;
        }

        $userId = Auth::id();

        // Buscar perfil del usuario
        $stmt = Database::query(
            "SELECT p.*, e.empresa_id as emp_id, e.codigo as emp_codigo, e.nombre_comercial, e.razon_social, e.ruc, e.logo
             FROM user_profiles p
             JOIN companies e ON p.empresa_id = e.empresa_id
             WHERE p.user_id = ?",
            [$userId]
        );
        $perfil = $stmt->fetch();

        if ($perfil) {
            // Establecer empresa
            $session->set('empresa_actual', [
                'empresa_id' => $perfil['emp_id'],
                'codigo' => $perfil['emp_codigo'],
                'nombre_comercial' => $perfil['nombre_comercial'],
                'razon_social' => $perfil['razon_social'],
                'ruc' => $perfil['ruc'],
                'logo' => $perfil['logo'],
            ]);

            // Obtener sucursales asignadas
            $sucursales = Database::query(
                "SELECT s.* FROM branches s
                 JOIN user_profiles_sucursales ps ON s.sucursal_id = ps.sucursal_id
                 WHERE ps.perfilusuario_id = ? AND s.activa = 1",
                [$perfil['perfil_id']]
            )->fetchAll();

            $session->set('sucursales_disponibles', $sucursales);

            // Establecer sucursal actual
            if ($perfil['sucursal_actual_id']) {
                $sucursalActual = Database::query(
                    "SELECT * FROM branches WHERE sucursal_id = ?",
                    [$perfil['sucursal_actual_id']]
                )->fetch();

                $session->set('sucursal_actual', $sucursalActual);
            } elseif (!empty($sucursales)) {
                $session->set('sucursal_actual', $sucursales[0]);

                // Actualizar en DB
                Database::query(
                    "UPDATE user_profiles SET sucursal_actual_id = ? WHERE perfil_id = ?",
                    [$sucursales[0]['sucursal_id'], $perfil['perfil_id']]
                );
            }

            // Lógica de depósitos para la sucursal actual
            $currSucursal = $session->get('sucursal_actual');
            if ($currSucursal) {
                $depositos = $this->filtrarDepositos(Database::query(
                    "SELECT * FROM depositos WHERE sucursal_id = ? AND estado = 'activo' ORDER BY es_principal DESC",
                    [$currSucursal['sucursal_id']]
                )->fetchAll());

                $session->set('depositos_disponibles', $depositos);

                if (!$session->has('deposito_actual') || empty($session->get('deposito_actual'))) {
                    if (!empty($depositos)) {
                        $session->set('deposito_actual', $depositos[0]);
                    }
                } else {
                    $curr = $session->get('deposito_actual');
                    $valid = false;
                    foreach ($depositos as $d) {
                        if ($d['deposito_id'] == $curr['deposito_id']) {
                            $valid = true;
                            break;
                        }
                    }
                    if (!$valid && !empty($depositos)) {
                        $session->set('deposito_actual', $depositos[0]);
                    }
                }
            }
        } else {
            // Sin perfil: intentar crear uno automáticamente
            $empresa = Database::query(
                "SELECT * FROM companies WHERE activa = 1 ORDER BY empresa_id LIMIT 1"
            )->fetch();

            if ($empresa) {
                $sucursal = Database::query(
                    "SELECT * FROM branches WHERE empresa_id = ? AND activa = 1 ORDER BY sucursal_id LIMIT 1",
                    [$empresa['empresa_id']]
                )->fetch();

                if ($sucursal) {
                    Database::query(
                        "INSERT INTO user_profiles (user_id, empresa_id, sucursal_actual_id, cargo)
                         VALUES (?, ?, ?, '')",
                        [$userId, $empresa['empresa_id'], $sucursal['sucursal_id']]
                    );
                    $perfilId = Database::lastInsertId();

                    Database::query(
                        "INSERT INTO user_profiles_sucursales (perfilusuario_id, sucursal_id)
                         VALUES (?, ?)",
                        [$perfilId, $sucursal['sucursal_id']]
                    );

                    $session->set('empresa_actual', $empresa);
                    $session->set('sucursal_actual', $sucursal);
                    $session->set('sucursales_disponibles', [$sucursal]);

                    // Lógica de depósitos
                    $depositos = $this->filtrarDepositos(Database::query(
                        "SELECT * FROM depositos WHERE sucursal_id = ? AND estado = 'activo' ORDER BY es_principal DESC",
                        [$sucursal['sucursal_id']]
                    )->fetchAll());
                    $session->set('depositos_disponibles', $depositos);
                    if (!empty($depositos)) {
                        $session->set('deposito_actual', $depositos[0]);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Filtra la lista de depósitos según los permisos del usuario.
     * Si el usuario no tiene restricciones retorna el array sin cambios.
     */
    private function filtrarDepositos(array $depositos): array
    {
        $permitidos = Auth::depositosPermitidos();
        if (empty($permitidos)) {
            return $depositos;
        }
        return array_values(
            array_filter($depositos, fn($d) => in_array((int) $d['deposito_id'], $permitidos, true))
        );
    }

    /**
     * Fuerza la asignación de una empresa y sucursal principal
     * Útil para impersonación de Superadmin
     */
    private function forceTenantAssignment($session, int $empresaId): void
    {
        $empresa = Database::query("SELECT * FROM companies WHERE empresa_id = ?", [$empresaId])->fetch();
        if (!$empresa) return;

        $sucursal = Database::query(
            "SELECT * FROM branches WHERE empresa_id = ? AND activa = 1 ORDER BY es_principal DESC LIMIT 1",
            [$empresaId]
        )->fetch();

        if ($sucursal) {
            $session->set('empresa_actual', $empresa);
            $session->set('sucursal_actual', $sucursal);
            $session->set('sucursales_disponibles', [$sucursal]); // Al menos la principal

            $depositos = $this->filtrarDepositos(Database::query(
                "SELECT * FROM depositos WHERE sucursal_id = ? AND estado = 'activo' ORDER BY es_principal DESC",
                [$sucursal['sucursal_id']]
            )->fetchAll());

            $session->set('depositos_disponibles', $depositos);
            if (!empty($depositos)) {
                $session->set('deposito_actual', $depositos[0]);
            }
        }
    }
}
