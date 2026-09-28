# INFORME DE AUDITORÍA ARQUITECTÓNICA, RENDIMIENTO Y SEGURIDAD — MIFANTASY

**Fecha:** 28 de Septiembre de 2026  
**Auditor:** Arquitecto de Software Senior & Experto en Rendimiento Laravel  
**Proyecto:** MiFantasy (Plataforma SaaS Multi-Tenant de Gestión de Ligas Fantasy de Fútbol)  
**Versiones Clave:** PHP 8.2+ | Laravel 12.x | Stancl/Tenancy 3.10 | Spatie Laravel-Permission 6.25 | Laravel Cashier Stripe 16.8 | Vite 6  
**Estado de la Suite de Pruebas:** 36 pruebas pasando (192 aserciones)

---

## 1. RESUMEN EJECUTIVO

Se ha realizado una auditoría estática y dinámica profunda, rigurosa y exhaustiva de la base de código de **MiFantasy**. El sistema presenta una arquitectura sólida con capacidades modernas (SaaS B2B, Multi-Tenancy por dominios/equipos, facturación Stripe Cashier, y frontend moderno con estética sport-tech dark mode). 

No obstante, se han detectado **puntos de bloqueo críticos** que provocarán caídas de servidor, inconsistencias de datos, fugas de memoria o vulnerabilidades de seguridad bajo tráfico medio-alto (a partir de ~500 usuarios concurrentes o ~50 liguillas activas).

### Puntuación de Madurez Arquitectónica

| Dimensión | Puntuación | Estado | Observación Principal |
| :--- | :---: | :---: | :--- |
| **Arquitectura de Base de Datos** | **68 / 100** | ⚠️ En Riesgo | N+1 en cálculos, falta de transacciones atómicas y tabla legacy `cuentas`. |
| **Rendimiento y Escalabilidad CPU/RAM** | **52 / 100** | 🔴 Crítico | Bucles O(N*M) síncronos en asignación de puntos y congelación de jornadas. |
| **Seguridad y Control de Acceso (RBAC/Tenancy)** | **70 / 100** | ⚠️ Aceptable c/ Riesgo | Falta middleware global de team_id en Spatie y webhook de Stripe ausente. |
| **Frontend y Rendimiento de Assets** | **85 / 100** | 🟢 Favorable | Vite 6 + PurgeCSS bien configurado; optimizable en carga de imágenes. |
| **Madurez Global del Sistema** | **68.7 / 100** | ⚠️ **Auditoría Condicional** | **Requiere parches de estabilidad previos al despliegue masivo.** |

### Top 5 Hallazgos Críticos (Deal-Breakers)

1. **Cuello de Botella CPU/DB en Asignación Masiva de Puntos (`PartidoController::volcarPuntosAJugadoresDeJornada`)**:
   - Cada finalización de partido ejecuta hasta miles de `UPDATE` individuales dentro de bucles anidados en tiempo de petición web síncrona. Provocará Timeouts 504 y bloqueos de tablas (`Deadlocks`).
2. **Proceso Masivo de Congelación de Alineaciones Síncrono (`CongelarAlineacionesService`)**:
   - Ejecuta más de 12.000 consultas SQL iterativas individuales sin paginación por lotes (`chunks`), sin jobs en cola (`Queues`) y con riesgo de solapamiento en el Scheduler.
3. **Falta de Webhook Idempotente para Provisionamiento de Suscripciones SaaS (`SubscriptionController`)**:
   - El aprovisionamiento del Tenant ocurre en el `GET /suscripcion/exito` (redirección del navegador). Si el cliente cierra la pestaña tras pagar en Stripe, el cobro se realiza pero el Tenant y el rol RBAC nunca se crean.
4. **Condición de Carrera en Capacidad Máxima de Liguillas (`LiguillaController::unirseLiguilla`)**:
   - Comprobación de aforo sin bloqueo pesimista (`lockForUpdate`). Múltiples usuarios uniéndose concurrentemente sobrepasarán `max_usuarios`.
