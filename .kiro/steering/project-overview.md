---
inclusion: always
---

# Taxi Diamantes — Sistema de Gestión de Servicios de Taxi

## Descripción General

Sistema web de gestión operativa para una empresa de taxis llamada "Taxi Diamantes". Permite la recepción de servicios (carreras), gestión de vehículos, clientes, sanciones y reportes. Está diseñado para uso interno por operadores de radio y administradores.

## Stack Tecnológico

- **Backend:** PHP 8.3+, Laravel 13.x
- **Frontend:** Blade templates, Bootstrap 5.3.2, Bootstrap Icons 1.11.1, Chart.js 4.4.1
- **CSS:** Tailwind CSS 4.x (configurado vía Vite pero el frontend usa Bootstrap CDN directamente)
- **Build:** Vite 7.x con laravel-vite-plugin
- **Base de datos:** MySQL/MariaDB en producción, SQLite para desarrollo local
- **Exportaciones:** maatwebsite/excel 3.1 para reportes Excel (.xlsx)
- **Cola de trabajos:** Queue driver `database`
- **Sesiones:** Driver `database`

## Idioma del Proyecto

Todo el proyecto está en **español**: nombres de tablas, columnas, variables, rutas, vistas, mensajes de validación y UI. Mantener esta convención en todo código nuevo.

## Arquitectura y Estructura

```
app/
├── Exports/              # Exportaciones Excel (maatwebsite/excel)
├── Http/
│   ├── Controllers/      # Controladores (sin subdirectorios por módulo, excepto Auth/)
│   ├── Middleware/        # CheckRole — middleware de autorización por rol
│   └── Requests/         # Form Requests organizados por entidad (Auth/, Usuario/)
├── Jobs/                 # Jobs de cola (VerificarSancionesVencidas)
├── Models/               # Modelos Eloquent
└── Providers/            # Service Providers
```

## Modelos y Base de Datos

### Entidades principales

| Modelo | Tabla | Timestamps | Descripción |
|--------|-------|------------|-------------|
| `Usuario` | `usuarios` | `created_at`, `updated_at` | Usuarios del sistema (extiende Authenticatable) |
| `Cliente` | `clientes` | No (usa `fecha_registro`, `ultima_actualizacion`) | Clientes identificados por teléfono |
| `Direccion` | `direcciones` | No (usa `fecha_registro`, `ultimo_uso`) | Direcciones de clientes con normalización colombiana |
| `Vehiculo` | `vehiculos` | No (usa `fecha_registro`, `ultima_actualizacion`) | Flota de taxis |
| `Servicio` | `servicios` | No (usa `fecha_solicitud`, `fecha_actualizacion`) | Carreras/servicios de taxi |
| `Sancion` | `sanciones` | No (usa `fecha_registro`) | Sanciones temporales a vehículos |
| `ArticuloSancion` | `articulos_sancion` | No (usa `fecha_registro`) | Catálogo de tipos de sanción |
| `HistorialServicio` | `historial_servicios` | No | Registro de cambios de estado de servicios |
| `HistorialSancion` | `historial_sanciones` | No | Registro de acciones sobre sanciones |

### Relaciones clave

- `Cliente` → hasMany `Direccion`, hasMany `Servicio`
- `Vehiculo` → hasMany `Servicio`, hasMany `Sancion`, belongsTo `Usuario` (conductor)
- `Servicio` → belongsTo `Cliente`, `Direccion`, `Vehiculo`, `Usuario` (operador)
- `Sancion` → belongsTo `Vehiculo`, `ArticuloSancion`, `Usuario`

### Convención de timestamps

La mayoría de modelos usan `$timestamps = false` y manejan fechas manualmente con columnas como `fecha_registro`, `ultima_actualizacion`, `fecha_solicitud`, etc. Solo `Usuario` usa los timestamps estándar de Laravel (`created_at`, `updated_at`).

## Roles y Permisos

Tres roles jerárquicos:

| Rol | Acceso |
|-----|--------|
| `superadmin` | Todo + Backup/Import SQL. Cuenta protegida (no se puede eliminar ni desactivar) |
| `administrador` | Usuarios, Artículos de Sanción, Reportes, Verificar vencimientos |
| `operador` | Dashboard, Recepción, Clientes, Vehículos, Sanciones (aplicar/ver) |

El middleware `CheckRole` (`role:superadmin,administrador`) protege rutas por rol. También verifica que el usuario esté activo.

## Autenticación

- Login con `username` o `email` (detección automática)
- Modelo de autenticación: `App\Models\Usuario`
- Guard: `web` (session)
- Rate limiting en login (5 intentos)
- Verificación de estado `activo` post-login

## Módulos Funcionales

