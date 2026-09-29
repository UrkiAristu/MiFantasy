# 🛡️ INFORME DE AUDITORÍA TÉCNICA V2 — PROYECTO MIFANTASY
**Fecha:** 29 de Septiembre de 2026  
**Equipo Auditor:** Agent Agency (Arquitecto Clean Code, Auditor de Seguridad Ofensiva, DevOps & QA Lead)  
**Proyecto:** MiFantasy (Laravel 12 / PHP 8.2+ / Multi-Tenant SaaS)  
**Estado de Partida:** *Optimización de base de datos e indexación completada.*

---

## 📋 RESUMEN EJECUTIVO

Tras una exhaustiva revisión del código fuente, arquitectura, configuración de entornos y vectores de seguridad en el repositorio **MiFantasy**, la *Agent Agency* ha identificado **18 hallazgos clave** distribuidos en cuatro niveles de severidad:

| Severidad | Cantidad | Descripción General |
| :--- | :---: | :--- |
| 🔴 **CRÍTICO** | 4 | Vulnerabilidades CSRF por métodos destructivos `GET`, bypass de autorización IDOR/BOLA, y exposición de endpoints de depuración y reseed en rutas públicas. |
| 🟠 **ALTO** | 5 | Information Disclosure mediante excepciones SQL directas, subida insegura de archivos a `public_path()`, violaciones masivas de SRP y DRY en controladores, y acoplamiento del sistema de archivos. |
| 🟡 **MEDIO** | 6 | Ausencia de capa Form Request / DTO / Service Layer, reglas de contraseñas débiles, sesión no invalidada en eliminación de perfil, e inconsistencias en queries de estado de torneos. |
| 🔵 **BAJO** | 3 | Falta de herramientas de análisis estático (PHPStan/Larastan) y linters (Pint) en CI/CD, configuración incompleta de `.env.example` para almacenamiento en la nube, y tests unitarios incompletos para políticas de autorización. |

---

---

# 1. 🏗️ SECCIÓN: ARQUITECTURA Y CLEAN CODE
*Especialista: Arquitecto Clean Code Senior*

### 1.1. Violación Sistemática de DRY y SRP en Controladores Administrativos
- **Severidad:** 🟠 **ALTO**
- **Ubicación:** `TorneoController.php`, `EquipoController.php`, `JugadorController.php`, `PartidoController.php`, `UsuarioController.php`.
- **Diagnóstico:**
  1. **Duplicación de Autorización:** Se repite en más de 25 métodos el bloque manual:
     ```php
     if (!Auth::check() || !Auth::user()->admin) {
         return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
     }
     ```
     Esto es redundante habiendo definido el middleware `verificar.admin` en `routes/web.php` y rompe el principio DRY.
  2. **Violación de Single Responsibility (SRP):** Los controladores validan peticiones HTTP, manipulan el sistema de archivos (`move`, `unlink`), ejecutan lógica de negocio (generación de plantillas aleatorias, validación posicional) y gestionan redirecciones.
- **Solución Propuesta:**
  1. Eliminar las comprobaciones manuales en los controladores y delegar en el middleware `verificar.admin` y en **Laravel Policies**.
  2. Extraer validaciones a **Form Requests** dedicados (`StoreTorneoRequest`, `StoreJugadorRequest`, `GuardarAlineacionRequest`).
  3. Extraer la lógica de negocio a **Actions / Services** (`GenerarPlantillaAleatoriaAction`, `GuardarAlineacionAction`).

---

### 1.2. Acoplamiento Directo al Sistema de Archivos (`public_path()`)
- **Severidad:** 🟠 **ALTO**
- **Ubicación:** 
  - `TorneoController.php:102, 122, 198`
  - `EquipoController.php:79, 98, 172`
  - `JugadorController.php:87, 107, 195`
- **Diagnóstico:**
  Se utilizan llamadas directas a `$request->file('...')->move(public_path(...))` y `unlink(public_path(...))` en lugar de utilizar la fachada `Storage` de Laravel.
  - **Consecuencias:** Imposibilita el despliegue en infraestructuras modernas (contenedores Docker efímeros, AWS S3, Google Cloud Storage) y no respeta la configuración `FILESYSTEM_DISK`.
- **Solución Propuesta:**
  Migrar todas las operaciones de subida y eliminación al disco `public` o `s3` mediante `Illuminate\Support\Facades\Storage`.

```php
// Refactorización con Storage Facade
if ($request->hasFile('foto')) {
    if ($jugador->foto) {
        Storage::disk('public')->delete($jugador->foto);
    }
    $path = $request->file('foto')->store('jugadores_fotos', 'public');
    $jugador->foto = $path;
}
```