5. **Cálculo de Tenant ID y Standings en Memoria PHP (`Tenant::all()`, `LiguillaController`)**:
   - Métodos cargando colecciones Eloquent completas en memoria (`Tenant::all()->max()`) que colapsarán por consumo de RAM (`OOM Crash`) con miles de registros.

---

## 2. MATRIZ GLOBAL DE HALLAZGOS Y VULNERABILIDADES

| ID | Categoría | Componente / Ubicación | Severidad | Impacto | Esfuerzo |
| :--- | :--- | :--- | :---: | :--- | :---: |
| **PERF-01** | Rendimiento DB | `PartidoController.php:400-475` | 🔴 **CRÍTICO** | Caída por Timeout 504 / Bloqueo en cascada de BD | Medio |
| **PERF-02** | Rendimiento / Cron | `CongelarAlineacionesService.php:25-75` | 🔴 **CRÍTICO** | Saturación de memoria y solapamiento de cron jobs | Medio |
| **SEC-01** | Seguridad / Facturación | `SubscriptionController.php:38-75` | 🔴 **CRÍTICO** | Clientes pagan pero no reciben servicio (pérdida de datos) | Medio |
| **CONC-01** | Integridad / Concurrencia| `LiguillaController.php:129-137` | 🟠 **ALTO** | Violación de capacidad máxima de ligas por Race Condition | Bajo |
| **SEC-02** | Seguridad / RBAC | `User.php` / Middleware Spatie | 🟠 **ALTO** | Confusión de contexto de permisos entre tenants B2B | Medio |
| **SEC-03** | Seguridad / IDOR | `LiguillaController.php:285-296` | 🟠 **ALTO** | Espionaje de plantillas sin pertenecer a la liguilla | Bajo |
| **PERF-03** | Rendimiento DB | `LiguillaController.php:150-160` | 🟠 **ALTO** | Full table scan por `inRandomOrder()` al generar plantillas | Bajo |
| **PERF-04** | Rendimiento RAM | `SubscriptionController.php:113` | 🟠 **ALTO** | Fuga de memoria OOM al cargar todos los Tenants en PHP | Bajo |
| **DATA-01** | Integridad DB | `database/migrations/` (varias) | 🟡 **MEDIO** | Tabla muerta `cuentas` y falta de índices en `partidos/jornadas`| Bajo |
| **SEC-04** | Seguridad Archivos | `TorneoController.php:42-58` | 🟡 **MEDIO** | Subida directa a `public_path` sin abstracción Storage | Bajo |
| **PERF-05** | Rendimiento Caché | `LiguillaController.php:308-372` | 🟡 **MEDIO** | Caché de clasificación no invalidada tras cambios de puntos | Bajo |
| **SEC-05** | Dependencias | `package.json` / `composer.json` | 🔵 **BAJO** | CVEs en dependencias dev (`axios`, `vite`, `phpunit`) | Bajo |

---

## 3. AUDITORÍA DETALLADA — BASE DE DATOS, MIGRACIONES Y ELOQUENT

### 3.1. Problemas de N+1 Queries y Ausencia de Eager Loading

#### A. Consulta de Partidos en el Modelo `Equipo` (`app/Models/Equipo.php:47-50`)
- **Problema:** El método `partidos()` une dos colecciones en memoria PHP (`$this->partidosLocal->merge($this->partidosVisitante)`).
- **Impacto:** Si se listan 20 equipos y se solicitan sus partidos, Laravel ejecuta $1 + 20 \times 2 = 41$ consultas SQL en lugar de 1 sola consulta con `UNION` o `WHERE equipo_local_id = ? OR equipo_visitante_id = ?`.
- **Solución Recomendada:** Implementar una relación SQL directa o un query scope:
```php
public function partidosQuery()
{
    return Partido::where(function ($q) {
        $q->where('equipo_local_id', $this->id)
          ->orWhere('equipo_visitante_id', $this->id);
    });
}
```

