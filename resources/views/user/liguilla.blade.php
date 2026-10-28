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
<div
    x-data="liguillaUi"
    x-init="init()"
    data-live-points-url="{{ route('liguillas.livePoints', $liguilla->id) }}"
    class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-4 sm:space-y-6">

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
                    @click.stop="compartirEnlace('{{ $liguilla->codigo_unico }}')"
                    class="py-1 px-2.5 bg-zinc-950/80 hover:bg-zinc-800 text-zinc-200 hover:text-zinc-100 font-mono font-bold text-[11px] rounded-lg border border-zinc-800 hover:border-zinc-700 transition-all flex items-center gap-1.5 shadow-sm group/btn cursor-pointer"
                    title="Copiar código">
                    <span class="hidden sm:inline text-zinc-400 font-sans font-medium text-[10px] uppercase tracking-wider">Código:</span>
                    <span class="text-lime-400 tracking-wider text-xs">{{ $liguilla->codigo_unico }}</span>
                    <i class="bi bi-share text-zinc-400 group-hover/btn:text-lime-400 transition-colors text-[10px]"></i>
                </button>

                <a
                    href="{{ url('/user/liguillas') }}"
                    @click.stop
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
        @foreach([
            'alineacion' => ['bi-diagram-3', 'Alineación'],
            'plantilla' => ['bi-people', 'Mi Plantilla'],
            'clasificacion' => ['bi-trophy', 'Clasificación'],
            'jornadas' => ['bi-calendar-check', 'Mis Jornadas'],
            'resultados' => ['bi-card-checklist', 'Resultados'],
            'participantes' => ['bi-person-lines-fill', 'Participantes'],
        ] as $tab => [$icon, $label])
            <button
                type="button"
                id="tab-btn-{{ $tab }}"
                @click="tabActiva = '{{ $tab }}'"
                :class="tabActiva === '{{ $tab }}' ? 'bg-zinc-800 text-lime-400 border border-zinc-700/60 shadow-sm' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/50'"
                class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <i class="bi {{ $icon }}"></i>
                <span>{{ $label }}</span>
                @if($tab === 'plantilla')
                    <span class="px-1.5 py-0.2 rounded-full bg-zinc-800 text-[10px] text-zinc-300">{{ $plantillaUsuario?->count() ?? 0 }}</span>
                @elseif($tab === 'participantes')
                    <span class="px-1.5 py-0.2 rounded-full bg-zinc-800 text-[10px] text-zinc-300">{{ $liguilla->usuarios?->count() ?? 0 }}</span>
                @endif
            </button>
        @endforeach
    </div>

    <!-- Contenido de las Pestañas -->
    <div class="space-y-6">

        <!-- ==========================================
             TAB 1: ALINEACIÓN / CAMPO TÁCTICO
        ========================================== -->
        <div id="tab-pane-alineacion" x-show="tabActiva === 'alineacion'" x-cloak class="tab-pane space-y-6">

            <!-- Panel de Control Táctico Compacto -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-xl p-3 mb-4 flex flex-col gap-3 shadow-sm mx-auto w-full">
                <!-- Fila superior: Esquema + Jornada -->
                <div class="flex justify-between items-center w-full">
                    <div class="flex items-center gap-2">
                        <label for="selectFormacion" class="text-[10px] sm:text-xs text-zinc-400 font-bold uppercase tracking-wider mb-0">Esquema:</label>
                        <select name="formacion" id="selectFormacion" x-model="formacionActiva" @change="cambiarFormacion" class="py-1 px-2 text-xs rounded-md bg-zinc-950 border border-zinc-800 text-zinc-200 outline-none focus:border-lime-400 transition-colors cursor-pointer">
                            @foreach($formaciones as $clave => $nombre)
                            <option value="{{ $clave }}">
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
                <button type="button" id="btnGuardarAlineacion" @click="guardarAlineacion" class="w-full py-2 bg-lime-400 hover:bg-lime-500 text-zinc-950 text-sm font-bold rounded-lg transition-colors flex justify-center items-center gap-2 border-none shadow-sm cursor-pointer">
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
                        <template x-for="posicion in ['delantero', 'centrocampista', 'defensa', 'portero']" :key="posicion">
                            <div x-show="slotsPorPosicion(posicion).length" class="relative z-10 flex items-center justify-center gap-2 sm:gap-6 py-2">
                                <template x-for="slot in slotsPorPosicion(posicion)" :key="slot.slot">
                                    <div class="slot group transition-all duration-200 cursor-pointer text-center" @click="abrirSeleccion(slot.slot, slot.posicion)">
                                        <template x-if="jugadorDelSlot(slot)">
                                            <div class="relative bg-zinc-900/90 hover:bg-zinc-850 border border-zinc-700/80 hover:border-lime-400 rounded-2xl p-2 sm:p-2.5 w-20 sm:w-28 flex flex-col items-center justify-center shadow-xl transition-all">
                                                <button type="button" @click.stop="quitarJugador(slot.slot)" class="absolute -top-1.5 -left-1.5 w-5 h-5 rounded-full bg-rose-500 hover:bg-rose-400 text-white flex items-center justify-center text-[10px] shadow transition-all z-20 cursor-pointer" title="Quitar jugador">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                                <span class="absolute -top-1.5 -right-1.5 px-1.5 py-0.2 rounded-full bg-lime-400 text-zinc-950 font-mono font-bold text-[9px]" x-text="slot.posicion.substring(0, 3).toUpperCase()"></span>
                                                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-zinc-950 border border-zinc-700 overflow-hidden mb-1">
                                                    <img :src="jugadorDelSlot(slot).foto || defaultPlayerImage" class="w-full h-full object-cover" alt="">
                                                </div>
                                                <span class="block text-[11px] sm:text-xs font-bold text-zinc-100 truncate w-full text-center px-1" x-text="nombreJugador(jugadorDelSlot(slot))"></span>
                                                <span class="block text-[9px] sm:text-[10px] text-zinc-400 truncate w-full text-center px-1" x-text="equipoJugador(jugadorDelSlot(slot))"></span>
                                            </div>
                                        </template>
                                        <template x-if="!jugadorDelSlot(slot)">
                                            <div class="relative border-2 border-dashed border-zinc-700/80 hover:border-lime-400/80 bg-zinc-950/60 hover:bg-zinc-900/60 rounded-2xl p-2 sm:p-2.5 w-20 sm:w-28 flex flex-col items-center justify-center transition-all">
                                                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-zinc-900 border border-zinc-800 text-zinc-500 group-hover:text-lime-400 flex items-center justify-center text-sm mb-1 transition-colors"><i class="bi bi-plus-lg font-bold"></i></div>
                                                <span class="text-[10px] sm:text-xs font-bold text-zinc-400 group-hover:text-zinc-200 uppercase" x-text="slot.posicion.substring(0, 3)"></span>
                                                <span class="text-[9px] text-zinc-600">Añadir</span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>
                </div>

                <!-- Formulario Oculto para sincronizar slots -->
                <form id="formAlineacion" x-ref="formAlineacion" method="POST" action="{{ url('/user/liguillas/'.$liguilla->id.'/alineacion/guardar') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="formacion" :value="formacionActiva">
                    <template x-for="slot in slots.filter(slot => slot.jugador_id)" :key="`jugador-${slot.slot}`">
                        <input type="hidden" name="jugadores[]" :value="slot.jugador_id">
                    </template>
                </form>

            </div>

        </div>

        <!-- ==========================================
             TAB 2: MI PLANTILLA
        ========================================== -->
        <div id="tab-pane-plantilla" x-show="tabActiva === 'plantilla'" x-cloak class="tab-pane space-y-6">

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
                                    @click='abrirFicha(@json($jugador))'
                                    class="jugador-card group bg-zinc-950/70 hover:bg-zinc-900 border border-zinc-800/80 hover:border-lime-500/40 rounded-xl p-3.5 flex items-center justify-between transition-all duration-200 cursor-pointer shadow-sm hover:shadow-lg">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border border-zinc-800 overflow-hidden shrink-0 flex items-center justify-center group-hover:border-lime-500/40 transition-colors">
                                            <img
                                                src="{{ $jugador->foto ? asset($jugador->foto) : asset('assets/media/images/default-player.png') }}"
                                                alt="{{ $jugador->nombre }}"
                                                class="w-full h-full object-cover"
                                                loading="lazy"
                                                data-fallback-src="{{ asset('assets/media/images/default-player.png') }}"
                                                onerror="this.src=this.dataset.fallbackSrc;">
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
        <div id="tab-pane-clasificacion" x-show="tabActiva === 'clasificacion'" x-cloak class="tab-pane space-y-6">

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
                            x-ref="selectClasificacion"
                            x-model="modoClasificacion"
                            @change="cargarClasificacion"
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
                    <table class="w-full text-left border-collapse" @live-points-updated.window="updateClasificacion($event.detail)">
                        <thead>
                            <tr class="border-b border-zinc-800/80 text-[11px] uppercase tracking-wider text-zinc-400">
                                <th class="py-3 px-4 w-16 text-center">Pos</th>
                                <th class="py-3 px-4">Manager</th>
                                <th class="py-3 px-4 text-right">Puntos</th>
                                <th id="thAlineacion" class="py-3 px-4 text-right" x-show="modoClasificacion !== 'global'">Alineación</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyClasificacion" class="divide-y divide-zinc-800/60 text-xs text-zinc-300 font-medium">
                            <template x-for="(item, index) in clasificacion" :key="item.id">
                                <tr :class="{'bg-lime-400/5': item.id == {{ Auth::id() }} }" class="hover:bg-zinc-800/30 transition-colors">
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-zinc-200">
                                        <span x-text="index === 0 ? '🥇' : (index === 1 ? '🥈' : (index === 2 ? '🥉' : '#' + (index + 1)))"></span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-zinc-100 flex items-center gap-2">
                                        <span x-text="item.name"></span>
                                        <template x-if="item.id == {{ Auth::id() }}">
                                            <span class="px-2 py-0.5 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-[10px] font-bold">Tú</span>
                                        </template>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-lime-400 text-sm" x-text="item.puntos"></td>
                                    <td class="py-3.5 px-4 text-right" x-show="modoClasificacion !== 'global'">
                                        <button type="button" class="ver-alineacion-btn px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-[11px] font-semibold rounded-lg transition-all"
                                            :data-user-id="item.id"
                                            :data-jornada-id="modoClasificacion"
                                            @click="abrirAlineacionRival(item.id, modoClasificacion, item.name)">
                                            Ver XI
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ==========================================
             TAB 4: MIS JORNADAS
        ========================================== -->
        <div id="tab-pane-jornadas" x-show="tabActiva === 'jornadas'" x-cloak class="tab-pane space-y-6">

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
                        @click="cargarMiJornada('{{ $jornada->id }}')"
                        :class="jornadaSeleccionada === '{{ $jornada->id }}' ? 'bg-zinc-800 text-lime-400 border-lime-400/40' : 'bg-zinc-950/80 text-zinc-300 border-zinc-800'"
                        class="mis-jornada-link px-4 py-2 rounded-xl hover:bg-zinc-800 hover:text-zinc-100 text-xs font-semibold border transition-all shrink-0 cursor-pointer">
                        {{ $jornada->nombre ?? ('Jornada ' . $jornada->orden) }}
                    </button>
                    @empty
                    <p class="text-xs text-zinc-500">No hay jornadas disputadas aún.</p>
                    @endforelse
                </div>

                <!-- Puntuación Resumen -->
                <div x-show="jornadaVisible" x-cloak id="misJornadasPuntos" class="p-4 rounded-xl bg-zinc-950/80 border border-zinc-800 flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-300">Puntuación Total de la Jornada:</span>
                    <span id="totalPuntosJornada" class="text-xl font-bold font-mono text-lime-400" x-text="jornadaPuntos">0</span>
                </div>

                <!-- Campo Read-Only Mis Jornadas -->
                <div x-show="jornadaVisible" x-cloak id="campoMisJornadas" class="relative rounded-2xl overflow-hidden min-h-[580px] sm:min-h-[640px] flex flex-col justify-between py-6 px-3 sm:px-6 shadow-inner {{ $esSala ? 'bg-amber-950/40 border border-amber-800/50 ring-1 ring-amber-900/20' : 'border border-emerald-500/20 bg-gradient-to-b from-emerald-950/90 via-emerald-900/40 to-emerald-950/90' }}">

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
    <div id="tab-pane-resultados" x-show="tabActiva === 'resultados'" x-cloak class="tab-pane space-y-6">

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
                        x-model="jornadaResultados" @change="cargarResultados"
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
                            <img src="{{ asset($partido->equipoLocal->logo ?? 'assets/media/images/default-team.png') }}" class="w-7 h-7 object-contain shrink-0" alt="{{ $partido->equipoLocal->nombre ?? 'Local' }}">
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
                            <img src="{{ asset($partido->equipoVisitante->logo ?? 'assets/media/images/default-team.png') }}" class="w-7 h-7 object-contain shrink-0" alt="{{ $partido->equipoVisitante->nombre ?? 'Visitante' }}">
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
    <div id="tab-pane-participantes" x-show="tabActiva === 'participantes'" x-cloak class="tab-pane space-y-6">

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