---

### 1.3. Ausencia de Capa de Servicios y Lógica de Negocio en Controladores
- **Severidad:** 🟡 **MEDIO**
- **Ubicación:** `LiguillaController.php:148-183` (`crearPlantillaAleatoria`), `AlineacionController.php:25-95` (`guardarAlineacion`).
- **Diagnóstico:**
  La lógica de ensamblado de plantillas aleatorias y validación de esquemas tácticos (4-3-3, 4-4-2, etc.) está incrustada directamente en los métodos de los controladores, dificultando los tests unitarios y la reutilización en comandos Artisan o eventos de dominio.
- **Solución Propuesta:**
  Crear una clase de servicio o acción de dominio: `app/Actions/Plantilla/GenerarPlantillaAleatoriaAction.php` y `app/Actions/Alineacion/GuardarAlineacionAction.php`.

```php
namespace App\Actions\Plantilla;

use App\Models\Liguilla;
use App\Models\Plantilla;
use App\Models\Jugador;
use Illuminate\Support\Facades\DB;

class GenerarPlantillaAleatoriaAction
{
    public function execute(Liguilla $liguilla, int $usuarioId): Plantilla
    {
        return DB::transaction(function () use ($liguilla, $usuarioId) {
            $plantilla = Plantilla::create([
                'liguilla_id' => $liguilla->id,
                'user_id'     => $usuarioId,
            ]);

            $limite = $liguilla->torneo->jugadores_por_equipo + 3;

            $jugadorIds = Jugador::whereHas('participaciones', function ($query) use ($liguilla) {
                $query->where('torneo_id', $liguilla->torneo_id);
            })
            ->whereDoesntHave('plantillas', function ($query) use ($liguilla) {
                $query->where('liguilla_id', $liguilla->id);
            })
            ->inRandomOrder()
            ->limit($limite)
            ->pluck('id');

            if ($jugadorIds->isNotEmpty()) {
                $now = now();
                $registros = $jugadorIds->map(fn($id) => [
                    'plantilla_id' => $plantilla->id,
                    'jugador_id'   => $id,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ])->all();

                DB::table('jugador_plantilla')->insert($registros);
            }

            return $plantilla;
        });
    }
}
```

---

### 1.4. Anti-patrón de Consultas Eloquent Inconsistentes y Falta de Route Model Binding
- **Severidad:** 🟡 **MEDIO**
- **Ubicación:** `TorneoController.php:462-466`
- **Diagnóstico:**
  En `mostrarPaginaTorneosUser`:
  ```php
  $torneos = Torneo::where('estado', 'activo')
      ->orWhere('estado', '1')
      ->orWhere('estado', 1)
      ->orWhere('estado', true)
      ->with('equipos')
      ->get();
  ```
  La concatenación de `orWhere` sin agrupar provoca que las cláusulas SQL no estén encapsuladas, pudiendo ignorar otros filtros futuros. Además, denota una inconsistencia de tipo de datos para el campo `estado` (string vs boolean vs integer).
- **Solución Propuesta:**
  1. Estandarizar la columna `estado` como `boolean` en la base de datos con un cast en el modelo `Torneo` (`'estado' => 'boolean'`).
  2. Implementar un Local Scope en el modelo `Torneo`:
     ```php
     // En App\Models\Torneo
     public function scopeActivos($query)
     {
         return $query->where('estado', true);
     }
     ```
  3. Utilizar **Route Model Binding** implícito en rutas en lugar de `$id` y llamadas manuales a `findOrFail($id)`.

---

---

# 2. 🔴 SECCIÓN: AUDITORÍA DE SEGURIDAD OFENSIVA
*Especialista: Auditor de Seguridad Ofensiva Senior*

### 2.1. Vulnerabilidad Crítica de CSRF mediante Verbos HTTP `GET` Destructivos
- **Severidad:** 🔴 **CRÍTICO** (CVSS 8.8 / CWE-352)
- **Ubicación:** `routes/web.php:142, 145, 149, 155, 158, 161, 167, 170, 178, 183`
- **Diagnóstico:**
  Se definen múltiples operaciones de borrado mediante el método HTTP `GET`:
  ```php
  Route::get('/admin/torneos/{id}/eliminar', [TorneoController::class, 'eliminarTorneo']);
  Route::get('/admin/torneos/{id}/equipos/{equipoId}/eliminar', [TorneoController::class, 'eliminarEquipoDeTorneo']);
  Route::get('/admin/equipos/{id}/eliminar', [EquipoController::class, 'eliminarEquipo']);
  Route::get('/admin/jugadores/{id}/eliminar', [JugadorController::class, 'eliminarJugador']);
  Route::get('/admin/jornadas/{id}/eliminar', [PartidoController::class, 'eliminarJornada']);
  Route::get('/admin/partidos/{id}/eliminar', [PartidoController::class, 'eliminarPartido']);
  ```
  - **Vector de Ataque:** Un atacante puede alojar un elemento HTML (`<img src="http://mifantasy.test/admin/torneos/1/eliminar">` o `<iframe src="...">`) en un foro, correo o sitio externo. Si un administrador autenticado carga dicho elemento, el navegador enviará automáticamente la cookie de sesión y el recurso será destruido silenciosamente sin protección CSRF.