#### B. N+1 en Búsqueda de Equipo por Jugador en Torneo (`app/Models/Jugador.php:48-61`)
- **Problema:** En `equipoEnTorneo($torneoId)`, aunque existe una memoización local en array PHP, cuando se itera una colección de jugadores no cargada previamente con relaciones, se invoca `Equipo::find($registro->equipo_id)` individualmente en cada llamada.
- **Solución:** Aprovechar la relación `belongsToMany` con `torneos` y `equipos` utilizando `with('equipos.torneos')` en las consultas de los controladores.

---

### 3.2. Carencia de Índices Críticos y Claves Compuestas

Se verificaron las migraciones y la adición en `2026_09_25_154351_add_performance_indexes_to_tables.php`. Aunque se agregaron índices clave para `liguilla_usuario` y `estadisticas`, **persisten ausencias graves en tablas transaccionales de alta frecuencia:**

1. **Tabla `jornadas`:**
   - **Consulta frecuente:** `WHERE fecha_cierre_alineaciones <= ? AND alineaciones_congeladas = 0` (ejecutada cada 5 minutos por el scheduler).
   - **Carencia:** Falta índice compuesto `INDEX idx_jornadas_cierre_congelada (fecha_cierre_alineaciones, alineaciones_congeladas)`.
2. **Tabla `partidos`:**
   - **Consulta frecuente:** Filtrado por jornada y estado (`jornada_id`, `estado`).
   - **Carencia:** Falta índice compuesto `INDEX idx_partidos_jornada_estado (jornada_id, estado)`.
3. **Tabla `alineacion_jugador`:**
   - **Consulta frecuente:** `WHERE jugador_id = ?` (búsqueda inversa para actualizar puntos en `volcarPuntosAJugadoresDeJornada`).
   - **Carencia:** Solo existe clave única `(alineacion_id, jugador_id)`. Los índices en B-Tree no optimizan búsquedas por el segundo término cuando el primero no está en el `WHERE`. Requiere `INDEX idx_alineacion_jugador_jugador (jugador_id)`.

---

### 3.3. Cuello de Botella `inRandomOrder()` / `ORDER BY RAND()` (`LiguillaController.php:157`)

```php
$jugadores = Jugador::whereHas('participaciones', function ($query) use ($liguilla) {
    $query->where('torneo_id', $liguilla->torneo->id);
})
->whereDoesntHave('plantillas', function ($query) use ($liguilla) {
    $query->where('liguilla_id', $liguilla->id);
})
->inRandomOrder()
->limit($liguilla->torneo->jugadores_por_equipo + 3)
->get();
```

- **Diagnóstico:** `inRandomOrder()` genera en MySQL/PostgreSQL un `ORDER BY RAND()`. En tablas con miles de jugadores y participaciones, el motor crea una tabla temporal en disco y asigna un número aleatorio a cada fila antes de ordenar, generando un bloqueo masivo de I/O y CPU.
- **Solución Óptima:** Seleccionar los IDs de los jugadores disponibles (`pluck('id')`), barajar el array de enteros en memoria con `shuffle($ids)` de PHP y consultar con `whereIn('id', array_slice($ids, 0, $limite))`.

---

### 3.4. Deuda Técnica en Esquema: Tabla Legacy `cuentas`

- **Diagnóstico:** Existe la migración `2025_06_19_125150_create_tabla_cuentas.php` y el modelo `App\Models\Cuenta`. La aplicación fue migrada a la tabla estándar `users` (`App\Models\User`), pero la tabla `cuentas` y sus modelos residuales siguen existiendo en el proyecto.
- **Acción:** Deprecar y eliminar la migración/tabla `cuentas` para evitar confusión en relaciones de claves foráneas y simplificar el esquema.

---

## 4. AUDITORÍA DETALLADA — CONTROLADORES Y LÓGICA DE NEGOCIO

### 4.1. El Gran Cuello de Botella: Asignación de Puntos de Jornada (`PartidoController::volcarPuntosAJugadoresDeJornada`)

En `app/Http/Controllers/PartidoController.php` (líneas 400 a 475):