<!-- ==========================================
     MODAL 1: SELECCIÓN DE JUGADOR PARA SLOT
========================================== -->
<div id="modalSeleccionJugador" x-show="modalSeleccionAbierto" x-cloak @keydown.escape.window="modalSeleccionAbierto = false" @click.self="modalSeleccionAbierto = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
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
            <button type="button" @click="modalSeleccionAbierto = false" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
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
                @click="modalSeleccionAbierto = false"
                class="py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold rounded-xl transition-all cursor-pointer ml-auto">
                Cerrar
            </button>
        </div>

    </div>
</div>

<!-- ==========================================
     MODAL 2: FICHA TÉCNICA DEL JUGADOR
========================================== -->
<div id="modalJugadorInfo" x-show="modalInfoAbierto" x-cloak @keydown.escape.window="modalInfoAbierto = false" @click.self="modalInfoAbierto = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="relative w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl shadow-zinc-950 space-y-5">

        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
            <h3 class="text-sm font-bold text-zinc-100">Ficha del Jugador</h3>
            <button type="button" @click="modalInfoAbierto = false" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
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

        <!-- Historial de Jornadas -->
        <div class="pt-4 border-t border-zinc-800/80 mt-4">
            <h5 class="text-[10px] uppercase font-semibold text-zinc-500 mb-2">Historial de Puntos</h5>
            <div id="modalInfoHistorial" class="flex items-end gap-1.5 overflow-x-auto pb-2 scrollbar-none">
                <!-- Inyectado por JS -->
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button
                type="button"
                @click="modalInfoAbierto = false"
                class="py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold rounded-xl transition-all cursor-pointer">
                Entendido
            </button>
        </div>

    </div>