- **Solución Propuesta:**
  1. Cambiar todas las rutas de eliminación al verbo HTTP `DELETE`.
  2. Requerir token CSRF `@csrf` y directiva `@method('DELETE')` en los formularios Blade correspondientes.

```php
// Corrección en routes/web.php
Route::delete('/admin/torneos/{torneo}', [TorneoController::class, 'eliminarTorneo'])->name('admin.torneos.destroy');
Route::delete('/admin/equipos/{equipo}', [EquipoController::class, 'eliminarEquipo'])->name('admin.equipos.destroy');
Route::delete('/admin/jugadores/{jugador}', [JugadorController::class, 'eliminarJugador'])->name('admin.jugadores.destroy');
Route::delete('/admin/jornadas/{jornada}', [PartidoController::class, 'eliminarJornada'])->name('admin.jornadas.destroy');
Route::delete('/admin/partidos/{partido}', [PartidoController::class, 'eliminarPartido'])->name('admin.partidos.destroy');
```

---

### 2.2. Exposición de Rutas de Depuración y Reseed en Producción
- **Severidad:** 🔴 **CRÍTICO** (CWE-489 / CWE-200)
- **Ubicación:** `routes/web.php:22-57` (`/test` y `/poblar`)
- **Diagnóstico:**
  1. `/test`: Ejecuta `dd(session()->all())` de forma pública y no autenticada, filtrando identificadores de sesión, tokens CSRF y metadatos de usuarios.
  2. `/poblar`: Aunque valida `app()->isLocal()`, deshabilita las restricciones de integridad referencial (`SET FOREIGN_KEY_CHECKS=0;`) y ejecuta SQL masivo sin autenticación. En caso de una mala configuración del entorno (`APP_ENV=production` pero ejecutando `APP_DEBUG=true`), este endpoint puede destruir y sobrescribir toda la base de datos.
- **Solución Propuesta:**
  1. Eliminar permanentemente la ruta `/test`.
  2. Eliminar la ruta `/poblar` de `routes/web.php` y trasladar la carga de datos exclusivamente a un comando Artisan seguro (`php artisan db:seed --class=MiFantasyDatosSeeder`).

---

### 2.3. Broken Object-Level Authorization (IDOR / BOLA) en Consulta de Alineaciones y Jornadas
- **Severidad:** 🔴 **CRÍTICO** (CVSS 7.5 / CWE-639)
- **Ubicación:** 
  - `LiguillaController.php:390-437` (`alineacionUsuarioJornada`)
  - `PartidoController.php:73-82` (`guardarOrdenJornadas`)
- **Diagnóstico:**
  1. En `alineacionUsuarioJornada($liguilla, $user, $jornadaId)`: Cualquier usuario autenticado puede solicitar la alineación de cualquier otro usuario pasando IDs arbitrarios en la URL, sin validar si el usuario solicitante y el usuario solicitado pertenecen a dicha liguilla.
  2. En `guardarOrdenJornadas($request, $idTorneo)`: Se recibe un array JSON `ordenData` y se actualiza `Jornada::where('id', $item['id'])->update(...)` sin validar que las jornadas pertenezcan realmente al `$idTorneo` indicado en la ruta. Un administrador podría alterar inadvertidamente o maliciosamente el orden de jornadas de otros torneos.
- **Solución Propuesta:**
  Implementar **Laravel Authorization Policies** y scoping estricto en las consultas Eloquent:

```php
// Scoping estricto en PartidoController::guardarOrdenJornadas
public function guardarOrdenJornadas(Request $request, Torneo $torneo)
{
    $validated = $request->validate([
        'orden' => 'required|array',
        'orden.*.id' => 'required|integer',
        'orden.*.orden' => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($validated, $torneo) {
        $jornadaIdsTorneo = $torneo->jornadas()->pluck('id')->all();

        foreach ($validated['orden'] as $item) {
            if (in_array($item['id'], $jornadaIdsTorneo, true)) {
                $torneo->jornadas()->where('id', $item['id'])->update(['orden' => $item['orden']]);
            }
        }
    });

    return redirect()->back()->with('success', 'Orden de jornadas actualizado correctamente.');
}
```