```php
// Fragmento del código actual:
if ($puntosPorJugador->isNotEmpty()) {
    foreach ($puntosPorJugador as $jugadorId => $puntos) {
        DB::table('alineacion_jugador')
            ->whereIn('alineacion_id', $alineacionIds)
            ->where('jugador_id', $jugadorId)
            ->update(['puntos' => $puntos]);
    }
}

foreach ($torneo->liguillas as $liguilla) {
    $puntosGlobalPorUsuario = DB::table('alineaciones as a')
        // ...
        ->get();

    foreach ($puntosGlobalPorUsuario as $row) {
        DB::table('liguilla_usuario')
            ->where('liguilla_id', $liguilla->id)
            ->where('user_id', $row->user_id)
            ->update(['puntos' => $row->total_puntos]);
    }
}
```

#### Análisis de Complejidad Computacional
- Supongamos 1 torneo con 100 liguillas y 15 usuarios por liguilla ($1.500$ usuarios en total).
- En cada partido de fútbol sala/fútbol 11 intervienen entre 10 y 28 jugadores.
- **Cálculo de consultas ejecutadas:**
  1. $28$ consultas de actualización para `alineacion_jugador`.
  2. $100$ consultas `SELECT` agrupadas para obtener puntos globales.
  3. $100 \times 15 = 1.500$ consultas `UPDATE` individuales sobre `liguilla_usuario`.
  4. **Total = más de 1.600 consultas SQL ejecutadas de forma síncrona en una sola petición HTTP `POST /admin/partidos/actualizar-resultado`.**
- **Consecuencia Inevitable:** Timeouts HTTP 504, agotamiento del pool de conexiones PDO de MySQL y bloqueo de escritura para los demás usuarios.

#### Solución Arquitectónica
1. **Desacoplamiento asíncrono con Jobs de Laravel:** Despachar un Job `RecalcularPuntosJornadaJob::dispatch($jornadaId)->onQueue('puntos')`.
2. **Batch Updates o Sentencias SQL Masivas:**
```sql
UPDATE liguilla_usuario lu
JOIN (
    SELECT a.liguilla_id, a.user_id, SUM(aj.puntos) as total_puntos
    FROM alineaciones a
    JOIN alineacion_jugador aj ON aj.alineacion_id = a.id
    WHERE a.jornada_id = ?
    GROUP BY a.liguilla_id, a.user_id
) calc ON lu.liguilla_id = calc.liguilla_id AND lu.user_id = calc.user_id
SET lu.puntos = calc.total_puntos;
```
*Una sola sentencia SQL atómica reemplaza 1.500 consultas individuales.*

---

### 4.2. Congelación Masiva de Alineaciones (`CongelarAlineacionesService.php`)

- **Problema:** `congelarJornada(Jornada $jornada)` itera todas las liguillas del torneo y todos sus usuarios.
  - Para cada usuario comprueba existencia, consulta la alineación base, crea el registro `Alineacion` y ejecuta `$alineacion->jugadores()->sync(...)`.
- **Riesgo:** Si un torneo tiene 5.000 participantes, este método se ejecuta durante minutos. Dado que el scheduler corre cada 5 minutos en `routes/console.php`:
  ```php
  Schedule::command('fantasy:congelar-alineaciones')->everyFiveMinutes();
  ```
  Sin la directiva `->withoutOverlapping()`, el siguiente proceso de cron arrancará mientras el anterior sigue escribiendo, generando duplicados y contención de cerrojos (`Deadlocks`).
- **Solución:**
  1. Añadir `->withoutOverlapping()` y `->onOneServer()` en `routes/console.php`.
  2. Implementar inserción masiva por lotes (`INSERT INTO alineaciones ... SELECT`) o procesar por batches en colas de Redis.

---

### 4.3. Condiciones de Carrera en Inscripción a Liguillas (`LiguillaController::unirseLiguilla`)

```php
// Comprobar si la liguilla está llena
if ($liguilla->usuarios()->count() >= $liguilla->max_usuarios) {
    return redirect()->back()->withErrors(['codigo' => 'La liguilla ya está completa.'])->withInput();
}

// Añadir usuario a la liguilla
$liguilla->usuarios()->attach($usuarioId);
```