### 1. Recepción de Servicios (`ServicioController`)
- Flujo de estados: `pendiente` → `asignado` → `en_camino` → `finalizado`/`cancelado`
- Operaciones con `lockForUpdate()` para concurrencia
- Polling AJAX para actualizaciones en tiempo real (no SSE/WebSocket)
- Endpoints JSON para carga inicial y cambios incrementales
- Al asignar vehículo, se marca como `ocupado`; al finalizar/cancelar, se libera a `disponible`

### 2. Clientes (`ClienteController`)
- Identificados por teléfono (único)
- Creación rápida desde módulo de servicios
- Autocompletado AJAX
- Historial de servicios con estadísticas

### 3. Direcciones (`DireccionController`)
- API JSON completa (CRUD)
- Normalización de direcciones colombianas (calle→cl, carrera→kr, etc.)
- Detección de duplicados por dirección normalizada
- Sistema de direcciones frecuentes
- Soft-delete cuando tiene servicios asociados

### 4. Vehículos (`VehiculoController`)
- Estados: `disponible`, `ocupado`, `sancionado`, `mantenimiento`, `inactivo`
- Baja lógica (cambio a `inactivo`)
- Verificación automática de sanciones vencidas al listar disponibles
- Detalle con historial de sanciones

### 5. Sanciones (`SancionController`)
- Sanciones temporales basadas en artículos con tiempo en minutos
- Auto-liberación de sanciones vencidas (en cada request + Job programado cada minuto)
- Countdown en tiempo real vía endpoint JSON
- Historial de acciones (aplicada, anulada, cumplida)

### 6. Reportes (`ReporteController`)
- Servicios, Operadores, Clientes
- Filtros por fecha, estado, operador, vehículo
- Exportación a Excel (.xlsx)
- Gráficos de tendencia

### 7. Backup/Import (`BackupImportController`)
- Solo superadmin
- Importación de archivos .sql (hasta 100MB)
- Parser SQL propio que respeta strings con comillas
- Preserva cuenta superadmin protegida durante importación
- Adaptación de columnas para compatibilidad con sistema anterior

## Patrones y Convenciones de Código

### Controladores
- Respuestas JSON para AJAX: `{ error: bool, mensaje: string, ...data }`
- Respuestas HTML para formularios tradicionales con `redirect()->with('success'|'error')`
- Validación inline en controladores (excepto Usuario que usa Form Requests)
- Transacciones DB para operaciones multi-tabla

### Vistas Blade
- Layout principal: `layouts.app` (sidebar + main content)
- Layout guest: `layouts.guest` (login)
- Parciales con prefijo `_` (ej: `_historial.blade.php`, `_detalle.blade.php`)
- CSS inline en cada vista con `@push('styles')`
- JS inline con `@push('scripts')`
- Bootstrap CDN (no compilado con Vite)

### Frontend
- AJAX con `fetch()` nativo (no Axios en las vistas)
- CSRF token desde `<meta name="csrf-token">`
- Modales Bootstrap para formularios de creación/edición
- Polling para actualizaciones en tiempo real

### Rutas
- Prefijo español para recursos (`usuarios`, `clientes`, `vehiculos`, `sanciones`)
- Endpoints API internos con prefijo `api/` pero dentro de rutas web (con sesión)
- Nombres de ruta en español (`clientes.buscar-telefono`, `sanciones.verificar-vencimientos`)

## Comandos de Desarrollo

```bash
# Setup inicial
composer setup

# Desarrollo (servidor + queue + logs + vite)
composer dev

# Tests
composer test

# Build frontend
npm run build
```

## Seed de Datos

El seeder crea un superadmin protegido:
- Email: superadmin@taxidiamantes.com
- Username: superadmin
- Rol: superadmin
- Protegido: true (no se puede eliminar)

## Consideraciones Importantes

1. **Concurrencia:** El módulo de servicios usa `lockForUpdate()` en transacciones para evitar race conditions al asignar vehículos.

2. **Sanciones auto-liberadas:** Las sanciones vencidas se liberan por tres mecanismos: Job programado cada minuto, verificación al listar sanciones, y verificación al obtener vehículos disponibles.

3. **Compatibilidad MySQL:** Los queries usan funciones MySQL (`TIMESTAMPDIFF`, `HOUR`, `DATE`, `CONCAT`). El DashboardController y ReporteController no son compatibles con SQLite.

4. **Sin API REST pura:** Los endpoints "api/" están dentro de las rutas web y requieren sesión autenticada. No hay API con tokens.

5. **Normalización de direcciones:** El sistema normaliza direcciones colombianas para detectar duplicados (calle→cl, carrera→kr, avenida→av, etc.).

6. **Backup/Import:** El sistema puede importar dumps SQL de un sistema anterior, adaptando nombres de columnas automáticamente.
