@extends('user.layouts.app')

@section('title', $liguilla->nombre . ' - MiFantasy')

@section('content')
@php
    $modalidad = strtolower($liguilla->torneo->modalidad ?? 'f11');
    $limiteSlots = match($modalidad) {
        'sala' => 5,
        'f7' => 7,
        default => 11,
    };
    $esSala = ($modalidad === 'sala');
@endphp
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-4 sm:space-y-6">

    <!-- Header Bento Acordeón Ultra Fino (Above the fold) -->
    <details class="group relative overflow-hidden bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-zinc-950/60 transition-all duration-300">
        <!-- Barra Fina Cerrada (Summary) -->
        <summary class="flex items-center justify-between py-2 px-4 cursor-pointer list-none select-none hover:bg-zinc-800/40 transition-colors">
            <!-- Izquierda: Nombre de la liga + Badge torneo -->
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-2 h-2 rounded-full bg-lime-400 shrink-0 animate-pulse"></span>
                <h1 class="text-sm sm:text-base font-extrabold tracking-tight text-zinc-100 truncate">
                    {{ $liguilla->nombre }}
                </h1>
                <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-[10px] font-semibold shrink-0">
                    <i class="bi bi-shield-shaded"></i>
                    <span>{{ $liguilla->torneo->nombre }}</span>
                </span>
            </div>

            <!-- Derecha: Código + Botón Mis Ligas + Chevron -->
            <div class="flex items-center gap-2 shrink-0">
                <button
                    type="button"
                    onclick="event.stopPropagation(); compartirEnlace('{{ $liguilla->codigo_unico }}')"
                    class="py-1 px-2.5 bg-zinc-950/80 hover:bg-zinc-800 text-zinc-200 hover:text-zinc-100 font-mono font-bold text-[11px] rounded-lg border border-zinc-800 hover:border-zinc-700 transition-all flex items-center gap-1.5 shadow-sm group/btn cursor-pointer"
                    title="Copiar código">
                    <span class="hidden sm:inline text-zinc-400 font-sans font-medium text-[10px] uppercase tracking-wider">Código:</span>
                    <span class="text-lime-400 tracking-wider text-xs">{{ $liguilla->codigo_unico }}</span>
                    <i class="bi bi-share text-zinc-400 group-hover/btn:text-lime-400 transition-colors text-[10px]"></i>
                </button>

                <a
                    href="{{ url('/user/liguillas') }}"
                    onclick="event.stopPropagation();"
                    class="py-1 px-2.5 bg-zinc-800/80 hover:bg-zinc-800 text-zinc-300 hover:text-zinc-100 font-semibold text-[11px] rounded-lg border border-zinc-700/80 transition-all hidden sm:flex items-center gap-1"
                    title="Mis Ligas">
                    <i class="bi bi-arrow-left"></i>
                    <span>Mis Ligas</span>
                </a>

                <!-- Icono Chevron Abajo / Arriba -->
                <div class="p-1 text-zinc-400 group-open:rotate-180 transition-transform duration-300 flex items-center justify-center">
                    <i class="bi bi-chevron-down text-xs"></i>
                </div>
            </div>
        </summary>

        <!-- Contenido Expandido (Detalles completos de la liguilla) -->
        <div class="border-t border-zinc-800/80 p-4 sm:p-6 bg-zinc-950/40 relative z-10">
            <!-- Glow ambiental decorativo -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-lime-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-5 min-w-0">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl bg-zinc-950/80 border border-zinc-800/80 p-1.5 flex items-center justify-center shrink-0 shadow-lg">
                        @if(!empty($liguilla->torneo->logo))
                        <img
                            src="{{ asset($liguilla->torneo->logo) }}"
                            alt="{{ $liguilla->torneo->nombre }}"
                            class="max-h-full max-w-full object-contain filter drop-shadow-md"
                            loading="lazy"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden text-amber-400 text-2xl items-center justify-center">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        @else
                        <div class="text-amber-400 text-2xl flex items-center justify-center">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-1 min-w-0">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-xs font-semibold">
                            <i class="bi bi-shield-shaded"></i>
                            <span class="truncate">{{ $liguilla->torneo->nombre }}</span>
                        </div>
                        <p class="text-xs text-zinc-400 flex items-center gap-2">
                            <span>Creada por <strong class="text-zinc-200">{{ $liguilla->creador->name ?? 'Admin' }}</strong></span>
                            <span class="text-zinc-600">•</span>
                            <span>{{ $liguilla->usuarios?->count() ?? 0 }} managers</span>
                        </p>
                    </div>
                </div>

                <div class="sm:hidden w-full pt-2 border-t border-zinc-800/60 flex items-center justify-between text-xs text-zinc-400">
                    <a href="{{ url('/user/liguillas') }}" class="text-lime-400 font-semibold flex items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Mis Ligas
                    </a>
                </div>
            </div>
        </div>
    </details>

    <!-- Navegación por Pestañas (Pill Tabs Minimalistas) -->
    <div class="flex items-center gap-1.5 sm:gap-2 p-1.5 bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl overflow-x-auto scrollbar-none shadow-lg">
        <button
            type="button"
            onclick="switchTab('alineacion')"
            id="tab-btn-alineacion"
            class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap bg-zinc-800 text-lime-400 border border-zinc-700/60 shadow-sm cursor-pointer">
            <i class="bi bi-diagram-3"></i>
            <span>Alineación</span>
        </button>

        <button
            type="button"
            onclick="switchTab('plantilla')"
            id="tab-btn-plantilla"
            class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50 cursor-pointer">
            <i class="bi bi-people"></i>
            <span>Mi Plantilla</span>
            <span class="px-1.5 py-0.2 rounded-full bg-zinc-800 text-[10px] text-zinc-300">{{ $plantillaUsuario?->count() ?? 0 }}</span>
        </button>

        <button
            type="button"
            onclick="switchTab('clasificacion')"
            id="tab-btn-clasificacion"
            class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50 cursor-pointer">
            <i class="bi bi-trophy"></i>
            <span>Clasificación</span>
        </button>

        <button
            type="button"
            onclick="switchTab('jornadas')"
            id="tab-btn-jornadas"
            class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50 cursor-pointer">
            <i class="bi bi-calendar-check"></i>
            <span>Mis Jornadas</span>
        </button>

        <button
            type="button"
            onclick="switchTab('resultados')"
            id="tab-btn-resultados"
            class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50 cursor-pointer">
            <i class="bi bi-card-checklist"></i>
            <span>Resultados</span>
        </button>

        <button
            type="button"
            onclick="switchTab('participantes')"
            id="tab-btn-participantes"
            class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50 cursor-pointer">
            <i class="bi bi-person-lines-fill"></i>
            <span>Participantes</span>
            <span class="px-1.5 py-0.2 rounded-full bg-zinc-800 text-[10px] text-zinc-300">{{ $liguilla->usuarios?->count() ?? 0 }}</span>
        </button>
    </div>

    <!-- Contenido de las Pestañas -->
    <div class="space-y-6">

        <!-- ==========================================
             TAB 1: ALINEACIÓN / CAMPO TÁCTICO
        ========================================== -->
        <div id="tab-pane-alineacion" class="tab-pane space-y-6">

            <!-- Panel de Control Táctico Compacto -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-xl p-3 mb-4 flex flex-col gap-3 shadow-sm mx-auto w-full">
                <!-- Fila superior: Esquema + Jornada -->
                <div class="flex justify-between items-center w-full">
                    <div class="flex items-center gap-2">
                        <label for="selectFormacion" class="text-[10px] sm:text-xs text-zinc-400 font-bold uppercase tracking-wider mb-0">Esquema:</label>
                        <select name="formacion" id="selectFormacion" class="py-1 px-2 text-xs rounded-md bg-zinc-950 border border-zinc-800 text-zinc-200 outline-none focus:border-lime-400 transition-colors cursor-pointer">
                            @foreach($formaciones as $clave => $nombre)
                            <option value="{{ $clave }}" {{ ($formacionActual ?? '4-3-3') == $clave ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-[10px] sm:text-xs px-2.5 py-1 rounded-md bg-zinc-800/80 text-zinc-300 inline-flex items-center gap-1.5 whitespace-nowrap">
                        <i class="bi bi-clock"></i>
                        @if(isset($proximaJornada) && $proximaJornada)
                        <span>{{ $proximaJornada->nombre ?? ('Jornada ' . ($proximaJornada->orden ?? '')) }}</span>
                        @if($proximaJornada->fecha_inicio)
                        <span class="text-zinc-500">• {{ \Carbon\Carbon::parse($proximaJornada->fecha_inicio)->format('d/m/Y H:i') }}</span>
                        @endif
                        @else
                        <span>Jornada actual</span>
                        @endif
                    </div>
                </div>

                <!-- Botón Guardar -->
                <button type="button" id="btnGuardarAlineacion" class="w-full py-2 bg-lime-400 hover:bg-lime-500 text-zinc-950 text-sm font-bold rounded-lg transition-colors flex justify-center items-center gap-2 border-none shadow-sm cursor-pointer">
                    <i class="bi bi-check-circle-fill text-base"></i> Guardar alineación
                </button>
            </div>

            <!-- Contenedor del Campo de Fútbol Táctico Bento -->
            <div class="relative bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-3xl p-4 sm:p-8 shadow-2xl shadow-zinc-950/50">

                <div class="campo-futbol-wrapper max-w-4xl mx-auto">
                    <!-- Césped con líneas tácticas -->
                    <div class="campo-futbol relative rounded-2xl overflow-hidden min-h-[580px] sm:min-h-[640px] flex flex-col justify-between py-6 px-3 sm:px-6 shadow-inner {{ $esSala ? 'bg-amber-950/40 border border-amber-800/50 ring-1 ring-amber-900/20' : 'border border-emerald-500/20 bg-gradient-to-b from-emerald-950/90 via-emerald-900/40 to-emerald-950/90' }}">

                        <!-- SVG de líneas de campo futbolístico / polideportivo -->
                        <div class="absolute inset-0 pointer-events-none opacity-25">
                            <!-- Borde exterior -->
                            <div class="absolute inset-3 border-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }} rounded-xl"></div>
                            <!-- Línea de medio campo -->
                            <div class="absolute inset-x-3 top-1/2 -translate-y-1/2 border-t-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }}"></div>
                            <!-- Círculo central -->
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-28 h-28 sm:w-36 sm:h-36 border-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }} rounded-full"></div>
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-2 h-2 {{ $esSala ? 'bg-amber-400' : 'bg-emerald-400' }} rounded-full"></div>
                            <!-- Área superior (Rival) -->
                            <div class="absolute top-3 left-1/2 -translate-x-1/2 w-44 sm:w-56 h-20 sm:h-24 border-b-2 border-x-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }} rounded-b-lg"></div>
                            <!-- Área inferior (Propia) -->
                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 w-44 sm:w-56 h-20 sm:h-24 border-t-2 border-x-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }} rounded-t-lg"></div>
                        </div>

                        <!-- 4 Filas Tácticas de Jugadores -->
                        <!-- 1. Delanteros -->
                        <div id="row-delanteros" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                            <!-- Inyectado dinámicamente -->
                        </div>

                        <!-- 2. Centrocampistas -->
                        <div id="row-centrocampistas" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                            <!-- Inyectado dinámicamente -->
                        </div>

                        <!-- 3. Defensas -->
                        <div id="row-defensas" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                            <!-- Inyectado dinámicamente -->
                        </div>

                        <!-- 4. Portero -->
                        <div id="row-portero" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                            <!-- Inyectado dinámicamente -->
                        </div>

                    </div>
                </div>

                <!-- Formulario Oculto para sincronizar slots -->
                <form id="formAlineacion" method="POST" action="{{ url('/user/liguillas/'.$liguilla->id.'/alineacion/guardar') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="formacion" id="hiddenFormacion" value="{{ $formacionActual ?? '4-3-3' }}">
                    <div id="hiddenInputsContainer"></div>
                </form>

            </div>

        </div>

        <!-- ==========================================
             TAB 2: MI PLANTILLA
        ========================================== -->
        <div id="tab-pane-plantilla" class="tab-pane hidden space-y-6">

            <div class="flex items-center justify-between mb-4 px-1">
                <h2 class="text-lg font-extrabold text-zinc-100">Mi Plantilla</h2>
                <span class="bg-lime-400/10 border border-lime-400/20 text-lime-400 text-xs px-2.5 py-1 rounded-lg font-bold">Total: {{ $plantillaUsuario?->count() ?? 0 }} jugadores</span>
            </div>

            <!-- Grupos de Posiciones -->
            @php
                $posicionesOrden = ['portero', 'defensa', 'centrocampista', 'delantero'];
                $plantillaAgrupada = $plantillaUsuario ? $plantillaUsuario->groupBy(function($j) {
                    return strtolower($j->posicion ?? '');
                })->sortKeysUsing(function($a, $b) use ($posicionesOrden) {
                    return array_search($a, $posicionesOrden) <=> array_search($b, $posicionesOrden);
                }) : collect();

                $posicionesMeta = [
                    'portero' => ['titulo' => 'Porteros', 'icono' => 'bi-shield-shaded', 'color' => 'amber'],
                    'defensa' => ['titulo' => 'Defensas', 'icono' => 'bi-shield-check', 'color' => 'blue'],
                    'centrocampista' => ['titulo' => 'Centrocampistas', 'icono' => 'bi-diagram-2', 'color' => 'emerald'],
                    'delantero' => ['titulo' => 'Delanteros', 'icono' => 'bi-lightning-charge', 'color' => 'rose'],
                ];
            @endphp

            <div class="space-y-4" id="plantilla">
                @forelse($plantillaAgrupada as $posClave => $jugadoresPos)
                    @php
                        $posMeta = $posicionesMeta[$posClave] ?? ['titulo' => ucfirst($posClave), 'icono' => 'bi-person', 'color' => 'zinc'];
                    @endphp

                    <details class="group bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-zinc-950/40" open>
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none select-none">
                            <div class="flex items-center gap-2.5">
                                <i class="bi {{ $posMeta['icono'] }} text-lime-400 text-base"></i>
                                <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-200">{{ $posMeta['titulo'] }}</h3>
                                <span class="px-2 py-0.5 rounded-md bg-zinc-800 text-[11px] font-semibold text-zinc-400">
                                    {{ $jugadoresPos->count() }}
                                </span>
                            </div>
                            <i class="bi bi-chevron-down text-zinc-500 transition-transform duration-300 group-open:-rotate-180"></i>
                        </summary>

                        <div class="px-6 pb-6 pt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                            @foreach($jugadoresPos as $jugador)
                                <div
                                    onclick="event.stopPropagation(); abrirModalJugador(this)"
                                    data-jugador='@json($jugador)'
                                    data-jugador-id="{{ $jugador->id }}"
                                    data-puntos="{{ $jugador->puntos_totales ?? 0 }}"
                                    data-precio="{{ $jugador->precio ?? 0 }}"
                                    class="jugador-card group bg-zinc-950/70 hover:bg-zinc-900 border border-zinc-800/80 hover:border-lime-500/40 rounded-xl p-3.5 flex items-center justify-between transition-all duration-200 cursor-pointer shadow-sm hover:shadow-lg">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border border-zinc-800 overflow-hidden shrink-0 flex items-center justify-center group-hover:border-lime-500/40 transition-colors">
                                            <img
                                                src="{{ $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png') }}"
                                                alt="{{ $jugador->nombre }}"
                                                class="w-full h-full object-cover"
                                                loading="lazy"
                                                onerror="this.src='{{ asset('assets/media/images/default-player.png') }}';">
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-zinc-100 group-hover:text-lime-400 transition-colors truncate">
                                                {{ $jugador->nombre }} {{ $jugador->apellido1 }}
                                            </h4>
                                            <p class="text-[11px] text-zinc-400 truncate">
                                                {{ $jugador->equipo->nombre ?? 'Sin club' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0 pl-2">
                                        <span class="block text-xs font-bold font-mono text-zinc-200">
                                            {{ number_format($jugador->precio ?? 0, 0, ',', '.') }} €
                                        </span>
                                        <span class="text-[10px] text-lime-400 font-semibold">
                                            {{ $jugador->puntos_totales ?? 0 }} pts
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @empty
                    <p class="text-xs text-zinc-500 py-3 text-center">No tienes jugadores en tu plantilla.</p>
                @endforelse
            </div>

        </div>

        <!-- ==========================================
             TAB 3: CLASIFICACIÓN
        ========================================== -->
        <div id="tab-pane-clasificacion" class="tab-pane hidden space-y-6">

            <div class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-2xl shadow-zinc-950/40 space-y-6">
                <!-- Selector Clasificación -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-800/80">
                    <div class="space-y-1">
                        <h2 class="text-lg font-bold text-zinc-100">Tabla de Clasificación</h2>
                        <p id="clasificacion-subtitle" class="text-xs text-zinc-400">Total acumulado de la liguilla</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="selectClasificacion" class="text-xs font-semibold uppercase tracking-wider text-zinc-400">
                            Filtro:
                        </label>
                        <select
                            id="selectClasificacion"
                            data-url-clasificacion="{{ route('liguillas.clasificacionAjax', $liguilla->id) }}"
                            class="bg-zinc-950 border border-zinc-800 text-zinc-100 text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:border-lime-400 transition-all cursor-pointer">
                            <option value="global">Clasificación General</option>
                            @foreach($jornadasDisponibles as $jornada)
                            <option value="{{ $jornada->id }}">
                                {{ $jornada->nombre ?? ('Jornada ' . $jornada->orden) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tabla Bento Minimalista -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-800/80 text-[11px] uppercase tracking-wider text-zinc-400">
                                <th class="py-3 px-4 w-16 text-center">Pos</th>
                                <th class="py-3 px-4">Manager</th>
                                <th class="py-3 px-4 text-right">Puntos</th>
                                <th id="thAlineacion" class="py-3 px-4 text-right hidden w-36">Alineación</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyClasificacion" class="divide-y divide-zinc-800/60 text-xs text-zinc-300 font-medium">
                            @forelse($clasificacion as $index => $item)
                            @php
                            $isMe = (Auth::id() == ($item->user_id ?? $item->id ?? null));
                            @endphp
                            <tr class="hover:bg-zinc-800/30 transition-colors {{ $isMe ? 'bg-lime-400/5' : '' }}">
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-zinc-200">
                                    @if($index === 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400/10 text-amber-400 text-xs">🥇</span>
                                    @elseif($index === 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-zinc-400/10 text-zinc-300 text-xs">🥈</span>
                                    @elseif($index === 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-700/10 text-amber-600 text-xs">🥉</span>
                                    @else
                                    #{{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-zinc-100 flex items-center gap-2">
                                    <span>{{ $item->name ?? $item->user->name ?? 'Usuario' }}</span>
                                    @if($isMe)
                                    <span class="px-2 py-0.5 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-[10px] font-bold">Tú</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-lime-400 text-sm">
                                    {{ $item->puntos ?? $item->total_puntos ?? 0 }}
                                </td>
                                <td class="py-3.5 px-4 text-right hidden"></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-zinc-500">
                                    No hay participantes registrados en esta liguilla.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ==========================================
             TAB 4: MIS JORNADAS
        ========================================== -->
        <div id="tab-pane-jornadas" class="tab-pane hidden space-y-6">

            <div class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-2xl shadow-zinc-950/40 space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-zinc-100">Historial de Alineaciones</h2>
                    <p class="text-xs text-zinc-400">Selecciona una jornada disputada para inspeccionar tu alineación y puntuación obtenida.</p>
                </div>

                <!-- Lista de Botones de Jornada -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    @forelse($jornadasDisponibles as $jornada)
                    <button
                        type="button"
                        data-jornada-id="{{ $jornada->id }}"
                        class="mis-jornada-link px-4 py-2 rounded-xl bg-zinc-950/80 hover:bg-zinc-800 text-zinc-300 hover:text-zinc-100 text-xs font-semibold border border-zinc-800 transition-all shrink-0 cursor-pointer">
                        {{ $jornada->nombre ?? ('Jornada ' . $jornada->orden) }}
                    </button>
                    @empty
                    <p class="text-xs text-zinc-500">No hay jornadas disputadas aún.</p>
                    @endforelse
                </div>

                <!-- Puntuación Resumen -->
                <div id="misJornadasPuntos" class="hidden p-4 rounded-xl bg-zinc-950/80 border border-zinc-800 flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-300">Puntuación Total de la Jornada:</span>
                    <span id="totalPuntosJornada" class="text-xl font-bold font-mono text-lime-400">0</span>
                </div>

                <!-- Campo Read-Only Mis Jornadas -->
                <div id="campoMisJornadas" class="hidden relative rounded-2xl overflow-hidden min-h-[580px] sm:min-h-[640px] flex flex-col justify-between py-6 px-3 sm:px-6 shadow-inner {{ $esSala ? 'bg-amber-950/40 border border-amber-800/50 ring-1 ring-amber-900/20' : 'border border-emerald-500/20 bg-gradient-to-b from-emerald-950/90 via-emerald-900/40 to-emerald-950/90' }}">

                    <!-- SVG de líneas de campo futbolístico / polideportivo -->
                    <div class="absolute inset-0 pointer-events-none opacity-25">
                        <!-- Borde exterior -->
                        <div class="absolute inset-3 border-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }} rounded-xl"></div>
                        <!-- Línea de medio campo -->
                        <div class="absolute inset-x-3 top-1/2 -translate-y-1/2 border-t-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }}"></div>
                        <!-- Círculo central -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-28 h-28 sm:w-36 sm:h-36 border-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }} rounded-full"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-2 h-2 {{ $esSala ? 'bg-amber-400' : 'bg-emerald-400' }} rounded-full"></div>
                        <!-- Área superior (Rival) -->
                        <div class="absolute top-3 left-1/2 -translate-x-1/2 w-44 sm:w-56 h-20 sm:h-24 border-b-2 border-x-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }} rounded-b-lg"></div>
                        <!-- Área inferior (Propia) -->
                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 w-44 sm:w-56 h-20 sm:h-24 border-t-2 border-x-2 {{ $esSala ? 'border-amber-400' : 'border-emerald-400' }} rounded-t-lg"></div>
                    </div>

                    <!-- 4 Filas Tácticas de Jugadores -->
                    <!-- 1. Delanteros -->
                    <div id="row-delanteros-jornada" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                        <!-- Inyectado dinámicamente -->
                    </div>

                    <!-- 2. Centrocampistas -->
                    <div id="row-centrocampistas-jornada" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                        <!-- Inyectado dinámicamente -->
                    </div>

                    <!-- 3. Defensas -->
                    <div id="row-defensas-jornada" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                        <!-- Inyectado dinámicamente -->
                    </div>

                    <!-- 4. Portero -->
                    <div id="row-portero-jornada" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                        <!-- Inyectado dinámicamente -->
                    </div>
                </div>

        </div>

    </div>

    <!-- ==========================================
             TAB 5: RESULTADOS DE PARTIDOS
        ========================================== -->
    <div id="tab-pane-resultados" class="tab-pane hidden space-y-6">

        <div class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-2xl shadow-zinc-950/40 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-800/80">
                <div>
                    <h2 class="text-lg font-bold text-zinc-100">Resultados Oficiales</h2>
                    <p class="text-xs text-zinc-400">Marcadores de los partidos del torneo oficial.</p>
                </div>

                <!-- Selector de Jornada -->
                <div class="flex items-center gap-2">
                    <label for="selectJornadaResultados" class="text-xs font-semibold uppercase tracking-wider text-zinc-400">
                        Jornada:
                    </label>
                    <select
                        id="selectJornadaResultados"
                        onchange="cargarJornadaAjax(this.value)"
                        class="bg-zinc-950 border border-zinc-800 text-zinc-100 text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:border-lime-400 transition-all cursor-pointer">
                        @foreach($jornadas as $jornada)
                        <option value="{{ $jornada->id }}" {{ (isset($jornadaSeleccionada) && $jornadaSeleccionada && $jornadaSeleccionada->id == $jornada->id) ? 'selected' : '' }}>
                            {{ $jornada->nombre ?? ('Jornada ' . ($jornada->numero ?? $jornada->orden ?? $loop->iteration)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="contenedor-partidos-ajax" class="transition-opacity duration-300">
                @if(isset($partidos) && $partidos->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($partidos as $partido)
                    <div class="bg-zinc-950/70 border border-zinc-800/80 rounded-xl p-4 flex items-center justify-between gap-4">
                        <!-- Local -->
                        <div class="flex items-center gap-2.5 flex-1 min-w-0">
                            <img src="{{ asset($partido->equipoLocal->logo ?? 'assets/media/images/default-team.png') }}" class="w-7 h-7 object-contain shrink-0" alt="{{ $partido->equipoLocal->nombre ?? 'Local' }}" onerror="this.src='{{ asset('assets/media/images/default-team.png') }}';">
                            <span class="text-xs font-bold text-zinc-200 truncate">{{ $partido->equipoLocal->nombre ?? 'Local' }}</span>
                        </div>

                        <!-- Marcador -->
                        <div class="px-3 py-1 bg-zinc-900 border border-zinc-800 rounded-lg text-center shrink-0">
                            <span class="text-sm font-bold font-mono text-zinc-100">
                                @if(is_null($partido->goles_local) || is_null($partido->goles_visitante))
                                    - : -
                                @else
                                    {{ $partido->goles_local }} : {{ $partido->goles_visitante }}
                                @endif
                            </span>
                        </div>

                        <!-- Visitante -->
                        <div class="flex items-center justify-end gap-2.5 flex-1 min-w-0">
                            <span class="text-xs font-bold text-zinc-200 truncate text-right">{{ $partido->equipoVisitante->nombre ?? 'Visitante' }}</span>
                            <img src="{{ asset($partido->equipoVisitante->logo ?? 'assets/media/images/default-team.png') }}" class="w-7 h-7 object-contain shrink-0" alt="{{ $partido->equipoVisitante->nombre ?? 'Visitante' }}" onerror="this.src='{{ asset('assets/media/images/default-team.png') }}';">
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-zinc-500 text-center py-8">No hay partidos disponibles para la jornada seleccionada.</p>
                @endif
            </div>
        </div>

    </div>

    <!-- ==========================================
             TAB 6: PARTICIPANTES
        ========================================== -->
    <div id="tab-pane-participantes" class="tab-pane hidden space-y-6">

        <div class="bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-2xl shadow-zinc-950/40 space-y-6">
            <div>
                <h2 class="text-lg font-bold text-zinc-100">Participantes de la Liguilla</h2>
                <p class="text-xs text-zinc-400">Managers que integran esta competición y sus plantillas.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($liguilla->usuarios ?? [] as $participante)
                <div class="bg-zinc-950/70 border border-zinc-800/80 rounded-xl p-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-zinc-900 border border-zinc-800 text-zinc-400 flex items-center justify-center text-sm font-bold shrink-0">
                            {{ strtoupper(substr($participante->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-zinc-100 truncate">{{ $participante->name }}</h4>
                            <p class="text-[11px] text-zinc-500 truncate">
                                Se unió el {{ \Carbon\Carbon::parse($participante->pivot->created_at)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ url('/user/liguillas/'.$liguilla->id.'/participante/'.$participante->id.'/plantilla') }}"
                        class="p-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-lime-400 border border-zinc-800 transition-all shrink-0"
                        title="Ver plantilla del participante">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

</div>

<!-- ==========================================
     MODAL 1: SELECCIÓN DE JUGADOR PARA SLOT
========================================== -->
<div id="modalSeleccionJugador" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="relative w-full max-w-lg bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl shadow-zinc-950 space-y-4 max-h-[90vh] flex flex-col">

        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-lime-400/10 text-lime-400 flex items-center justify-center text-sm font-bold">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-zinc-100" id="tituloModalSeleccion">Seleccionar Jugador</h3>
                    <p class="text-[11px] text-zinc-400" id="subtituloModalSeleccion">Elige un futbolista de tu plantilla</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalSeleccionJugador')" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Buscador Rápido -->
        <div class="shrink-0">
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                <input
                    type="text"
                    id="inputBuscarJugadorModal"
                    placeholder="Buscar por nombre o club..."
                    class="w-full pl-9 pr-4 py-2 bg-zinc-950 border border-zinc-800 rounded-xl text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-lime-400 transition-all">
            </div>
        </div>

        <!-- Alerta sin jugadores -->
        <div id="avisoSinJugadoresPosicion" class="hidden p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs">
            <span id="textoAvisoSinJugadores">No tienes jugadores disponibles para esta posición.</span>
        </div>

        <!-- Lista con Scroll de Jugadores -->
        <div id="listaJugadoresDisponibles" class="overflow-y-auto space-y-2 flex-1 pr-1 scrollbar-thin">
            <!-- Inyectado dinámicamente -->
        </div>

        <!-- Footer Modal -->
        <div class="pt-3 border-t border-zinc-800 flex items-center justify-between shrink-0">
            <button
                type="button"
                id="btnDesvincularSlot"
                class="py-2 px-3.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold rounded-xl border border-rose-500/30 transition-all cursor-pointer hidden">
                <i class="bi bi-x-circle me-1"></i> Quitar del campo
            </button>
            <button
                type="button"
                onclick="closeModal('modalSeleccionJugador')"
                class="py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold rounded-xl transition-all cursor-pointer ml-auto">
                Cerrar
            </button>
        </div>

    </div>
</div>

<!-- ==========================================
     MODAL 2: FICHA TÉCNICA DEL JUGADOR
========================================== -->
<div id="modalJugadorInfo" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="relative w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl shadow-zinc-950 space-y-5">

        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
            <h3 class="text-sm font-bold text-zinc-100">Ficha del Jugador</h3>
            <button type="button" onclick="closeModal('modalJugadorInfo')" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Perfil Principal -->
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-zinc-950 border border-zinc-800 overflow-hidden shrink-0 flex items-center justify-center">
                <img id="modalInfoFoto" src="" alt="" class="w-full h-full object-cover">
            </div>
            <div class="space-y-1 min-w-0">
                <h4 id="modalInfoNombre" class="text-base font-bold text-zinc-100 truncate"></h4>
                <p id="modalInfoEquipo" class="text-xs text-zinc-400 truncate"></p>
                <span id="modalInfoPosicion" class="inline-block px-2 py-0.5 rounded-md bg-zinc-800 text-[10px] font-bold text-lime-400 uppercase"></span>
            </div>
        </div>

        <!-- Estadísticas Grid Bento -->
        <div class="grid grid-cols-2 gap-3">
            <div class="p-3 rounded-xl bg-zinc-950/80 border border-zinc-800 text-center">
                <span class="block text-[10px] uppercase font-semibold text-zinc-500">Valor de Mercado</span>
                <span id="modalInfoPrecio" class="text-sm font-bold font-mono text-zinc-100"></span>
            </div>
            <div class="p-3 rounded-xl bg-zinc-950/80 border border-zinc-800 text-center">
                <span class="block text-[10px] uppercase font-semibold text-zinc-500">Puntos Totales</span>
                <span id="modalInfoPuntos" class="text-sm font-bold font-mono text-lime-400"></span>
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button
                type="button"
                onclick="closeModal('modalJugadorInfo')"
                class="py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold rounded-xl transition-all cursor-pointer">
                Entendido
            </button>
        </div>

    </div>
</div>

<!-- ==========================================
     MODAL 3: VER ALINEACIÓN DE RIVAL
========================================== -->
<div id="modalAlineacionClasificacion" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="relative w-full max-w-xl bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl shadow-zinc-950 space-y-4">

        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
            <div>
                <h3 class="text-sm font-bold text-zinc-100">Alineación de <span id="alineacionModalUsuario" class="text-lime-400"></span></h3>
                <p class="text-[11px] text-zinc-400">Puntuación en esta jornada: <strong id="alineacionModalTotal" class="text-zinc-200">...</strong></p>
            </div>
            <button type="button" onclick="closeModal('modalAlineacionClasificacion')" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <div class="relative rounded-2xl overflow-hidden min-h-[380px] p-4 shadow-inner {{ $esSala ? 'bg-amber-950/40 border border-amber-800/50 ring-1 ring-amber-900/20' : 'border border-emerald-500/20 bg-gradient-to-b from-emerald-950/90 via-emerald-900/40 to-emerald-950/90' }}">
            <div id="alineacionModalSlots" class="relative z-10 grid grid-cols-4 sm:grid-cols-6 gap-2.5">
                @for($i = 1; $i <= $limiteSlots; $i++)
                    <div class="slot vacio bg-zinc-900/80 border border-zinc-800 rounded-xl p-2 text-center flex flex-col items-center justify-center min-h-[80px]" data-slot="{{ $i }}">
                    <div class="card-body p-1 flex flex-col items-center justify-center">
                        <small class="text-zinc-500 text-[10px]">Vacío</small>
                    </div>
                </div>
                @endfor
            </div>
        </div>

    <div class="pt-2 flex justify-end">
        <button
            type="button"
            onclick="closeModal('modalAlineacionClasificacion')"
            class="py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold rounded-xl transition-all cursor-pointer">
            Cerrar
        </button>
    </div>

</div>
</div>

@endsection

@push('styles')
<style>
    .campo-futbol {
        background-size: 100% 100%;
    }

    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #3f3f46;
        border-radius: 9999px;
    }

    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-none {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Configuración de formaciones
    const cuotasPorFormacion = {
        '4-3-3': { portero: 1, defensa: 4, centrocampista: 3, delantero: 3 },
        '4-4-2': { portero: 1, defensa: 4, centrocampista: 4, delantero: 2 },
        '3-5-2': { portero: 1, defensa: 3, centrocampista: 5, delantero: 2 },
        '3-4-3': { portero: 1, defensa: 3, centrocampista: 4, delantero: 3 },
        '5-3-2': { portero: 1, defensa: 5, centrocampista: 3, delantero: 2 },
        '5-4-1': { portero: 1, defensa: 5, centrocampista: 4, delantero: 1 },
        '4-2-3-1': { portero: 1, defensa: 4, centrocampista: 5, delantero: 1 },
        '4-5-1': { portero: 1, defensa: 4, centrocampista: 5, delantero: 1 },
        '1-1-2': { portero: 1, defensa: 1, centrocampista: 1, delantero: 2 },
        '1-2-1': { portero: 1, defensa: 1, centrocampista: 2, delantero: 1 },
        '2-2': { portero: 1, defensa: 2, centrocampista: 0, delantero: 2 },
        '2-1-1': { portero: 1, defensa: 2, centrocampista: 1, delantero: 1 },
        '1-3': { portero: 1, defensa: 1, centrocampista: 0, delantero: 3 },
        '3-1': { portero: 1, defensa: 3, centrocampista: 0, delantero: 1 },
        '2-3-1': { portero: 1, defensa: 2, centrocampista: 3, delantero: 1 },
        '3-2-1': { portero: 1, defensa: 3, centrocampista: 2, delantero: 1 },
        '3-1-2': { portero: 1, defensa: 3, centrocampista: 1, delantero: 2 },
        '2-2-2': { portero: 1, defensa: 2, centrocampista: 2, delantero: 2 },
    };

    function obtenerCuotaFormacion(formacion) {
        if (cuotasPorFormacion[formacion]) return cuotasPorFormacion[formacion];
        if (!formacion || typeof formacion !== 'string') return { portero: 1, defensa: 4, centrocampista: 3, delantero: 3 };
        const parts = formacion.split('-').map(n => parseInt(n, 10) || 0);
        if (parts.length === 3) return { portero: 1, defensa: parts[0], centrocampista: parts[1], delantero: parts[2] };
        if (parts.length === 2) return { portero: 1, defensa: parts[0], centrocampista: 0, delantero: parts[1] };
        if (parts.length === 4) return { portero: 1, defensa: parts[0], centrocampista: parts[1] + parts[2], delantero: parts[3] };
        return { portero: 1, defensa: 4, centrocampista: 3, delantero: 3 };
    }

    // Plantilla del usuario en memoria JavaScript
    const plantillaCompleta = @json($plantillaUsuario);
    const alineacionGuardada = @json($alineacionActual ?? []);

    let formacionActiva = "{{ $formacionActual ?? '4-3-3' }}";
    let slotsEstado = {}; // { 1: { jugador_id, posicion_slot, ... } }
    let slotSeleccionando = null;
    let cacheModales = {
        jugadores: {},
        alineaciones: {}
    };

    // Funciones Vanilla JS para gestión de Tabs
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-zinc-800', 'text-lime-400', 'border', 'border-zinc-700/60', 'shadow-sm');
            btn.classList.add('text-zinc-400', 'hover:text-zinc-200', 'hover:bg-zinc-800/50');
        });

        const activeBtn = document.getElementById('tab-btn-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('text-zinc-400', 'hover:text-zinc-200', 'hover:bg-zinc-800/50');
            activeBtn.classList.add('bg-zinc-800', 'text-lime-400', 'border', 'border-zinc-700/60', 'shadow-sm');
        }

        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
        });

        const activePane = document.getElementById('tab-pane-' + tabId);
        if (activePane) {
            activePane.classList.remove('hidden');
        }
    }

    // Funciones Vanilla JS para gestión de Modales
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function abrirModalJugador(elemento) {
        try {
            const ptsAttr = elemento.getAttribute('data-puntos');
            const prcAttr = elemento.getAttribute('data-precio');

            const rawData = elemento.getAttribute('data-jugador');
            const j = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
            if (!j) return;

            const pts = ptsAttr !== null ? ptsAttr : (j.puntos_totales ?? 0);
            const prc = prcAttr !== null ? prcAttr : (j.precio ?? 0);

            const foto = j.foto ? (j.foto.startsWith('http') || j.foto.startsWith('/') ? j.foto : '/' + j.foto) : '/assets/media/images/default-player.png';
            const nombreCompleta = `${j.nombre || ''} ${j.apellido1 || ''} ${j.apellido2 || ''}`.trim();
            const equipoNombre = j.equipo && j.equipo.nombre ? j.equipo.nombre : (j.club && j.club.nombre ? j.club.nombre : 'Sin club');
            const posicion = j.posicion || 'Jugador';
            const precio = Number(prc).toLocaleString() + ' €';
            const puntos = pts + ' pts';

            document.getElementById('modalInfoNombre').textContent = nombreCompleta;
            document.getElementById('modalInfoEquipo').textContent = equipoNombre;
            document.getElementById('modalInfoPosicion').textContent = posicion;
            document.getElementById('modalInfoPrecio').textContent = precio;
            document.getElementById('modalInfoPuntos').textContent = puntos;
            document.getElementById('modalInfoFoto').src = foto;
            openModal('modalJugadorInfo');
        } catch (e) {
            console.error('Error al abrir modal de jugador:', e);
        }
    }

    // Cerrar modales con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('modalSeleccionJugador');
            closeModal('modalJugadorInfo');
            closeModal('modalAlineacionClasificacion');
        }
    });

    // Compartir o copiar código
    function compartirEnlace(codigo) {
        const enlace = "{{ url('/user/unirseLiguilla') }}?codigo=" + codigo;
        if (navigator.share) {
            navigator.share({
                title: 'Únete a mi liguilla en MiFantasy',
                text: 'Usa mi código para unirte a la competición:',
                url: enlace
            }).catch(err => console.error(err));
        } else {
            navigator.clipboard.writeText(enlace).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Enlace copiado!',
                    text: 'El código se ha copiado al portapapeles.',
                    background: '#18181b',
                    color: '#f4f4f5',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'border border-zinc-800 rounded-2xl'
                    }
                });
            });
        }
    }

    // Renderizar slots del campo táctico
    function renderCampoTactico(formacion) {
        formacionActiva = formacion;
        const hiddenForm = document.getElementById('hiddenFormacion');
        if (hiddenForm) hiddenForm.value = formacion;

        const cuota = obtenerCuotaFormacion(formacion);

        const rowDel = document.getElementById('row-delanteros');
        const rowMed = document.getElementById('row-centrocampistas');
        const rowDef = document.getElementById('row-defensas');
        const rowPor = document.getElementById('row-portero');

        if (rowDel) rowDel.innerHTML = '';
        if (rowMed) rowMed.innerHTML = '';
        if (rowDef) rowDef.innerHTML = '';
        if (rowPor) rowPor.innerHTML = '';

        let slotNumero = 1;

        // Delanteros
        if (rowDel) {
            rowDel.style.display = cuota.delantero > 0 ? '' : 'none';
            for (let i = 0; i < cuota.delantero; i++) {
                rowDel.appendChild(crearSlotElement(slotNumero, 'delantero'));
                slotNumero++;
            }
        }

        // Centrocampistas
        if (rowMed) {
            rowMed.style.display = cuota.centrocampista > 0 ? '' : 'none';
            for (let i = 0; i < cuota.centrocampista; i++) {
                rowMed.appendChild(crearSlotElement(slotNumero, 'centrocampista'));
                slotNumero++;
            }
        }

        // Defensas
        if (rowDef) {
            rowDef.style.display = cuota.defensa > 0 ? '' : 'none';
            for (let i = 0; i < cuota.defensa; i++) {
                rowDef.appendChild(crearSlotElement(slotNumero, 'defensa'));
                slotNumero++;
            }
        }

        // Portero
        if (rowPor) {
            rowPor.style.display = cuota.portero > 0 ? '' : 'none';
            for (let i = 0; i < cuota.portero; i++) {
                rowPor.appendChild(crearSlotElement(slotNumero, 'portero'));
                slotNumero++;
            }
        }

        vincularSlotsClicks();
        sincronizarHiddenInputs();
    }

    function crearSlotElement(slotNum, posicion) {
        const slotData = slotsEstado[slotNum];
        const div = document.createElement('div');
        div.className = 'slot group transition-all duration-200 cursor-pointer text-center';
        div.dataset.slot = slotNum;
        div.dataset.posicion = posicion;

        let jugador = null;
        if (slotData && slotData.jugador) {
            jugador = slotData.jugador;
        } else if (slotData && slotData.jugador_id) {
            jugador = plantillaCompleta.find(j => Number(j.id) === Number(slotData.jugador_id));
        }

        if (jugador) {
            const jug = jugador;
            div.classList.add('ocupado');
            div.innerHTML = `
                <div class="relative bg-zinc-900/90 hover:bg-zinc-850 border border-zinc-700/80 hover:border-lime-400 rounded-2xl p-2 sm:p-2.5 w-20 sm:w-28 flex flex-col items-center justify-center shadow-xl transition-all">
                    <button type="button" class="btn-eliminar-slot absolute -top-1.5 -left-1.5 w-5 h-5 rounded-full bg-rose-500 hover:bg-rose-400 text-white flex items-center justify-center text-[10px] shadow transition-all z-20 cursor-pointer" title="Quitar jugador">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <span class="absolute -top-1.5 -right-1.5 px-1.5 py-0.2 rounded-full bg-lime-400 text-zinc-950 font-mono font-bold text-[9px]">
                        ${posicion.substring(0, 3).toUpperCase()}
                    </span>
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-zinc-950 border border-zinc-700 overflow-hidden mb-1">
                        <img src="${jugador.foto || '/assets/media/images/default-player.png'}" class="w-full h-full object-cover" onerror="this.src='/assets/media/images/default-player.png'">
                    </div>
                    <span class="block text-[11px] sm:text-xs font-bold text-zinc-100 truncate w-full text-center px-1">${jug.nombre} ${jug.apellido1 || ''}</span>
                    <span class="block text-[9px] sm:text-[10px] text-zinc-400 truncate w-full text-center px-1">${jug.equipo ? jug.equipo.nombre : (jug.club ? jug.club.nombre : 'Sin club')}</span>
                </div>
            `;
        } else {
            div.classList.add('vacio');
            div.innerHTML = `
                <div class="relative border-2 border-dashed border-zinc-700/80 hover:border-lime-400/80 bg-zinc-950/60 hover:bg-zinc-900/60 rounded-2xl p-2 sm:p-2.5 w-20 sm:w-28 flex flex-col items-center justify-center transition-all">
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-zinc-900 border border-zinc-800 text-zinc-500 group-hover:text-lime-400 flex items-center justify-center text-sm mb-1 transition-colors">
                        <i class="bi bi-plus-lg font-bold"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-zinc-400 group-hover:text-zinc-200 uppercase">
                        ${posicion.substring(0, 3)}
                    </span>
                    <span class="text-[9px] text-zinc-600">Añadir</span>
                </div>
            `;
        }

        return div;
    }

    function vincularSlotsClicks() {
        document.querySelectorAll('.campo-futbol .slot').forEach(slot => {
            const btnEliminar = slot.querySelector('.btn-eliminar-slot');
            if (btnEliminar) {
                btnEliminar.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const slotNum = slot.dataset.slot;
                    delete slotsEstado[slotNum];
                    renderCampoTactico(formacionActiva);
                });
            }

            slot.addEventListener('click', function() {
                const slotNum = this.dataset.slot;
                const posicion = this.dataset.posicion;
                abrirModalSeleccion(slotNum, posicion);
            });
        });
    }

    function abrirModalSeleccion(slotNum, posicion) {
        slotSeleccionando = slotNum;
        const slotData = slotsEstado[slotNum];
        const btnDesvincular = document.getElementById('btnDesvincularSlot');

        document.getElementById('tituloModalSeleccion').textContent = `Seleccionar ${posicion.toUpperCase()} (Slot #${slotNum})`;

        if (slotData && slotData.jugador_id) {
            btnDesvincular.classList.remove('hidden');
            btnDesvincular.onclick = function() {
                delete slotsEstado[slotSeleccionando];
                closeModal('modalSeleccionJugador');
                renderCampoTactico(formacionActiva);
            };
        } else {
            btnDesvincular.classList.add('hidden');
        }

        // Filtrar jugadores de esa posición que no estén en OTROS slots
        const idsEnUso = Object.entries(slotsEstado)
            .filter(([sNum, data]) => sNum != slotNum && data && data.jugador_id)
            .map(([sNum, data]) => Number(data.jugador_id));

        const disponibles = plantillaCompleta.filter(j => {
            const coincidePos = strtolower(j.posicion || '') === strtolower(posicion);
            const noEnUso = !idsEnUso.includes(Number(j.id));
            return coincidePos && noEnUso;
        });

        renderListaJugadoresModal(disponibles, slotData ? slotData.jugador_id : null);
        openModal('modalSeleccionJugador');
    }

    function strtolower(str) {
        return (str || '').toLowerCase();
    }

    function renderListaJugadoresModal(jugadores, seleccionadoId) {
        const contenedor = document.getElementById('listaJugadoresDisponibles');
        const aviso = document.getElementById('avisoSinJugadoresPosicion');
        contenedor.innerHTML = '';

        if (!jugadores || jugadores.length === 0) {
            aviso.classList.remove('hidden');
            return;
        }
        aviso.classList.add('hidden');

        jugadores.forEach(j => {
            const isSelected = seleccionadoId && Number(seleccionadoId) === Number(j.id);
            const div = document.createElement('div');
            div.className = `p-3 rounded-xl border transition-all flex items-center justify-between gap-3 cursor-pointer ${
                isSelected
                    ? 'bg-lime-400/10 border-lime-400/40 text-lime-300'
                    : 'bg-zinc-950/70 hover:bg-zinc-900 border-zinc-800/80 hover:border-zinc-700 text-zinc-200'
            }`;

            div.innerHTML = `
                <div class="flex items-center gap-3 min-w-0">
                    <img src="${j.foto || '/assets/media/images/default-player.png'}" class="w-10 h-10 rounded-full object-cover bg-zinc-900 border border-zinc-800 shrink-0">
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-zinc-100 truncate">${j.nombre} ${j.apellido1 || ''}</h4>
                        <p class="text-[11px] text-zinc-400 truncate">${j.equipo ? j.equipo.nombre : 'Sin club'}</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="block text-xs font-bold font-mono text-zinc-200">${Number(j.precio || 0).toLocaleString()} €</span>
                    <button type="button" class="mt-1 px-3 py-1 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold text-[11px] rounded-lg transition-all">
                        ${isSelected ? 'Seleccionado' : 'Elegir'}
                    </button>
                </div>
            `;

            div.addEventListener('click', function() {
                slotsEstado[slotSeleccionando] = {
                    jugador_id: j.id,
                    slot: slotSeleccionando,
                    posicion: j.posicion
                };
                closeModal('modalSeleccionJugador');
                renderCampoTactico(formacionActiva);
            });

            contenedor.appendChild(div);
        });
    }

    // Buscador en modal
    document.getElementById('inputBuscarJugadorModal').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('#listaJugadoresDisponibles > div').forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(query) ? 'flex' : 'none';
        });
    });

    function sincronizarHiddenInputs() {
        const container = document.getElementById('hiddenInputsContainer');
        if (!container) return;
        container.innerHTML = '';

        const hiddenForm = document.getElementById('hiddenFormacion');
        if (hiddenForm) hiddenForm.value = formacionActiva;

        Object.entries(slotsEstado).forEach(([slotNum, data]) => {
            if (data && data.jugador_id) {
                const inJugador = document.createElement('input');
                inJugador.type = 'hidden';
                inJugador.name = 'jugadores[]';
                inJugador.value = data.jugador_id;
                container.appendChild(inJugador);
            }
        });
    }

    // Modal de Ficha Técnica Jugador
    function mostrarModalJugador(data) {
        if (!data || !data.jugador) return;
        const j = data.jugador;
        document.getElementById('modalInfoFoto').src = j.foto || '/assets/media/images/default-player.png';
        document.getElementById('modalInfoNombre').textContent = `${j.nombre} ${j.apellido1 || ''} ${j.apellido2 || ''}`;
        document.getElementById('modalInfoEquipo').textContent = j.equipo ? j.equipo.nombre : 'Sin club';
        document.getElementById('modalInfoPosicion').textContent = j.posicion || 'Jugador';
        document.getElementById('modalInfoPrecio').textContent = `${Number(j.precio || 0).toLocaleString()} €`;
        document.getElementById('modalInfoPuntos').textContent = `${j.puntos_totales ?? 0} pts`;
        openModal('modalJugadorInfo');
    }

    // Renderizar slots en modal de alineación rival
    function renderAlineacionSlots(data, slotsWrap, totalEl) {
        totalEl.textContent = `${data.total_puntos ?? 0} pts`;
        const slots = slotsWrap.querySelectorAll('.slot');
        slots.forEach(s => {
            s.className = 'slot vacio bg-zinc-900/80 border border-zinc-800 rounded-xl p-2 text-center flex flex-col items-center justify-center min-h-[80px]';
            s.innerHTML = '<div class="card-body p-1 flex flex-col items-center justify-center"><small class="text-zinc-500 text-[10px]">Vacío</small></div>';
        });

        if (data.status === 'ok' && data.jugadores) {
            data.jugadores.forEach((jug, idx) => {
                const s = slotsWrap.querySelector(`.slot[data-slot="${idx + 1}"]`);
                if (s) {
                    s.className = 'slot ocupado bg-zinc-900/90 border border-zinc-700 rounded-xl p-2 text-center flex flex-col items-center justify-center relative min-h-[80px]';
                    s.innerHTML = `
                        <span class="absolute top-1 right-1 px-1.5 py-0.2 rounded-full bg-lime-400 text-zinc-950 font-mono font-bold text-[9px]">
                            ${jug.puntos ?? 0}
                        </span>
                        <img src="${jug.foto || '/assets/media/images/default-player.png'}" class="w-8 h-8 rounded-full object-cover mb-1" onerror="this.src='/assets/media/images/default-player.png'">
                        <span class="block text-[10px] font-bold text-zinc-100 truncate w-full text-center">${jug.nombre}</span>
                    `;
                }
            });
        }
    }

    function normalizarPosicion(pos) {
        const p = (pos || '').toLowerCase();
        if (p === 'portero') return 'portero';
        if (p === 'defensa') return 'defensa';
        if (p === 'centrocampista' || p === 'medio') return 'centrocampista';
        if (p === 'delantero') return 'delantero';
        return p;
    }

    function inicializarSlotsDesdeAlineacionGuardada() {
        slotsEstado = {};
        // alineacionGuardada puede ser objeto Eloquent (con .jugadores) o array plano
        const jugadores = Array.isArray(alineacionGuardada)
            ? alineacionGuardada
            : (alineacionGuardada && Array.isArray(alineacionGuardada.jugadores) ? alineacionGuardada.jugadores : []);

        if (!jugadores || jugadores.length === 0) return;

        // Usar la formación guardada si existe
        if (alineacionGuardada && alineacionGuardada.formacion) {
            formacionActiva = alineacionGuardada.formacion;
            const sel = document.getElementById('selectFormacion');
            if (sel) sel.value = formacionActiva;
        }

        const cuota = obtenerCuotaFormacion(formacionActiva);
        const porPos = {
            delantero: jugadores.filter(j => normalizarPosicion(j.posicion) === 'delantero'),
            centrocampista: jugadores.filter(j => normalizarPosicion(j.posicion) === 'centrocampista'),
            defensa: jugadores.filter(j => normalizarPosicion(j.posicion) === 'defensa'),
            portero: jugadores.filter(j => normalizarPosicion(j.posicion) === 'portero'),
        };

        // Asignar slots en el mismo orden que renderCampoTactico: DEL → CEN → DEF → POR
        let slotNumero = 1;
        ['delantero', 'centrocampista', 'defensa', 'portero'].forEach(pos => {
            const max = cuota[pos] || 0;
            const lista = porPos[pos] || [];
            for (let i = 0; i < max; i++) {
                if (lista[i]) {
                    slotsEstado[slotNumero] = {
                        jugador_id: lista[i].id,
                        slot: slotNumero,
                        posicion: pos,
                        jugador: lista[i]
                    };
                }
                slotNumero++;
            }
        });
    }

    function reubicarJugadoresEnNuevaFormacion(nuevaFormacion) {
        const cuota = obtenerCuotaFormacion(nuevaFormacion);
        // Agrupar jugadores activos por posición normalizada
        const porPos = { delantero: [], centrocampista: [], defensa: [], portero: [] };
        Object.values(slotsEstado).forEach(data => {
            if (!data || !data.jugador_id) return;
            const pos = normalizarPosicion(data.posicion);
            if (porPos[pos]) porPos[pos].push(data);
        });

        // Reconstruir slotsEstado respetando nuevas cuotas; sobrantes se descartan (vuelven al modal)
        slotsEstado = {};
        let slotNumero = 1;
        ['delantero', 'centrocampista', 'defensa', 'portero'].forEach(pos => {
            const max = cuota[pos] || 0;
            const lista = porPos[pos] || [];
            for (let i = 0; i < max; i++) {
                if (lista[i]) {
                    slotsEstado[slotNumero] = {
                        jugador_id: lista[i].jugador_id,
                        slot: slotNumero,
                        posicion: pos,
                        jugador: lista[i].jugador
                    };
                }
                slotNumero++;
            }
        });
    }

    // Inicialización al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            switchTab(tabParam);
        }

        // Cargar alineación previa en el estado
        inicializarSlotsDesdeAlineacionGuardada();

        renderCampoTactico(formacionActiva);

        // Event listener formación — reubicar jugadores antes de repintar
        const selectFormacion = document.getElementById('selectFormacion');
        if (selectFormacion) {
            selectFormacion.addEventListener('change', function() {
                reubicarJugadoresEnNuevaFormacion(this.value);
                renderCampoTactico(this.value);
            });
        }

        // Guardar alineación (AJAX silencioso con Fetch API y SweetAlert2)
        const btnGuardar = document.getElementById('btnGuardarAlineacion');
        const formAlineacion = document.getElementById('formAlineacion');

        function ejecutarGuardadoAlineacionAjax() {
            sincronizarHiddenInputs();
            const formData = new FormData(formAlineacion);
            const btnOriginalHtml = btnGuardar.innerHTML;

            btnGuardar.disabled = true;
            btnGuardar.classList.add('opacity-75', 'cursor-not-allowed');
            btnGuardar.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i><span>Guardando...</span>';

            fetch(formAlineacion.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Ocurrió un error al guardar la alineación.');
                }
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Alineación guardada!',
                    text: data.message || 'Tu alineación se ha guardado correctamente.',
                    confirmButtonColor: '#a3e635',
                    background: '#18181b',
                    color: '#f4f4f5',
                    customClass: {
                        popup: 'border border-zinc-800 rounded-2xl',
                        confirmButton: '!text-zinc-950 !font-bold'
                    }
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al guardar',
                    text: error.message || 'No se pudo guardar la alineación. Inténtalo de nuevo.',
                    confirmButtonColor: '#a3e635',
                    background: '#18181b',
                    color: '#f4f4f5',
                    customClass: {
                        popup: 'border border-zinc-800 rounded-2xl',
                        confirmButton: '!text-zinc-950 !font-bold'
                    }
                });
            })
            .finally(() => {
                btnGuardar.disabled = false;
                btnGuardar.classList.remove('opacity-75', 'cursor-not-allowed');
                btnGuardar.innerHTML = btnOriginalHtml;
            });
        }

        if (btnGuardar && formAlineacion) {
            btnGuardar.addEventListener('click', function(e) {
                e.preventDefault();
                sincronizarHiddenInputs();
                const cuotaActual = obtenerCuotaFormacion(formacionActiva);
                const slotsRequeridos = cuotaActual.portero + cuotaActual.defensa + cuotaActual.centrocampista + cuotaActual.delantero;
                const totalSlots = Object.values(slotsEstado).filter(s => s && s.jugador_id).length;
                if (totalSlots < slotsRequeridos) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Alineación incompleta',
                        text: `Tienes ${totalSlots} de ${slotsRequeridos} jugadores en el campo. ¿Deseas guardar de todos modos?`,
                        showCancelButton: true,
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#a3e635',
                        cancelButtonColor: '#27272a',
                        background: '#18181b',
                        color: '#f4f4f5',
                        customClass: {
                            popup: 'border border-zinc-800 rounded-2xl'
                        }
                    }).then(res => {
                        if (res.isConfirmed) {
                            ejecutarGuardadoAlineacionAjax();
                        }
                    });
                } else {
                    ejecutarGuardadoAlineacionAjax();
                }
            });

            formAlineacion.addEventListener('submit', function(e) {
                e.preventDefault();
                ejecutarGuardadoAlineacionAjax();
            });
        }


        // AJAX Clasificación
        const selectClasif = document.getElementById('selectClasificacion');
        const tbodyClasif = document.getElementById('tbodyClasificacion');
        const subtitleClasif = document.getElementById('clasificacion-subtitle');
        const thAlineacion = document.getElementById('thAlineacion');
        const currentUserId = "{{ Auth::id() }}";

        if (selectClasif && tbodyClasif) {
            selectClasif.addEventListener('change', function() {
                const url = this.dataset.urlClasificacion;
                const modo = this.value;

                tbodyClasif.innerHTML = `<tr><td colspan="4" class="py-8 text-center text-zinc-400">Cargando clasificación...</td></tr>`;

                fetch(`${url}?modo_clasificacion=${encodeURIComponent(modo)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.modo === 'global') {
                            subtitleClasif.textContent = 'Total acumulado de la liguilla';
                            thAlineacion.classList.add('hidden');
                        } else {
                            const j = data.jornada || {};
                            subtitleClasif.textContent = `Jornada ${j.orden ?? ''} ${j.nombre ?? ''}`.trim();
                            thAlineacion.classList.remove('hidden');
                        }

                        tbodyClasif.innerHTML = '';
                        if (!data.clasificacion || data.clasificacion.length === 0) {
                            tbodyClasif.innerHTML = `<tr><td colspan="4" class="py-8 text-center text-zinc-500">No hay datos disponibles para esta jornada.</td></tr>`;
                            return;
                        }

                        data.clasificacion.forEach((u, idx) => {
                            const isMe = (Number(currentUserId) === Number(u.id));
                            const tr = document.createElement('tr');
                            tr.className = `hover:bg-zinc-800/30 transition-colors ${isMe ? 'bg-lime-400/5' : ''}`;

                            let posIcon = `#${idx + 1}`;
                            if (idx === 0) posIcon = '🥇';
                            else if (idx === 1) posIcon = '🥈';
                            else if (idx === 2) posIcon = '🥉';

                            tr.innerHTML = `
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-zinc-200">${posIcon}</td>
                                <td class="py-3.5 px-4 font-semibold text-zinc-100 flex items-center gap-2">
                                    <span>${u.name || u.email || 'Usuario'}</span>
                                    ${isMe ? '<span class="px-2 py-0.5 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-[10px] font-bold">Tú</span>' : ''}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-lime-400 text-sm">${u.puntos ?? 0}</td>
                                <td class="py-3.5 px-4 text-right ${data.modo === 'global' ? 'hidden' : ''}">
                                    ${data.modo !== 'global' && data.jornada ? `
                                        <button type="button" class="ver-alineacion-btn px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-[11px] font-semibold rounded-lg transition-all" data-user-id="${u.id}" data-jornada-id="${data.jornada.id}" data-user-name="${u.name || 'Usuario'}">
                                            Ver XI
                                        </button>
                                    ` : ''}
                                </td>
                            `;
                            tbodyClasif.appendChild(tr);
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        tbodyClasif.innerHTML = `<tr><td colspan="4" class="py-8 text-center text-rose-400">Error al cargar clasificación.</td></tr>`;
                    });
            });
        }

        // Click en "Ver alineación rival"
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.ver-alineacion-btn');
            if (!btn) return;

            const userId = btn.dataset.userId;
            const jornadaId = btn.dataset.jornadaId;
            const userName = btn.dataset.userName;

            document.getElementById('alineacionModalUsuario').textContent = userName;
            document.getElementById('alineacionModalTotal').textContent = '...';
            const slotsWrap = document.getElementById('alineacionModalSlots');

            openModal('modalAlineacionClasificacion');

            const cacheKey = `${userId}:${jornadaId}`;
            if (cacheModales.alineaciones[cacheKey]) {
                renderAlineacionSlots(cacheModales.alineaciones[cacheKey], slotsWrap, document.getElementById('alineacionModalTotal'));
                return;
            }

            fetch(`/user/liguillas/{{ $liguilla->id }}/alineacion-usuario/${userId}/jornada/${jornadaId}`)
                .then(res => res.json())
                .then(data => {
                    cacheModales.alineaciones[cacheKey] = data;
                    renderAlineacionSlots(data, slotsWrap, document.getElementById('alineacionModalTotal'));
                })
                .catch(err => console.error(err));
        });

        // Click en Mis Jornadas
        document.querySelectorAll('.mis-jornada-link').forEach(btn => {
            btn.addEventListener('click', function() {
                const jornadaId = this.dataset.jornadaId;
                document.querySelectorAll('.mis-jornada-link').forEach(b => b.classList.remove('bg-zinc-800', 'text-lime-400', 'border-lime-400/40'));
                this.classList.add('bg-zinc-800', 'text-lime-400', 'border-lime-400/40');

                const campo = document.getElementById('campoMisJornadas');
                const panelPuntos = document.getElementById('misJornadasPuntos');
                const totalPuntosEl = document.getElementById('totalPuntosJornada');

                const rowDel = document.getElementById('row-delanteros-jornada');
                const rowMed = document.getElementById('row-centrocampistas-jornada');
                const rowDef = document.getElementById('row-defensas-jornada');
                const rowPor = document.getElementById('row-portero-jornada');

                fetch(`/user/liguillas/{{ $liguilla->id }}/alineacion/${jornadaId}`)
                    .then(res => res.json())
                    .then(data => {
                        campo.classList.remove('hidden');
                        panelPuntos.classList.remove('hidden');

                        const formacion = data.formacion || formacionActiva;
                        const cuota = obtenerCuotaFormacion(formacion);

                        const todosJugadores = Array.isArray(data.jugadores) ? [...data.jugadores] : [];
                        const porJugadores = todosJugadores.filter(j => (j.posicion || '').toLowerCase().includes('por'));
                        const defJugadores = todosJugadores.filter(j => (j.posicion || '').toLowerCase().includes('def'));
                        const cenJugadores = todosJugadores.filter(j => (j.posicion || '').toLowerCase().includes('cen') || (j.posicion || '').toLowerCase().includes('med'));
                        const delJugadores = todosJugadores.filter(j => (j.posicion || '').toLowerCase().includes('del'));

                        const renderFila = (rowEl, cantidad, jugadoresPos, posNombre) => {
                            if (!rowEl) return;
                            rowEl.innerHTML = '';
                            rowEl.style.display = cantidad > 0 ? '' : 'none';
                            for (let i = 0; i < cantidad; i++) {
                                const jug = jugadoresPos[i] || null;
                                const div = document.createElement('div');
                                div.className = 'slot transition-all duration-200 text-center relative z-10 w-20 sm:w-28';
                                if (jug) {
                                    div.classList.add('ocupado');
                                    div.innerHTML = `
                                        <div class="relative bg-zinc-900/90 border border-zinc-700/80 rounded-2xl p-2 sm:p-2.5 w-full flex flex-col items-center justify-center shadow-xl">
                                            <span class="absolute -top-1.5 -right-1.5 px-1.5 py-0.2 rounded-full bg-lime-400 text-zinc-950 font-mono font-bold text-[9px] whitespace-nowrap line-clamp-1">
                                                ${jug.puntos ?? 0}
                                            </span>
                                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-zinc-950 border border-zinc-700 overflow-hidden mb-1">
                                                <img src="${jug.foto || '/assets/media/images/default-player.png'}" class="w-full h-full object-cover" onerror="this.src='/assets/media/images/default-player.png'">
                                            </div>
                                            <span class="block text-[11px] sm:text-xs font-bold text-zinc-100 truncate w-full text-center">
                                                ${jug.nombre}
                                            </span>
                                            <span class="block text-[9px] sm:text-[10px] text-zinc-400 truncate w-full text-center">
                                                ${jug.apellido1 || ''}
                                            </span>
                                        </div>
                                    `;
                                } else {
                                    div.classList.add('vacio');
                                    div.innerHTML = `
                                        <div class="relative border-2 border-dashed border-zinc-700/80 bg-zinc-950/60 rounded-2xl p-2 sm:p-2.5 w-full flex flex-col items-center justify-center">
                                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-zinc-900 border border-zinc-800 text-zinc-500 flex items-center justify-center text-sm mb-1">
                                                <i class="bi bi-dash-lg"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-zinc-500 uppercase">
                                                ${posNombre.substring(0, 3)}
                                            </span>
                                            <span class="text-[9px] text-zinc-600">Vacío</span>
                                        </div>
                                    `;
                                }
                                rowEl.appendChild(div);
                            }
                        };

                        renderFila(rowDel, cuota.delantero, delJugadores, 'delantero');
                        renderFila(rowMed, cuota.centrocampista, cenJugadores, 'centrocampista');
                        renderFila(rowDef, cuota.defensa, defJugadores, 'defensa');
                        renderFila(rowPor, cuota.portero, porJugadores, 'portero');

                        totalPuntosEl.textContent = data.total_puntos ?? 0;
                    })
                    .catch(err => console.error(err));
            });
        });
    });

    async function cargarJornadaAjax(jornadaId) {
        const contenedor = document.getElementById('contenedor-partidos-ajax');
        contenedor.style.opacity = '0.4'; // Efecto visual de carga
        try {
            const url = '?tab=resultados&jornada_id=' + jornadaId;
            const response = await fetch(url);
            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            contenedor.innerHTML = doc.getElementById('contenedor-partidos-ajax').innerHTML;
            window.history.pushState({}, '', url); // Mantiene la URL correcta arriba
        } catch (error) {
            console.error('Error cargando los partidos:', error);
        } finally {
            contenedor.style.opacity = '1';
        }
    }
</script>
@endpush