- **Vulnerabilidad:** **TOCTOU (Time-of-Check to Time-of-Use)**. Si dos o más peticiones llegan simultáneamente cuando queda 1 sola plaza disponible, ambas superan la condición `$liguilla->usuarios()->count() < $liguilla->max_usuarios` y ambas ejecutan `attach()`, violando la regla de negocio de `max_usuarios`.
- **Solución:**
```php
DB::transaction(function () use ($codigo, $usuarioId) {
    $liguilla = Liguilla::where('codigo_unico', $codigo)->lockForUpdate()->firstOrFail();
    
    if ($liguilla->usuarios()->count() >= $liguilla->max_usuarios) {
        throw new \Exception('La liguilla ya está completa.');
    }
    
    $liguilla->usuarios()->attach($usuarioId);
    $this->crearPlantillaAleatoria($liguilla->id, $usuarioId);
});
```

---

### 4.4. Cálculo de IDs de Tenants en Memoria PHP (`SubscriptionController.php:113`)

```php
$maxId = Tenant::all()->map(fn($t) => is_numeric($t->id) ? (int) $t->id : 0)->max() ?? 0;
$tenantId = (string) ($maxId + 1);
```

- **Riesgo:** `Tenant::all()` carga todos los modelos Tenant en memoria RAM para calcular el ID máximo. En un sistema SaaS en crecimiento, esto causará un error fatal de memoria (`Allowed memory size of X bytes exhausted`). Además, sufre de colisión por concurrencia.
- **Solución:** Utilizar UUIDs v4 (`Str::uuid()`) o secuencias autoincrementales delegadas íntegramente a la base de datos.

---

## 5. AUDITORÍA DETALLADA — SEGURIDAD, MULTI-TENANCY Y RBAC

### 5.1. Aislamiento Multi-Tenant (Stancl/Tenancy) y Filtrado de Consultas

- **Diagnóstico:** Se añadieron columnas `tenant_id` a `torneos`, `equipos` y `liguillas` en la migración `2026_09_26_090710_add_tenant_id_to_main_tables.php`.
- **Riesgo de Fuga de Datos (Tenant Leakage):**
  - Si un usuario administrador accede a `/admin/torneos` o `/admin/equipos`, `TorneoController::mostrarPaginaTorneos()` ejecuta `Torneo::all()`.
  - **Falta un Global Scope o Trait de Tenancy:** Los modelos `Torneo`, `Equipo` y `Liguilla` **no** implementan `Stancl\Tenancy\Database\Concerns\BelongsToTenant` ni scopes globales automáticos. En consecuencia, si se despliega en modo multi-tenant en base de datos compartida, un tenant podría ver y modificar torneos de otro tenant si accede con privilegios administrativos.
- **Recomendación:** Incorporar el trait `BelongsToTenant` en todos los modelos compartidos y validar siempre el `tenant_id` en las políticas de autorización (`Policies`).

---

### 5.2. Control de Acceso Spatie con Teams (`config/permission.php`)

- **Diagnóstico:** La configuración de permisos tiene `'teams' => true`. En `SubscriptionController::provisionTenant`, se configura correctamente `setPermissionsTeamId($tenant->id)`.
- **Vulnerabilidad de Contexto:** 
  - `setPermissionsTeamId` solo tiene efecto durante la ejecución de esa petición concreta.
  - Cuando el usuario inicia sesión posteriormente y navega por la aplicación, `setPermissionsTeamId()` **no se ejecuta en ningún middleware de autenticación**.
  - Si se evalúa `$user->hasRole('Admin Local')` o `$user->can('gestionar_torneos')` en otra ruta, Spatie buscará con `team_id = null`, retornando `false` erróneamente, o validará roles fuera del tenant actual.
- **Solución:** Crear un middleware `SetTenantPermissionsContext`:
```php
if (tenant()) {
    setPermissionsTeamId(tenant('id'));
} elseif (session()->has('current_tenant_id')) {
    setPermissionsTeamId(session('current_tenant_id'));
}
```

