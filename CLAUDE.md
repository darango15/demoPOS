# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Sistema POS - Punto de Venta e Inventario** is a PHP-based Point of Sale and Inventory Management system for single-company deployment (no SaaS multi-tenancy). It provides web and REST API interfaces for managing products, inventory, sales, quotations, purchases, and customer accounts.

- **Technology Stack**: PHP 8.2, MySQL 8.0+, Vanilla JS (Tailwind CSS, Alpine.js via CDN)
- **Only external dependency**: `vlucas/phpdotenv` (`composer.json`)
- **PSR-4 autoload**: namespace `App\` maps to `src/`
- **Entry Points**: `/public/index.php` (web), `/api/v1/*` (REST API)

## Commands

```bash
composer install          # Install dependencies (only command needed)
```

There is no test suite, no PHPUnit, no static analysis (phpstan/phpcs), and no CI pipeline. Verification is manual via the browser or REST client.

## Architecture: Hexagonal + DDD

The codebase uses **hexagonal architecture (Ports & Adapters)** with **Domain-Driven Design** for the core domains, alongside a legacy MVC layer for HTTP handling.

```
src/
├── Inventory/Domain/          # BOUNDED CONTEXT: Inventory
│   ├── Model/                 #   Aggregates, Entities, Value Objects
│   └── Repository/            #   Ports (interfaces)
├── Sales/Domain/              # BOUNDED CONTEXT: Sales
│   ├── Model/                 #   Aggregates, Entities, Value Objects
│   └── Repository/            #   Port (interface)
├── Shared/Domain/ValueObject/ # Cross-domain value objects (Money, Quantity)
├── Application/               # USE CASES (orchestration, no DB/HTTP)
│   ├── Inventory/             #   Commands + Use Cases + CQRS queries
│   └── Sales/                 #   ProcessSaleUseCase
├── Infrastructure/Persistence/ # ADAPTERS (SQL implementations of ports)
├── Shared/Infrastructure/     # ServiceFactory (manual DI wiring)
│
├── Core/                      # Legacy MVC micro-framework
├── Controllers/               # HTTP entry points (call ServiceFactory for use cases)
├── Models/                    # Legacy Active Record (used outside DDD domains)
├── Middleware/                 # Auth, Sucursal, feature access
└── Services/                  # Cross-cutting services (Audit, Totp, Alertas, OrdenCompra)
```

### Domain Layer (Hexagon Core)

**Inventory bounded context** (`src/Inventory/Domain/`):

| Class | Type | Key Behavior |
|-------|------|-------------|
| `Product` | Aggregate Root | `calculatePriceWithTax()`, `changeName()`, `updateCost()` |
| `InventoryRecord` | Entity | `addStock(Quantity, Money)` recalculates weighted average cost; `isBelowMinimum()` |
| `ProductId` | Value Object | Nullable ID (null = new, int = persisted) |
| `ProductRepositoryPort` | Port | `save()`, `findById()`, `findBySku()`, `findAll()`, `delete()` |
| `InventoryRepositoryPort` | Port | `findByProductAndDeposit()`, `save()`, `findByProduct()` |
| `PriceRepositoryPort` | Port | `replacePrice()`, `findByProduct()`, `deleteByProduct()` |

**Sales bounded context** (`src/Sales/Domain/`):

| Class | Type | Key Behavior |
|-------|------|-------------|
| `Sale` | Aggregate Root | `addItem(SaleItem)` validates status, auto-recalculates totals; `void()` |
| `SaleItem` | Entity | Immutable; constructor auto-calculates subtotal, taxAmount, total |
| `SaleId` | Value Object | Nullable ID (null = new) |
| `InvoiceNumber` | Value Object | Format `F{YYYYMMDD}{seq}`; non-empty validated |
| `SaleRepositoryPort` | Port | `save()`, `findById()`, `nextInvoiceNumber()`, `findByDateRange()` |

**Shared value objects** (`src/Shared/Domain/ValueObject/`):
- `Money` — Immutable; enforces currency consistency; `add()`, `subtract()`, `multiply()`, `equals()`, `format()`
- `Quantity` — Immutable; rejects negatives in constructor; `add()`, `subtract()`, `isGreaterThan()`, `isZero()`

Domain has **zero dependency on DB, HTTP, or framework**. All business rules live here.

### Application Layer (Use Cases)

Located in `src/Application/`. Use cases depend only on domain ports (interfaces), never on concrete adapters.

**Inventory use cases**:
- `RegisterProductUseCase` + `RegisterProductCommand` — validates SKU uniqueness, creates Product, saves prices and initial stock per deposit
- `UpdateProductUseCase` + `UpdateProductCommand` — reconstitutes Product with new values, updates prices
- `DeleteProductUseCase` — validates ownership before deletion
- `AddStockUseCase` — adds stock to InventoryRecord; creates record if new deposit
- `ProductListQuery` — **CQRS read side**: raw SQL for paginated UI lists; returns plain arrays, not domain entities

**Sales use cases**:
- `ProcessSaleUseCase` — creates Sale aggregate, generates invoice number, builds SaleItems, persists via SaleRepositoryPort

### Infrastructure Layer (Adapters)

Located in `src/Infrastructure/Persistence/`. Each class implements a domain port with raw SQL via PDO.

| Adapter | Implements | Key Notes |
|---------|-----------|-----------|
| `SqlProductRepository` | `ProductRepositoryPort` | `mapToDomain()` maps DB rows → Product; INSERT or UPDATE in `save()` |
| `SqlInventoryRepository` | `InventoryRepositoryPort` | Maps `inventario` table; INSERT if `id=null`, UPDATE otherwise |
| `SqlPriceRepository` | `PriceRepositoryPort` | `replacePrice()` = DELETE then INSERT (idempotent) |
| `SqlSaleRepository` | `SaleRepositoryPort` | Transaction-wrapped: INSERT to `ventas` + `ventas_detalle`; `nextInvoiceNumber()` generates sequence |

### Dependency Injection (ServiceFactory)

`src/Shared/Infrastructure/ServiceFactory.php` is the **only place** where ports are coupled to adapters. It's a manual DI container (singleton per request):

```php
// In a controller:
$useCase = ServiceFactory::getRegisterProductUseCase();
// ServiceFactory wires: RegisterProductUseCase(SqlProductRepository, SqlInventoryRepository, SqlPriceRepository)
```

To swap a DB adapter: change only `ServiceFactory`, nowhere else.

---

## Legacy MVC Layer

The HTTP layer (`src/Controllers/`, `src/Models/`, `src/Core/`) pre-dates the DDD refactoring and coexists with it.

### Core Framework (`src/Core/`)

| Class | Purpose |
|-------|---------|
| `Application` | Bootstrap singleton; security headers, CORS, runs router |
| `Router` | `{id}` → `(?P<id>[0-9]+)` patterns; route groups; middleware |
| `Request` | HTTP abstraction; strips `/backend` or `/backend/public` URL prefixes |
| `Controller` | Base: `view()`, `empresaId()`, `sucursalId()`, `requirePermission()`, flash messages |
| `Model` | Active Record: `find()`, `all()`, `where()`, `create()`, `update()`, `delete()`, `paginate()` |
| `Database` | PDO singleton; `query($sql, $params)` with `?` placeholders; server-side prepared statements |
| `Auth` | Django-compatible PBKDF2 + bcrypt; `attempt()`, `check()`, `user()`, `isSuperuser()` |
| `View` | PHP template engine with `extract($data)` and layout/section system |

### Routing

`config/routes.php` only defines public routes (`/login`, `/logout`, `/auth/2fa/*`, `/api/v1` status, `/api/v1/login`). All other routes are registered dynamically at bootstrap via `ModuleManager::loadRoutes($router)`, which requires each installed module's `modules/{name}/routes.php`.

Route action: `[ControllerClass::class, 'methodName']` or closure.

### Module System (`modules/`)

Feature routing and sidebar menus are driven by modules. Each module is a directory under `modules/` containing:

- **`manifest.php`** — returns an array with `label`, `version`, `depends`, `menu_order`, and `menu` (sidebar links)
- **`routes.php`** — registers web and API routes directly against `$router` (injected by `ModuleManager`)

`ModuleManager` (in `src/Core/ModuleManager.php`) reads installed modules from the `modules` DB table (`estado = 'instalado'`). If the table doesn't exist (first boot), it falls back to all five built-in modules: `core`, `inventario`, `ventas`, `clientes`, `reportes`.

Key `ModuleManager` methods:
- `loadRoutes(Router)` — iterates installed modules **in DB `id` order** (insertion order), requires their `routes.php`. This matters for wildcard route conflicts.
- `getMenu()` — assembles sidebar from `core` manifest's `menu_top`/`menu_bottom` + all other installed modules' `menu` entries, **sorted by `menu_order`** from each manifest.
- `install($name)` / `uninstall($name)` — writes to `modules` table; enforces `depends` graph; `core` cannot be uninstalled

To add a new feature area: create `modules/{name}/manifest.php` and `modules/{name}/routes.php`, then install via `ModuleManager::install()`.

### Web Controllers (`src/Controllers/`)

Extend `Controller`. Standard actions: `index()`, `crear()`, `guardar()`, `editar()`, `actualizar()`, `eliminar()`.

```php
// View rendering — dot notation maps to file path:
$this->view('inventario.lista', $data);   // → views/inventario/lista.php

// Context helpers:
$this->empresaId();    // Current company ID from session
$this->sucursalId();   // Current branch ID from session

// Flash messages:
$this->success('Guardado');   $this->error('Error');
$this->warning('Atención');   $this->info('Info');
```

Controllers calling domain use cases do so via `ServiceFactory::get*UseCase()`.

### API Controllers (`src/Controllers/Api/`)

Extend `ApiController`. Constructor validates Bearer token. Response helpers: `successResponse()`, `errorResponse()`, `unauthorized()`, `notFound()`. Input via `getInputData()` (JSON or form).

### Views (`views/`)

Native PHP templates. All `$data` keys become local variables via `extract()`.

```php
View::layout('app');                    // Use views/layouts/app.php
View::section('content'); ?> ... <?php View::endSection('content');
echo View::yield('content');
View::e($value)                         // htmlspecialchars escape
View::csrf()                            // CSRF hidden input
View::isActive('/inventario/*')         // Active nav check
View::include('partials.search', $data) // Include partial
```

**Standard list view pattern** — all list views follow the same structure for consistency:
1. **4 stat cards** — `border-l-4` colored borders (sky/emerald/amber or gray/violet); sourced from a `getStats()` model method passed as `$stats`
2. **Filter bar** — debounce search (500ms, fires at ≥2 chars or empty), dropdowns with `onchange="this.form.submit()"`, per-page selector (10/25/50/100), result count + clear link to the right, primary action button
3. **Table header row** — "Ordenado por X" left, "Pág. N / N" right
4. **Table body** — `group` on `<tr>`, `hover:bg-sky-50/40`, icon avatar `w-8 h-8 rounded-lg bg-*-50`, action icons with `opacity-60 group-hover:opacity-100`
5. **State badges** — `bg-*-50 text-*-700 border border-*-100` (always include explicit `border`)
6. **Pagination** — outside and below the table `<div>`; use `View::include('partials.pagination', ['pagination' => $pagination])`
7. **JS** — debounce and any Alpine components go in `View::section('extra_js')`, never inline in `<head>`

**Alpine.js pitfall** — never put Alpine component data with `>` characters directly in an HTML attribute (`x-data="{ fn: x => x > 0 }"`). The `>` terminates the attribute and breaks parsing. Use the **named function pattern** instead:

```php
// In the template:
<div x-data="myComponent()">...</div>

// In View::section('extra_js'):
<script>
function myComponent() {
    return {
        get filtered() {
            // Safe to use > here; also use function() not arrow functions
            return this.items.filter(function(i) { return i.qty > 0; });
        }
    };
}
</script>
```

### Authentication

**Web**: `Auth::attempt($username, $password)` → PHP session (`user_id`, `username`, `is_superuser`, `rol`).  
**API**: `Authorization: Bearer <api_token>` header. Session is NOT started for `/api/*` requests.  
**2FA**: Optional TOTP for `ROLES_2FA = ['gerente', 'auditor', 'superadmin']`, managed by `TotpService`.

### Middleware

- `AuthMiddleware` — redirects to `/login`; returns 401 for API
- `SucursalMiddleware` — sets `sucursal_actual` and `deposito_actual` in session
- `BaseModuleMiddleware`, `InventarioModuleMiddleware`, `VentasModuleMiddleware` — feature access
- `TenantMiddleware`, `SaaSLimitMiddleware` — exist but unused

---

## Setup & Environment

```
APP_NAME="Sistema POS"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://ventas.test
APP_TIMEZONE=America/Panama
DB_HOST=localhost  DB_PORT=3306  DB_DATABASE=pos_empresa
DB_USERNAME=root   DB_PASSWORD=
ITBMS_RATE=0.07
PAGINATION_PER_PAGE=25
```

1. `composer install`
2. Edit `.env`
3. Import `migrations/database.sql` into MySQL (no migration tool — raw SQL only)
4. Optional seeds: `migrations/seed_*.sql`
5. Point web root to `/public/` (`.htaccess` handles rewriting)

No build scripts, no package.json. Tailwind and Alpine.js loaded from CDN.

---

## Database Schema Highlights

| Table | Key Fields |
|-------|-----------|
| `companies` (empresa_id) | Single record; `itbms_rate` |
| `branches` (sucursal_id) | `empresa_id`, `es_principal` |
| `depositos` (deposito_id) | `sucursal_id`, `es_principal`, `estado` |
| `users` | Django-compatible; `api_token`, `is_superuser`, `is_staff` |
| `user_profiles` | `user_id → empresa_id → sucursal_actual_id` mapping |
| `productos` (producto_id) | `empresa_id`, `categoria_id`, `codigo_barras`, `costo`, `itbms`, `estado` |
| `inventario` | Stock by `(producto_id, deposito_id)`; `existencia`, `costo_promedio`, `minimo` |
| `categorias_productos` | Self-referential `padre_id` for hierarchy |
| `clientes` (cliente_id) | `limite_credito`, `saldo`, `saldo_pendiente`, `dias_credito` — **note**: `saldo` and `saldo_pendiente` are separate fields; `getStats()` aggregates `saldo_pendiente`, but list views display `saldo` |
| `ventas` (venta_id) | `numero_factura`, `subtotal`, `itbms`, `forma_pago`, `estado` |
| `ventas_detalle` | `venta_id`, `producto_id`, `cantidad`, `precio` |
| `compras` (compra_id) | `estado`: `pendiente`/`recibida`/`cancelada` |
| `cotizaciones` | `estado`: `pendiente`/`aprobada`/`rechazada`/`convertida` |
| `traslados` | `deposito_origen_id`, `deposito_destino_id`, `estado` |
| `inventario_movimientos` | Audit log; `tipo`: `entrada`/`salida`/`ajuste`/`traslado` |
| `precios_productos` | Multi-tier pricing: tipo `A`, `B`, `C` |
| `modules` | `name`, `estado` (`instalado`/`desinstalado`), `version`; drives `ModuleManager` |

**Naming**: PHP classes = PascalCase; DB tables = snake_case plural; PKs = `tabla_id` pattern.

---

## Critical Pitfalls

1. **SQL injection**: Always use `Database::query($sql, $params)` with `?` placeholders. Never concatenate user input.
2. **Sucursal scope**: Every query must filter by `empresa_id` or `sucursal_id`. Middleware sets context but does not enforce it in SQL — failing to filter causes cross-branch data leaks.
3. **Session vs API**: Never start sessions in API controllers. Never use Bearer auth in web controllers.
4. **No lazy loading in Active Record**: Models have no relationship support. Write explicit JOIN queries.
5. **URL prefix stripping**: `Request::uri()` strips `/backend` or `/backend/public` — account for this in deployment.
6. **Domain boundary**: Use cases must not import from `src/Core/`, `src/Controllers/`, or `src/Models/`. Keep domain pure.
7. **ITBMS (tax)**: 7% Panama sales tax stored on both product records and sale records. Configurable via `ITBMS_RATE`.