---

### 2.4. Information Disclosure mediante Fuga de Excepciones SQL en Respuestas JSON
- **Severidad:** 🟠 **ALTO** (CWE-209)
- **Ubicación:** `AlineacionController.php:143-148` (`guardarAlineacion`)
- **Diagnóstico:**
  El bloque `catch` captura cualquier excepción del sistema y la devuelve íntegra en la respuesta JSON:
  ```php
  catch (Exception $e) {
      return response()->json([
          'status'  => 'error',
          'message' => 'Error al guardar la alineación: ' . $e->getMessage(),
      ], 500);
  }
  ```
  Esto expone nombres de tablas, estructura de columnas, sintaxis SQL interna y posibles rutas del servidor ante errores de base de datos o bloqueos.
- **Solución Propuesta:**
  Registrar la excepción con `Log::error()` y retornar un mensaje genérico y seguro al cliente:

```php
catch (\Throwable $e) {
    Log::error('Fallo al guardar alineación: ' . $e->getMessage(), [
        'user_id'     => Auth::id(),
        'liguilla_id' => $liguillaId,
        'trace'       => $e->getTraceAsString(),
    ]);

    return response()->json([
        'status'  => 'error',
        'message' => 'No se pudo guardar la alineación. Por favor, inténtelo de nuevo más tarde.',
    ], 500);
}
```

---

### 2.5. Subida Insegura de Archivos y Riesgo de Extensión Spoofing
- **Severidad:** 🟠 **ALTO** (CWE-434)
- **Ubicación:** `TorneoController.php:98`, `EquipoController.php:75`, `JugadorController.php:83`
- **Diagnóstico:**
  Se utiliza `$request->file('...')->getClientOriginalExtension()`, que obtiene la extensión suministrada por la cabecera enviada por el cliente (fácilmente falsificable), en lugar de `$request->file('...')->extension()` que infiere la extensión real inspeccionando el MIME-type del contenido en el servidor.
- **Solución Propuesta:**
  Utilizar validación estricta de extensiones y mimes (`image`, `mimes:jpg,jpeg,png,webp`), y delegar la generación segura del nombre a `Storage::disk('public')->putFile(...)` o `hashName()`.

---

### 2.6. Inconsistencia en la Invalidación de Sesión al Eliminar Perfil
- **Severidad:** 🟡 **MEDIO** (CWE-613)
- **Ubicación:** `PerfilController.php:108-115` (`eliminarPerfil`)
- **Diagnóstico:**
  Se ejecuta `Auth::logout()` y `$user->delete()`, pero no se invalida la sesión HTTP subyacente ni se regenera el token CSRF mediante `$request->session()->invalidate()` y `$request->session()->regenerateToken()`. Esto puede permitir la reutilización de sesiones en clientes compartidos.
- **Solución Propuesta:**
  ```php
  public function eliminarPerfil(Request $request): RedirectResponse
  {
      $user = $request->user();
      Auth::logout();
      $user->delete();

      $request->session()->invalidate();
      $request->session()->regenerateToken();

      return redirect('/')->with('success', 'Tu cuenta ha sido eliminada correctamente.');
  }
  ```

---

---

# 3. ⚙️ SECCIÓN: DEVOPS, INFRAESTRUCTURA Y QA
*Especialista: DevOps & QA Lead Senior*

### 3.1. Pipeline CI/CD Incompleto: Ausencia de Análisis Estático y Linters de Código
- **Severidad:** 🟡 **MEDIO**
- **Ubicación:** `.github/workflows/laravel.yml`
- **Diagnóstico:**
  El workflow actual de GitHub Actions únicamente ejecuta `php artisan test`. No cuenta con:
  1. Verificación de estilo de código con **Laravel Pint** (`./vendor/bin/pint --test`).
  2. Análisis estático de tipos con **PHPStan / Larastan** (`./vendor/bin/phpstan analyse --memory-limit=2G`).
  3. Auditoría de vulnerabilidades en paquetes Composer (`composer audit`).
- **Solución Propuesta:**
  Actualizar `.github/workflows/laravel.yml` para incluir las fases de Linting, Static Analysis y Security Scan antes de la ejecución de tests.