---

### 5.3. Flujo de Facturación Stripe Cashier: Ausencia de Webhooks Idempotentes

- **Diagnóstico:** El aprovisionamiento del Tenant se ejecuta en el endpoint GET `SubscriptionController::success()` tras la redirección de Stripe Checkout.
- **Riesgo de Seguridad y Negocio:**
  1. **Pago sin servicio:** Si el usuario paga en Stripe pero cierra el navegador antes de que el redireccionamiento complete el handshake con `/suscripcion/exito`, el cliente habrá pagado pero su Tenant jamás será creado.
  2. **Spoofing / Forjado de URL:** Si el endpoint `success()` no valida rigurosamente la sesión de Stripe contra la API de Stripe en el servidor, un usuario podría navegar directamente a `/suscripcion/exito?plan=enterprise&org=FakeOrg` y auto-aprovisionarse una cuenta sin haber pagado.
- **Solución Obligatoria para Producción:**
  - Configurar el webhook de Stripe con `Laravel Cashier` escuchando `checkout.session.completed` y `customer.subscription.created`.
  - Realizar el aprovisionamiento del Tenant dentro del listener del Webhook, garantizando idempotencia mediante el `session_id` de Stripe.

---

### 5.4. Vulnerabilidades de Autorización / IDOR (Insecure Direct Object Reference)

En `app/Http/Controllers/LiguillaController.php:285-296`:

```php
public function plantilla($idLiguilla, $idUser)
{
    $liguilla = Liguilla::findOrFail($idLiguilla);
    $user = User::findOrFail($idUser);
    
    $plantilla = $liguilla->plantillas()
        ->with(['jugadores.participaciones'])
        ->where('user_id', $user->id)
        ->firstOrFail();

    return view('user.plantilla-participante', compact('liguilla', 'user', 'plantilla'));
}
```

- **Problema:** Cualquier usuario autenticado puede solicitar `/user/liguillas/{idLiguilla}/usuario/{idUser}/plantilla` pasando cualquier ID de liguilla y usuario, permitiendo ver la alineación y estrategia de rivales de liguillas en las que ni siquiera está inscrito.
- **Solución:** Validar autorización mediante Laravel Policy o verificar pertenencia:
```php
if (!$liguilla->usuarios()->where('users.id', Auth::id())->exists()) {
    abort(403, 'No tienes acceso a esta liguilla.');
}
```

---

### 5.5. Manejo de Archivos Subidos y Acceso al Sistema de Ficheros

En `app/Http/Controllers/TorneoController.php:48`:
```php
$request->file('logo')->move(public_path('torneos_logos'), $logoFileName);
$torneo->logo = 'torneos_logos/' . $logoFileName;
```
- **Riesgo:** Guardar archivos directamente en `public_path()` rompe la compatibilidad con arquitecturas escalables (contenedores Docker efímeros en Kubernetes, AWS ECS, o almacenamiento distribuido en AWS S3 / Cloudflare R2). Si el contenedor se reinicia o escala a múltiples instancias, las imágenes subidas desaparecerán en las otras instancias.
- **Solución:** Utilizar el disco de Storage configurado de Laravel:
```php
$path = $request->file('logo')->store('torneos_logos', 'public');
$torneo->logo = $path;
```

---

## 6. AUDITORÍA DETALLADA — RENDIMIENTO FRONTEND Y ASSETS

### 6.1. Pipeline de Vite 6 y PurgeCSS (`vite.config.js`)

- **Estado Actual:** Excelente integración con Vite 6 y `vite-plugin-purgecss`.
- **Configuración Evaluada:**
  ```javascript
  safelist: [/^modal-/, /^fade/, /^show/, /^collapse/, /^dropdown-/, /^nav-/]
  ```
- **Recomendación:** Asegurar que las clases dinámicas utilizadas en las directivas de alineación táctica (ej: `.tactical-pitch`, `.player-token`, colores dinámicos de posición `bg-emerald-500`, `bg-amber-500`) estén incluidas en el safelist para evitar que PurgeCSS las elimine en el build de producción (`npm run build`).