</div>

<!-- ==========================================
     MODAL 3: VER ALINEACIÓN DE RIVAL
========================================== -->
<div id="modalAlineacionClasificacion" x-show="modalRivalAbierto" x-cloak @keydown.escape.window="modalRivalAbierto = false" @click.self="modalRivalAbierto = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="relative w-full max-w-xl bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl shadow-zinc-950 space-y-4">

        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
            <div>
                <h3 class="text-sm font-bold text-zinc-100">Alineación de <span id="alineacionModalUsuario" class="text-lime-400"></span></h3>
                <p class="text-[11px] text-zinc-400">Puntuación en esta jornada: <strong id="alineacionModalTotal" class="text-zinc-200">...</strong></p>
            </div>
            <button type="button" @click="modalRivalAbierto = false" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <div class="relative rounded-2xl overflow-hidden min-h-[380px] p-4 shadow-inner {{ $esSala ? 'bg-amber-950/40 border border-amber-800/50 ring-1 ring-amber-900/20' : 'border border-emerald-500/20 bg-gradient-to-b from-emerald-950/90 via-emerald-900/40 to-emerald-950/90' }}">
            <div id="alineacionModalSlots" class="relative z-10 grid grid-cols-4 sm:grid-cols-6 gap-2.5">
                @for($i = 1; $i <= $limiteSlots; $i++)
                    <div class="slot vacio bg-zinc-900/80 border border-zinc-800 rounded-xl p-2 text-center flex flex-col items-center justify-center min-h-[80px]" data-slot="{{ $i }}">
                    <div class="flex flex-col items-center justify-center p-1">
                        <small class="text-zinc-500 text-[10px]">Vacío</small>
                    </div>
                </div>
                @endfor
            </div>
        </div>

    <div class="pt-2 flex justify-end">
        <button
            type="button"
            @click="modalRivalAbierto = false"
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

