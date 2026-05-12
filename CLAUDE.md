# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Sistema POS - Punto de Venta e Inventario** is a PHP-based Point of Sale and Inventory Management system designed for single-company deployment (no SaaS multi-tenancy). It provides web and REST API interfaces for managing products, inventory, sales, quotations, purchases, and customer accounts.

- **Technology Stack**: PHP 8.2, MySQL 8.0+, Vanilla JS (Tailwind CSS, Alpine.js)
- **Architecture**: Custom lightweight MVC framework (not Laravel, Symfony, or CodeIgniter)
- **Database**: Single MySQL database with company/branch/depot hierarchy
- **Entry Points**: `/public/index.php` (web), `/api/v1/*` (REST API)

## Architecture & Code Organization

### Core Framework (Minimal Custom MVC)

The application uses a hand-built micro-framework in `/src/Core/`:

- **Application** - Bootstrap singleton that initializes router, request, session, and runs the app
- **Router** - URL routing with pattern matching, route groups, and middleware support
- **Request** - HTTP request abstraction (GET/POST/FILES/SERVER)
- **Response** - JSON and redirect responses
- **Controller** - Base controller with view rendering, JSON responses, flash messages, file uploads
- **Model** - Active Record pattern base class with find(), where(), create(), update(), delete(), paginate()
- **Database** - PDO singleton wrapper with query() method (supports prepared statements)
- **Auth** - Authentication using `users` table (Django-compatible PBKDF2 + bcrypt password verification)
- **Session** - PHP session management with CSRF token generation
- **View** - PHP template engine with layout/section support and data injection

Key characteristic: **No dependency injection container, no query builder, raw SQL with PDO prepared statements**.

### Directory Structure

```
src/
├── Core/                 # Framework core classes
├── Controllers/          # Web controllers for HTML pages
│   ├── Api/             # REST API controllers
│   └── [Controller].php  # Individual controllers (CRUD handlers)
├── Models/              # Active Record models (26 models)
├── Middleware/          # Route middleware (Auth, Sucursal, SaaS limits)
├── Services/            # Business logic services
├── Application/         # Use case/application layer
├── Inventory/           # Inventory domain (DDD structure - unused)
├── Sales/               # Sales domain (DDD structure - unused)
└── Shared/              # Shared domain/value objects

views/
├── layouts/app.php      # Main layout with nav, flash messages
├── auth/login.php       # Login page
├── [feature]/           # Feature-specific views
└── partials/            # Reusable components

config/
└── routes.php           # All route definitions

migrations/
├── database.sql         # Full MySQL schema
└── seed_*.sql          # Data seeds

public/
├── index.php           # Front controller
└── assets/             # CSS, JS, images, uploads
```

### Routing System

Routes are defined in `/config/routes.php`. The Router supports:

- **Route methods**: `get()`, `post()`, `put()`, `delete()`
- **Route groups**: Prefix + shared middleware
- **Pattern matching**: `{id}` captures numeric IDs as `(?P<id>[0-9]+)`
- **Middleware**: Applied per-route or per-group
- **Callable actions**: Inline closures or `[ControllerClass::class, 'methodName']`

Route structure:
- **Public routes**: `/login` (no auth required)
- **Web routes** (session-based): Protected by `AuthMiddleware` and `SucursalMiddleware`
- **API routes** (`/api/v1/*`): Token-based Bearer auth, JSON responses

### Authentication & Authorization

- **Users table**: Django-compatible schema (`users`, `user_profiles`)
- **Auth class** (`src/Core/Auth.php`):
  - `attempt($username, $password)` - Verify credentials (PBKDF2 Django + bcrypt PHP)
  - `check()` - Is user authenticated?
  - `user()` - Get current user data
  - `isSuperuser()`, `isStaff()` - Role checks
- **API authentication**: Bearer token in `Authorization` header validated against `users.api_token`
- **Web authentication**: PHP session stored after `Auth::attempt()`

### Models & Database Access

All models inherit from `/src/Core/Model` and use Active Record pattern:

```php
Produto::find($id);                              // Get by ID
Produto::all();                                  // Get all
Produto::where('estado', 'activo');              // Query with condition
Produto::whereFirst(['categoria_id' => 5]);      // Get first match
Produto::paginate($perPage, $page);              // Paginated results
Produto::create(['nome' => 'X', ...]);           // Insert with mass assignment
$produto->update(['preco' => 100]);              // Update instance
$produto->delete();                              // Delete instance
```

**Fillable fields** protect against mass assignment. **No query builder** — SQL is written directly with `?` placeholders.

Key models: Producto, Inventario, Cliente, Venta, Cotizacion, Deposito, Sucursal, Lote, Movimiento.

### Controllers

**Web Controllers** (`src/Controllers/`) render HTML views:
- Inherit from `Controller` base class
- Use `$this->view($name, $data)` to render
- Implement CRUD actions: `index()`, `crear()`, `guardar()`, `editar()`, `actualizar()`, `eliminar()`
- Handle file uploads with `$this->handleUpload()`
- Flash messages: `$this->success()`, `$this->error()`, `$this->warning()`, `$this->info()`

**API Controllers** (`src/Controllers/Api/`) return JSON:
- Inherit from `ApiController` (extends `Controller`)
- Implement token authentication in constructor
- Use `$this->successResponse()`, `$this->errorResponse()`, `$this->unauthorized()`, `$this->notFound()`
- Accept JSON body via `$this->getInputData()` or form data

### Middleware

Located in `/src/Middleware/`:

- **AuthMiddleware** - Redirects to `/login` if not authenticated (or returns 401 for API)
- **SucursalMiddleware** - Sets current sucursal/deposito in session
- **BaseModuleMiddleware**, **InventarioModuleMiddleware**, **VentasModuleMiddleware** - Feature access control
- **TenantMiddleware**, **SaaSLimitMiddleware** - Prepared for multi-tenancy (currently unused)

### Views & Templating

- **Engine**: Native PHP with `extract($data)` and `ob_get_clean()` for buffering
- **Layouts**: Defined via `View::layout('app')` in views
- **Sections**: `View::section('name')` / `View::endSection('name')` / `View::yield('name')`
- **Helpers**: 
  - `View::e($value)` - HTML escape
  - `View::csrf()` - CSRF hidden input
  - `View::isActive($pattern)` - Check active nav
  - `View::include('partial.name', $data)` - Include partial

Layout (`views/layouts/app.php`) includes Tailwind CSS, Font Awesome, Alpine.js, Chart.js, navigation sidebar, flash messages.

## Development Workflow

### Setup & Installation

1. **Requirements**: PHP 8.1+, MySQL 8.0+, Composer
2. **Install dependencies**: `composer install`
3. **Configure environment**: Edit `.env` file
4. **Database**: Import `/migrations/database.sql` into MySQL
5. **Seed data** (optional): Run migration SQL files for test data
6. **Access**: Navigate to `http://ventas.test` (or configured APP_URL)

### Environment Variables (.env)

```
APP_NAME="Sistema POS"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://ventas.test
APP_TIMEZONE=America/Panama
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=pos_empresa
DB_USERNAME=root
DB_PASSWORD=
ITBMS_RATE=0.07
PAGINATION_PER_PAGE=25
```

### Common Development Tasks

**No build scripts or package.json** — vanilla PHP with static Tailwind/Alpine CDN links.

- **Run locally**: Point web root to `/public/` (or use `.htaccess` rewrite from root)
- **Test a controller**: Access route in browser or curl (Web controllers) or Bearer token (API)
- **Debug**: Check `APP_DEBUG=true` in `.env` and see error messages in browser
- **Add a route**: Edit `/config/routes.php` and create controller + view
- **Add a model**: Create class in `/src/Models/` extending `Model`, define `$table` and `$fillable`
- **Add middleware**: Create class in `/src/Middleware/`, implement `handle(Request $request): bool`

### Database Migrations