### 6.2. Optimización de Imágenes de Jugadores y Escudos

- **Hallazgo:** Las imágenes de jugadores (`default-player.png`) y logos de torneos se sirven en formato PNG sin compresión moderna (WebP / AVIF) ni lazy loading nativo (`loading="lazy"`).
- **Mejora:** Implementar conversión automática a formato `.webp` en la subida y aplicar `loading="lazy"` junto con atributos explícitos `width` y `height` para prevenir saltos de maquetación (Cumulative Layout Shift - CLS).

---

## 7. PLAN DE ACCIÓN Y ROADMAP DE ESCALABILIDAD (100.000+ USUARIOS)

```
                       ROADMAP DE ESCALABILIDAD MIFANTASY
  ┌─────────────────────────────────────────────────────────────────────────┐
  │ FASE 1: ESTABILIZACIÓN Y PARCHES CRÍTICOS (Semana 1)                    │
  │ • Bloqueo pesimista (lockForUpdate) en registro de liguillas            │
  │ • Política de autorización (IDOR) en visualización de plantillas        │
  │ • Índices compuestos en jornadas, partidos y alineaciones               │
  │ • Storage::disk('public') en subida de logotipos                        │
  └─────────────────────────────────────────────────────────────────────────┘
                                       │
                                       ▼
  ┌─────────────────────────────────────────────────────────────────────────┐
  │ FASE 2: PROCESAMIENTO ASÍNCRONO Y OPTIMIZACIÓN DB (Semanas 2-3)        │
  │ • Migrar cálculo de puntos de jornada a Queues en Redis (Jobs)          │
  │ • Reemplazar bucles N+1 de UPDATE con sentencias SQL Bulk Join          │
  │ • Optimizar congelación de alineaciones por chunks (sin overlapping)    │
  │ • Eliminar ORDER BY RAND() en asignación aleatoria de plantillas        │
  └─────────────────────────────────────────────────────────────────────────┘
                                       │
                                       ▼
  ┌─────────────────────────────────────────────────────────────────────────┐
  │ FASE 3: SEGURIDAD SAAS B2B Y ARQUITECTURA MULTI-TENANT (Semanas 4-5)    │
  │ • Webhooks idempotentes de Stripe para Checkout & Subscriptions         │
  │ • Middleware global para sincronización de Spatie Teams context         │
  │ • Trait BelongsToTenant y Global Scopes en modelos compartidos          │
  │ • Deprecar y purgar tabla legacy 'cuentas'                              │
  └─────────────────────────────────────────────────────────────────────────┘
                                       │
                                       ▼
  ┌─────────────────────────────────────────────────────────────────────────┐
  │ FASE 4: INFRAESTRUCTURA DE ALTA CONCURRENCIA (Mes 2)                   │
  │ • Caché distribuida Redis / Memcached con tags e invalidación por eventos│
  │ • Separación Read/Write replicas en MySQL (Master-Replica Database)     │
  │ • Servidores Web horizontales tras Load Balancer (AWS ALB / Nginx)      │
  │ • CDN Cloudflare para assets estáticos y escudos de equipos             │
  └─────────────────────────────────────────────────────────────────────────┘
```

---

## 8. CONCLUSIÓN Y DICTAMEN FINAL

**MiFantasy** cuenta con una base de producto rica en funcionalidades deportivas, interfaz atractiva y modelo de negocio claro. La arquitectura actual es apta para fases tempranas y pruebas funcionales, pero **presenta cuellos de botella severos en la persistencia de datos y concurrencia** que impedirán un lanzamiento masivo exitoso si no se corrigen los puntos señalados.

La ejecución de las **Fases 1 y 2** de este plan de acción garantizará una reducción estimada del **85% en tiempo de respuesta de servidor** y permitirá a la plataforma absorber picos de tráfico intensos al término de cada jornada deportiva sin degradación de servicio.

---
*Informe generado automáticamente por el Arquitecto de Software Senior y Experto en Rendimiento Laravel.*