<script type="application/json" id="liguilla-cuotas">@json($formacionesDisponibles)</script>
<script type="application/json" id="liguilla-plantilla">@json($plantillaUsuario)</script>
<script type="application/json" id="liguilla-alineacion">@json($alineacionActual ?? [])</script>
<script type="application/json" id="liguilla-formacion">@json($formacionActual ?? '4-3-3')</script>
<script type="application/json" id="liguilla-clasificacion">@json($clasificacion)</script>
<script type="application/json" id="liguilla-jornada-resultados">@json($jornadaSeleccionada?->id)</script>
<div data-default-player-image="{{ asset('assets/media/images/default-player.png') }}" hidden></div>

@push('scripts')
<script>
const cuotasPorFormacion = JSON.parse(document.getElementById('liguilla-cuotas').textContent);

const plantillaCompleta = JSON.parse(document.getElementById('liguilla-plantilla').textContent);
const alineacionGuardada = JSON.parse(document.getElementById('liguilla-alineacion').textContent);
const defaultPlayerImage = document.querySelector('[data-default-player-image]').dataset.defaultPlayerImage;

document.addEventListener('alpine:init', () => {
    Alpine.data('liguillaUi', () => ({
        livePointsTimer: null, livePointsUrl: null, tabActiva: 'alineacion',
        modalSeleccionAbierto: false, modalInfoAbierto: false, modalRivalAbierto: false,
        modalRivalNombre: '', modalRivalTotal: '...', modalRivalJugadores: [], jugadorFicha: null,
        slotSeleccionado: null, buscarJugador: '', formacionActiva: JSON.parse(document.getElementById('liguilla-formacion').textContent), slots: [],
        clasificacion: JSON.parse(document.getElementById('liguilla-clasificacion').textContent), modoClasificacion: 'global', subtituloClasificacion: 'Total acumulado de la liguilla',
        jornadaSeleccionada: null, jornadaVisible: false, jornadaPuntos: 0, jornadaJugadores: [], jornadaFormacion: '4-3-3',
        jornadaResultados: JSON.parse(document.getElementById('liguilla-jornada-resultados').textContent),
        init() { this.livePointsUrl = this.$root.dataset.livePointsUrl; const tab = new URLSearchParams(window.location.search).get('tab'); if (['alineacion','plantilla','clasificacion','jornadas','resultados','participantes'].includes(tab)) this.tabActiva = tab; this.inicializarSlots(); this.startLivePointsPolling(); },
        startLivePointsPolling() { if (this.livePointsUrl) { this.refreshLivePoints(); this.livePointsTimer = setInterval(() => this.refreshLivePoints(), 30000); } },
        async refreshLivePoints() { try { const r = await fetch(this.livePointsUrl, {headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}}); const d = await r.json(); if (r.ok && d.status === 'ok') this.updateClasificacion(d); } catch (e) { console.warn('No se pudo actualizar la clasificación en directo.', e); } },
        updateClasificacion(data) { this.clasificacion = data.clasificacion || []; },
        cuota(formacion) { return cuotasPorFormacion[formacion] || {portero:1, defensa:4, centrocampista:3, delantero:3}; },
        slotsPorPosicion(posicion) { return this.slots.filter(s => s.posicion === posicion); },
        jugadorDelSlot(slot) { return plantillaCompleta.find(j => Number(j.id) === Number(slot.jugador_id)); },
        nombreJugador(j) { return [j?.nombre, j?.apellido1].filter(Boolean).join(' '); },
        equipoJugador(j) { return j?.equipo?.nombre || j?.club?.nombre || 'Sin club'; },
        normalizarPosicion(pos) { const p = (pos || '').toLowerCase(); return p === 'medio' ? 'centrocampista' : p; },
        inicializarSlots() { const jugadores = Array.isArray(alineacionGuardada) ? alineacionGuardada : (alineacionGuardada?.jugadores || []); if (alineacionGuardada?.formacion) this.formacionActiva = alineacionGuardada.formacion; const grupos = {delantero:[], centrocampista:[], defensa:[], portero:[]}; jugadores.forEach(j => { const p=this.normalizarPosicion(j.posicion); if(grupos[p]) grupos[p].push(j); }); this.reconstruirSlots(grupos, this.cuota(this.formacionActiva)); },
        reconstruirSlots(grupos, cuota) { let n=1; this.slots=[]; ['delantero','centrocampista','defensa','portero'].forEach(pos => { for(let i=0;i<(cuota[pos]||0);i++) { this.slots.push({slot:n++, posicion:pos, jugador_id:grupos[pos]?.[i]?.id || null}); } }); },
        cambiarFormacion() { const grupos={}; this.slots.forEach(s => { if(s.jugador_id) (grupos[s.posicion] ||= []).push(s); }); this.reconstruirSlots(grupos, this.cuota(this.formacionActiva)); },
        abrirSeleccion(slot) { this.slotSeleccionado=slot; this.buscarJugador=''; this.modalSeleccionAbierto=true; },
        get jugadoresDisponibles() { const usados=this.slots.filter(s=>s.slot!==this.slotSeleccionado && s.jugador_id).map(s=>Number(s.jugador_id)); const pos=this.slots.find(s=>s.slot===this.slotSeleccionado)?.posicion; const q=this.buscarJugador.toLowerCase().trim(); return plantillaCompleta.filter(j=>this.normalizarPosicion(j.posicion)===pos && !usados.includes(Number(j.id)) && this.nombreJugador(j).toLowerCase().includes(q)); },
        seleccionarJugador(j) { const s=this.slots.find(s=>s.slot===this.slotSeleccionado); if(s) s.jugador_id=j.id; this.modalSeleccionAbierto=false; },
        quitarJugador(slot) { const s=this.slots.find(s=>s.slot===slot); if(s) s.jugador_id=null; },
        abrirFicha(j) { this.jugadorFicha=j; this.modalInfoAbierto=true; },
        abrirAlineacionRival(userId, jornadaId, userName) { this.modalRivalNombre=userName; this.modalRivalTotal='...'; this.modalRivalJugadores=[]; this.modalRivalAbierto=true; fetch(`/user/liguillas/{{ $liguilla->id }}/alineacion-usuario/${userId}/jornada/${jornadaId}`).then(r=>r.json()).then(d=>{this.modalRivalTotal=`${d.total_puntos ?? 0} pts`; this.modalRivalJugadores=d.jugadores||[];}); },
        async cargarClasificacion() { const url=this.$refs.selectClasificacion?.dataset.urlClasificacion; if(!url)return; const d=await (await fetch(`${url}?modo_clasificacion=${encodeURIComponent(this.modoClasificacion)}`)).json(); this.clasificacion=d.clasificacion||[]; this.subtituloClasificacion=d.modo==='global'?'Total acumulado de la liguilla':`Jornada ${d.jornada?.orden||''} ${d.jornada?.nombre||''}`.trim(); },
        async cargarMiJornada(id) { this.jornadaSeleccionada=String(id); const d=await (await fetch(`/user/liguillas/{{ $liguilla->id }}/alineacion/${id}`)).json(); this.jornadaFormacion=d.formacion||this.formacionActiva; this.jornadaJugadores=d.jugadores||[]; this.jornadaPuntos=d.total_puntos||0; this.jornadaVisible=true; },
        guardarAlineacion() { const form=this.$refs.formAlineacion; fetch(form.action,{method:'POST',body:new FormData(form),headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}}).then(async r=>{const d=await r.json();if(!r.ok)throw new Error(d.message);Swal.fire({icon:'success',title:'¡Alineación guardada!',text:d.message||'Alineación guardada.',background:'#18181b',color:'#f4f4f5'});}).catch(e=>Swal.fire({icon:'error',title:'Error al guardar',text:e.message||'No se pudo guardar.',background:'#18181b',color:'#f4f4f5'})); },
        cargarResultados() { window.location.href=`?tab=resultados&jornada_id=${encodeURIComponent(this.jornadaResultados)}`; },
        compartirEnlace(codigo) { const enlace=`{{ url('/user/unirseLiguilla') }}?codigo=${codigo}`; if(navigator.share) navigator.share({title:'Únete a mi liguilla en MiFantasy',text:'Usa mi código para unirte a la competición:',url:enlace}); else navigator.clipboard.writeText(enlace).then(()=>Swal.fire({icon:'success',title:'¡Enlace copiado!',timer:2000,showConfirmButton:false,background:'#18181b',color:'#f4f4f5'})); }
    }));
});
</script>
@endpush