- **Schema**: `/migrations/database.sql` (run once to initialize)
- **Seeds**: `/migrations/seed_*.sql` (optional test data)
- **Migrations**: No migration system (raw SQL). Changes made directly to schema.

No test scripts, Makefile, or CI/CD pipeline exists. Manual testing or curl commands for API.

## Key Patterns & Conventions

### Single-Company Deployment

- **No tenant isolation** in code
- **Sucursal (branch)** and **Deposito (warehouse)** per company
- User session stores `sucursal_actual` and `deposito_actual` (set by middleware)
- Queries always filtered by `empresa_id` or `sucursal_id`

### Naming Conventions

- **PHP Classes**: PascalCase (e.g., `ProductoController`, `CategoriaProducto`)
- **Database Tables**: snake_case plural (e.g., `productos`, `categorias_productos`)
- **Primary Keys**: Table name + `_id` (e.g., `producto_id`, `cliente_id`)
- **Views**: kebab-case (e.g., `views/inventario/nuevo.php`)
- **Controllers**: Singular noun + `Controller` (e.g., `ProductoController`)

### Flash Messages

Used in web controllers for user feedback:

```php
$this->success('Producto guardado exitosamente');
$this->error('Error al guardar producto');
$this->warning('Stock bajo');
$this->info('Información importante');
```

Rendered in layout via `$flash` variable (types: `success`, `danger`, `warning`, `info`).

### CSRF Protection

Web forms include hidden input via `View::csrf()`. Controllers verify with `$this->verifyCsrf()` before processing POST.

## Database Schema Highlights

- **companies** (empresa_id) - Single record in deployment
- **branches** (sucursal_id) - Multiple branches per company
- **warehouses** (deposito_id) - Multiple depots per branch
- **products** (producto_id) - SKU, pricing, stock flags
- **inventory** (producto_id, deposito_id) - Stock levels by depot
- **categories** (categoria_id), **brands** (marca_id)
- **suppliers** (proveedor_id), **clients** (cliente_id)
- **sales** (venta_id), **sale_details** (venta_detalle_id)
- **purchases** (compra_id), **purchase_details** (compra_detalle_id)
- **quotations** (cotizacion_id), **quotation_details**
- **movements** (movimiento_id) - Audit log of inventory changes
- **batches** (lote_id) - For lot/expiry tracking
- **users** - Django-compatible user table with API tokens

## Common Pitfalls & Gotchas

1. **Query safety**: Always use `Database::query($sql, $params)` with `?` placeholders. Never concatenate user input.
2. **Sucursal/Deposito context**: Verify that queries in controllers filter by `$this->sucursalId()` or `$this->deposito_id` to avoid cross-branch data.
3. **Session vs. API**: Web uses PHP session; API uses Bearer token. Don't mix auth mechanisms.
4. **Active Record overhead**: For complex queries involving multiple JOINs, use raw SQL via `Database::rawQuery()` instead of Model methods.
5. **No lazy loading**: Models don't support relationships/eager loading. Manual JOIN queries needed.
6. **File uploads**: Always validate MIME types and extensions. Upload dir must be writable.
7. **Timezone**: Set in Application constructor from `APP_TIMEZONE` env var.

## Code Style & Structure Notes

- **Type declarations**: PSR-12 with `declare(strict_types=1)`
- **Namespacing**: PSR-4 autoloading under `App\*` namespace
- **Closures in routes**: Used for simple responses (e.g., API status endpoint)
- **View data**: Extracted via `extract($data)` — all variables become local in view scope
- **Error handling**: Basic exception handling; no centralized error handler defined

## Future Enhancements & Architecture Notes

- **/src/Inventory/** and **/src/Sales/** directories suggest planned DDD (Domain-Driven Design) refactoring, but are currently empty/unused
- **Tenant-related middleware** exists but unused; codebase is single-company focused
- No query builder, ORM, or schema migration tool (unlike Laravel/Eloquent)
- No comprehensive logging system
- No automated testing framework

---

**Last updated**: May 2026 — Based on codebase snapshot analysis