```yaml
# Pipeline CI/CD Mejorado (.github/workflows/laravel.yml)
name: Laravel CI / QA Quality Gate

on:
  push:
    branches: [ "main", "test" ]
  pull_request:
    branches: [ "main", "test" ]

jobs:
  code-quality:
    name: Code Quality & Security Gate
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, iconv, intl, pdo, pdo_mysql, bcmath, zip, redis
      - run: composer install --prefer-dist --no-progress
      - name: Security Audit
        run: composer audit
      - name: Code Style (Laravel Pint)
        run: ./vendor/bin/pint --test
      - name: Static Analysis (PHPStan)
        run: ./vendor/bin/phpstan analyse --level=5 --memory-limit=1G

  tests:
    name: Automated Test Suite
    needs: code-quality
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: mi_fantasy_test
          MYSQL_ROOT_PASSWORD: root
        ports: [ 3306:3306 ]
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3
      redis:
        image: redis:alpine
        ports: [ 6379:6379 ]
        options: --health-cmd="redis-cli ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, iconv, intl, pdo, pdo_mysql, bcmath, zip, redis
      - run: cp .env.example .env
      - run: composer install --prefer-dist --no-progress
      - run: php artisan key:generate
      - run: php artisan test --parallel
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: mi_fantasy_test
          DB_USERNAME: root
          DB_PASSWORD: root
          CACHE_STORE: array
          QUEUE_CONNECTION: sync
```

---

### 3.2. Discrepancia de Configuración de Entornos (`.env.example`)
- **Severidad:** 🟡 **MEDIO**
- **Ubicación:** `.env.example`
- **Diagnóstico:**
  `.env.example` define `DB_CONNECTION=sqlite`, `FILESYSTEM_DISK=local`, `CACHE_STORE=database`. En producción y en el flujo Multi-Tenant (`stancl/tenancy` + `spatie/laravel-permission`), se requiere MySQL/PostgreSQL, Redis y almacenamiento S3/MinIO. La falta de variables de referencia en `.env.example` induce a errores durante el aprovisionamiento de nuevos entornos.
- **Solución Propuesta:**
  Completar `.env.example` con las variables canónicas para producción SaaS:
  - `FILESYSTEM_DISK=s3`
  - `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET`, `AWS_ENDPOINT`
  - `CENTRAL_DOMAINS=mifantasy.com,app.mifantasy.com`
  - `TENANCY_AUTO_CREATE_DATABASES=false` (para multi-tenancy single-database / team scoped)

---

### 3.3. Rutas de Tenant y Aislamiento de Dominios Centrales
- **Severidad:** 🔵 **BAJO**
- **Ubicación:** `routes/tenant.php`
- **Diagnóstico:**
  `routes/tenant.php` contiene únicamente la ruta de prueba por defecto de Stancl Tenancy:
  ```php
  Route::get('/', function () {
      return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
  });
  ```
  No se han definido aún los grupos de rutas que operarán bajo subdominios de tenant frente a dominios centrales.
- **Solución Propuesta:**
  Mapear las rutas operativas de torneos privados y gestión de ligas de tenant dentro de `routes/tenant.php` o asegurar que el middleware `InitializeTenancyByDomain` esté configurado con fallback controlado a dominios centrales.

---

---

# 4. 🚀 PLAN DE ACCIÓN Y MATRIZ DE REMEDIACIÓN

| Fase | Tarea Prioritaria | Responsable | Estimación |
| :--- | :--- | :--- | :--- |
| **Fase 1: Hotfixes de Seguridad** | 1. Refactorizar todas las rutas `GET /eliminar` a `DELETE` con `@csrf`.<br>2. Eliminar rutas `/test` y `/poblar`.<br>3. Enmascarar mensajes de excepción SQL en `AlineacionController`. | Auditor de Seguridad | Inmediato (1 día) |
| **Fase 2: Arquitectura & Refactoring** | 1. Implementar `Storage::disk('public')` en todos los controladores.<br>2. Crear Form Requests y clases Action (`GuardarAlineacionAction`, `GenerarPlantillaAction`).<br>3. Limpiar comprobaciones redundantes de admin en favor de middleware/policies. | Arquitecto Clean Code | Corto Plazo (2 días) |
| **Fase 3: DevOps & QA Gate** | 1. Integrar Pint y PHPStan en GitHub Actions.<br>2. Completar `.env.example` con variables SaaS/S3.<br>3. Agregar tests de autorización para endpoints de liguilla y alineaciones. | DevOps & QA Lead | Medio Plazo (2 días) |

---
*Informe generado automáticamente por la Agencia de Agentes MiFantasy.*
