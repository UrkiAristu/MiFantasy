@extends('admin.layouts.app')

@section('title', 'Detalle del Partido')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-900">
    <!-- Mensajes -->
    @if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl text-sm">
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Cabecera y Acciones -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Detalle del Partido</h1>
            <a href="{{ url('/admin/torneos/'.$partido->jornada->torneo->id.'/jornadas') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">
                {{ $partido->jornada->torneo->nombre ?? '' }} &bull; {{ $partido->jornada->nombre ?? '' }}
            </a>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openModal('editarPartidoModal')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2 rounded-xl text-sm transition-colors cursor-pointer">Editar Partido</button>
            <button type="button" onclick="openModal('eventosPartidoModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2 rounded-xl text-sm transition-colors shadow-md cursor-pointer">Gestionar Eventos</button>
            <a href="{{ url('/admin/partidos/'.$partido->id.'/eliminar') }}"
                class="bg-red-50 hover:bg-red-100 text-red-700 font-medium px-4 py-2 rounded-xl text-sm transition-colors btn-eliminar-partido cursor-pointer"
                title="Eliminar el partido"
                data-url="{{ url('/admin/partidos/'.$partido->id.'/eliminar') }}">
                Eliminar
            </a>
        </div>
    </div>

    <!-- Equipos y marcador -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6 mb-8 grid grid-cols-1 md:grid-cols-3 gap-6 items-center text-center">
        <div class="flex flex-col items-center">
            <a href="{{ url('/admin/equipos/'.$partido->equipoLocal->id) }}" class="group">
                @if($partido->equipoLocal?->logo)
                <img src="{{ asset($partido->equipoLocal->logo) }}" alt="Logo Local" class="w-20 h-20 object-contain mx-auto rounded-xl bg-white p-2 border border-zinc-200 mb-3" onerror="this.outerHTML='<div class=\'w-20 h-20 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3\'>N/A</div>'">
                @else
                <div class="w-20 h-20 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3">N/A</div>
                @endif
                <h3 class="text-lg font-bold text-zinc-900 group-hover:text-lime-600 transition-colors">{{ $partido->equipoLocal?->nombre ?? 'Equipo Local' }}</h3>
            </a>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="text-3xl font-extrabold tracking-tight text-zinc-900 mb-1">{{ $partido->goles_local ?? '-' }} &mdash; {{ $partido->goles_visitante ?? '-' }}</div>
            <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                @if(!empty($partido->fecha_partido))
                {{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y H:i') }}
                @endif
            </div>
        </div>
        <div class="flex flex-col items-center">
            <a href="{{ url('/admin/equipos/'.$partido->equipoVisitante->id) }}" class="group">
                @if($partido->equipoVisitante?->logo)
                <img src="{{ asset($partido->equipoVisitante->logo) }}" alt="Logo Visitante" class="w-20 h-20 object-contain mx-auto rounded-xl bg-white p-2 border border-zinc-200 mb-3" onerror="this.outerHTML='<div class=\'w-20 h-20 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3\'>N/A</div>'">
                @else
                <div class="w-20 h-20 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3">N/A</div>
                @endif
                <h3 class="text-lg font-bold text-zinc-900 group-hover:text-lime-600 transition-colors">{{ $partido->equipoVisitante?->nombre ?? 'Equipo Visitante' }}</h3>
            </a>
        </div>
    </div>

    <!-- Eventos (Timeline) -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6 mb-8">
        <h2 class="text-lg font-bold text-zinc-900 mb-6">Cronología de Eventos</h2>

        @php
        $eventosRaw = is_string($partido->eventos) ? json_decode($partido->eventos) : $partido->eventos;
        $eventos = collect($eventosRaw)->map(fn($e) => is_array($e) ? (object)$e : $e)->sortBy('minuto');
        @endphp

        @if($eventos && $eventos->count())
        <div class="space-y-3">
            @foreach($eventos as $evento)
            @php
            $equipoNombre = (($evento->equipo_id ?? null) == $partido->equipo_local_id) ? ($partido->equipoLocal?->nombre ?? '') : ($partido->equipoVisitante?->nombre ?? '');
            @endphp
            <div class="flex items-center justify-between p-4 bg-zinc-50 border border-zinc-200 rounded-xl">
                <div class="flex items-center gap-3">
                    <span class="font-mono font-bold text-zinc-700 bg-white px-2.5 py-1 rounded-lg border border-zinc-200 text-xs">{{ $evento->minuto ?? '' }}'</span>
                    <span class="font-semibold text-zinc-900">{{ $evento->tipo ?? '' }}</span>
                    @if(!empty($evento->jugador_nombre))
                    <span class="text-zinc-600 text-sm">
                        &mdash; <a href="{{ url('/admin/jugadores/' . ($evento->jugador_id ?? '')) }}" class="hover:underline font-medium text-zinc-900">{{ $evento->jugador_nombre ?? '' }}</a>
                    </span>
                    @endif
                    @if(!empty($evento->equipo_id))
                    <span class="text-xs text-zinc-500 bg-white px-2 py-0.5 rounded border border-zinc-200">
                        <a href="{{ url('/admin/equipos/' . $evento->equipo_id) }}" class="hover:underline">{{ ucfirst($equipoNombre) }}</a>
                    </span>
                    @endif
                </div>
                <div>
                    @if(($evento->tipo ?? '') == 'Gol')
                    <img src="{{ asset('assets/media/icons/gol.png') }}" alt="Gol" class="w-6 h-6 object-contain" onerror="this.outerHTML='<span class=\'text-xs font-bold text-emerald-600\'>⚽</span>'">
                    @elseif(($evento->tipo ?? '') == 'Asistencia')
                    <img src="{{ asset('assets/media/icons/asistencia.png') }}" alt="Asistencia" class="w-6 h-6 object-contain" onerror="this.outerHTML='<span class=\'text-xs font-bold text-blue-600\'>👟</span>'">
                    @elseif(($evento->tipo ?? '') == 'Tarjeta Roja')
                    <img src="{{ asset('assets/media/icons/tarjeta_roja.png') }}" alt="Tarjeta Roja" class="w-6 h-6 object-contain" onerror="this.outerHTML='<span class=\'text-xs font-bold text-red-600\'>🟥</span>'">
                    @elseif(($evento->tipo ?? '') == 'Tarjeta Amarilla')
                    <img src="{{ asset('assets/media/icons/tarjeta_amarilla.png') }}" alt="Tarjeta Amarilla" class="w-6 h-6 object-contain" onerror="this.outerHTML='<span class=\'text-xs font-bold text-amber-600\'>🟨</span>'">
                    @elseif(($evento->tipo ?? '') == 'Falta')
                    <img src="{{ asset('assets/media/icons/falta.png') }}" alt="Falta" class="w-6 h-6 object-contain" onerror="this.outerHTML='<span class=\'text-xs font-bold text-zinc-600\'>⚠️</span>'">
                    @elseif(($evento->tipo ?? '') == 'Parada')
                    <img src="{{ asset('assets/media/icons/parada.png') }}" alt="Parada" class="w-6 h-6 object-contain" onerror="this.outerHTML='<span class=\'text-xs font-bold text-cyan-600\'>🧤</span>'">
                    @else
                    <i class="bi bi-info-circle text-zinc-400"></i>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-zinc-500 text-center py-4">No hay eventos registrados para este partido.</p>
        @endif
    </div>

    <!-- Tablas estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        {{-- LOCAL --}}
        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
            <div class="flex items-center gap-3 mb-6">
                @if($partido->equipoLocal?->logo)
                <img src="{{ asset($partido->equipoLocal->logo) }}" alt="Logo" class="w-8 h-8 object-contain rounded-lg bg-white p-0.5 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200\'>N/A</div>'">
                @endif
                <h2 class="text-lg font-bold text-zinc-900">Estadísticas &mdash; {{ $partido->equipoLocal?->nombre ?? '' }}</h2>
            </div>
            @if ($statsLocal->count())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-700">
                    <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-3">Jugador</th>
                            <th class="py-3 px-2 text-center">Pos</th>
                            <th class="py-3 px-2 text-center">Min</th>
                            <th class="py-3 px-2 text-center">G</th>
                            <th class="py-3 px-2 text-center">A</th>
                            <th class="py-3 px-2 text-center">TA</th>
                            <th class="py-3 px-2 text-center">TR</th>
                            <th class="py-3 px-2 text-center">F</th>
                            <th class="py-3 px-2 text-center">Par</th>
                            <th class="py-3 px-3 text-end">Pts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($statsLocal as $stat)
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="py-3 px-3 font-medium text-zinc-900">{{ $stat->jugador?->nombre }} {{ $stat->jugador?->apellido1 }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->posicion ?? '—' }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->minutos }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->goles }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->asistencias }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->tarjetas_amarillas }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->tarjetas_rojas }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->faltas }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->paradas }}</td>
                            <td class="py-3 px-3 text-end font-bold text-zinc-900">{{ $stat->puntos }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-zinc-500 text-center py-4">No hay estadísticas disponibles.</p>
            @endif
        </div>

        {{-- VISITANTE --}}
        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
            <div class="flex items-center gap-3 mb-6">
                @if($partido->equipoVisitante?->logo)
                <img src="{{ asset($partido->equipoVisitante->logo) }}" alt="Logo" class="w-8 h-8 object-contain rounded-lg bg-white p-0.5 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200\'>N/A</div>'">
                @endif
                <h2 class="text-lg font-bold text-zinc-900">Estadísticas &mdash; {{ $partido->equipoVisitante?->nombre ?? '' }}</h2>
            </div>
            @if ($statsVisitante->count())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-700">
                    <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-3">Jugador</th>
                            <th class="py-3 px-2 text-center">Pos</th>
                            <th class="py-3 px-2 text-center">Min</th>
                            <th class="py-3 px-2 text-center">G</th>
                            <th class="py-3 px-2 text-center">A</th>
                            <th class="py-3 px-2 text-center">TA</th>
                            <th class="py-3 px-2 text-center">TR</th>
                            <th class="py-3 px-2 text-center">F</th>
                            <th class="py-3 px-2 text-center">Par</th>
                            <th class="py-3 px-3 text-end">Pts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($statsVisitante as $stat)
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="py-3 px-3 font-medium text-zinc-900">{{ $stat->jugador?->nombre }} {{ $stat->jugador?->apellido1 }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->posicion ?? '—' }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->minutos }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->goles }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->asistencias }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->tarjetas_amarillas }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->tarjetas_rojas }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->faltas }}</td>
                            <td class="py-3 px-2 text-center text-zinc-600">{{ $stat->paradas }}</td>
                            <td class="py-3 px-3 text-end font-bold text-zinc-900">{{ $stat->puntos }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-zinc-500 text-center py-4">No hay estadísticas disponibles.</p>
            @endif
        </div>
    </div>

    <!-- Botón para volver -->
    <div class="mt-6">
        <a href="{{ url('/admin/torneos/'.$partido->jornada->torneo->id.'/jornadas') }}" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-5 py-2.5 rounded-xl text-sm transition-colors inline-block cursor-pointer">Volver al Torneo</a>
    </div>
</div>

<!-- Modal Editar Partido -->
<div id="editarPartidoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
    <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
        <div class="flex items-center justify-between p-6 border-b border-zinc-200">
            <h5 class="text-lg font-bold text-zinc-900">Editar Partido</h5>
            <button type="button" onclick="closeModal('editarPartidoModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ url('/admin/partidos/'.$partido->id.'/editar') }}">
            @csrf
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="editarEquipoLocal" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Equipo Local</label>
                        <select name="equipo_local_id" id="editarEquipoLocal" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" required>
                            @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}" {{ $partido->equipo_local_id == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="editarEquipoVisitante" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Equipo Visitante</label>
                        <select name="equipo_visitante_id" id="editarEquipoVisitante" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" required>
                            @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}" {{ $partido->equipo_visitante_id == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="editarGolesLocal" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Goles Local</label>
                        <input type="number" name="goles_local" id="editarGolesLocal" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" value="{{ $partido->goles_local ?? '' }}" min="0">
                    </div>
                    <div>
                        <label for="editarGolesVisitante" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Goles Visitante</label>
                        <input type="number" name="goles_visitante" id="editarGolesVisitante" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" value="{{ $partido->goles_visitante ?? '' }}" min="0">
                    </div>
                    <div>
                        <label for="editarEstado" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Estado</label>
                        <select name="estado" id="editarEstado" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500">
                            <option value="programado" {{ $partido->estado == 'programado' ? 'selected' : '' }}>Programado</option>
                            <option value="jugado" {{ $partido->estado == 'jugado' ? 'selected' : '' }}>Jugado</option>
                            <option value="suspendido" {{ $partido->estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                            <option value="cancelado" {{ $partido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="editarFecha" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha</label>
                        <input type="date" name="fecha_partido" id="editarFecha" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500"
                            value="{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('Y-m-d') }}"
                            min="{{ \Carbon\Carbon::parse($partido->jornada->torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($partido->jornada->torneo->fecha_fin)->format('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label for="editarHora" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Hora</label>
                        <input type="time" name="hora_partido" id="editarHora" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500"
                            value="{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200">
                <button type="button" onclick="closeModal('editarPartidoModal')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2 rounded-xl text-sm transition-colors cursor-pointer">Cancelar</button>
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md cursor-pointer">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Gestionar Eventos -->
<div id="eventosPartidoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
    <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl relative">
        <div class="flex items-center justify-between p-6 border-b border-zinc-200">
            <h5 class="text-lg font-bold text-zinc-900">Gestionar Eventos</h5>
            <button type="button" onclick="closeModal('eventosPartidoModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ url('/admin/partidos/'.$partido->id.'/eventos/crear') }}">
            @csrf
            <input type="hidden" name="partido_id" id="eventosPartidoId" value="{{ $partido->id }}">
            <input type="hidden" name="eventos_json" id="eventos_json">

            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="tipoEvento" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Tipo</label>
                        <select id="tipoEvento" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500">
                            <option value="">Selecciona tipo</option>
                            <option value="Gol">Gol</option>
                            <option value="Asistencia">Asistencia</option>
                            <option value="Falta">Falta</option>
                            <option value="Tarjeta Amarilla">Tarjeta Amarilla</option>
                            <option value="Tarjeta Roja">Tarjeta Roja</option>
                            <option value="Parada">Parada</option>
                        </select>
                    </div>
                    <div>
                        <label for="minutoEvento" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Minuto</label>
                        <input type="number" id="minutoEvento" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" min="0" max="120">
                    </div>
                    <div>
                        <label for="equipoEvento" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Equipo</label>
                        <select id="equipoEvento" name="equipo_id" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500">
                            <option value="">Selecciona equipo</option>
                            @foreach ($partido->equipos as $equipo)
                            <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="jugadorEvento" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Jugador</label>
                        <select id="jugadorEvento" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500">
                            <option value="">Selecciona jugador</option>
                            @foreach ($jugadores as $jugador)
                            <option value="{{ $jugador->id }}" data-equipo-id="{{ $jugador->equipo_id }}">
                                {{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <button type="button" onclick="agregarEvento()" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md cursor-pointer">Añadir Evento</button>
                </div>

                <div class="border-t border-zinc-200 pt-4">
                    <h6 class="text-sm font-bold text-zinc-900 mb-3">Eventos actuales en la sesión:</h6>
                    <ul class="space-y-2" id="listaEventos"></ul>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200">
                <button type="button" onclick="location.reload();" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md cursor-pointer">Actualizar Página</button>
            </div>
        </form>
    </div>
</div>

@php
$eventosDecoded = is_string($partido->eventos) ? json_decode($partido->eventos, true) : $partido->eventos;
$eventosArray = collect($eventosDecoded ?? [])->map(fn($e) => is_object($e) ? (array)$e : $e)->sortBy('minuto')->values()->all();
@endphp

@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    let jugadoresLocal = <?= json_encode($jugadoresLocal) ?>,
        jugadoresVisitante = <?= json_encode($jugadoresVisitante) ?>,
        jugadores = <?= json_encode($jugadores) ?>;

    const jugadoresData = jugadores.map(j => ({
        id: j.id,
        nombre: j.nombre,
        apellido1: j.apellido1,
        apellido2: j.apellido2,
        equipo_id: j.equipo_id
    }));

    const selectEquipo = document.getElementById('equipoEvento');
    const selectJugador = document.getElementById('jugadorEvento');

    function filtrarJugadoresPorEquipo(equipoId) {
        selectJugador.innerHTML = '<option value="">Selecciona jugador</option>';

        if (!equipoId) {
            jugadoresData.forEach(j => {
                const opt = document.createElement('option');
                opt.value = j.id;
                opt.textContent = j.nombre + ' ' + j.apellido1 + ' ' + j.apellido2;
                selectJugador.appendChild(opt);
            });
            return;
        }

        jugadoresData.forEach(j => {
            if (j.equipo_id == equipoId) {
                const opt = document.createElement('option');
                opt.value = j.id;
                opt.textContent = j.nombre + ' ' + j.apellido1 + ' ' + j.apellido2;
                selectJugador.appendChild(opt);
            }
        });
    }

    function seleccionarEquipoPorJugador(jugadorId) {
        const jugador = jugadoresData.find(j => j.id == jugadorId);
        if (!jugador) return;

        selectEquipo.value = jugador.equipo_id;
        filtrarJugadoresPorEquipo(jugador.equipo_id);
        selectJugador.value = jugadorId;
    }

    if(selectEquipo) {
        selectEquipo.addEventListener('change', () => {
            filtrarJugadoresPorEquipo(selectEquipo.value);
        });
    }

    if(selectJugador) {
        selectJugador.addEventListener('change', () => {
            seleccionarEquipoPorJugador(parseInt(selectJugador.value));
        });
    }

    let eventos = [];

    function agregarEvento() {
        const tipo = document.getElementById('tipoEvento').value;
        const minuto = document.getElementById('minutoEvento').value;
        const equipoId = document.getElementById('equipoEvento').value;
        const jugadorId = document.getElementById('jugadorEvento').value;

        if (!tipo || !minuto || !equipoId || !jugadorId) {
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor completa todos los campos.',
                background: '#ffffff',
                color: '#18181b',
                confirmButtonColor: '#a3e635'
            });
            return;
        }

        const jugador = jugadoresData.find(j => j.id == jugadorId);
        const equipo = selectEquipo.options[selectEquipo.selectedIndex].text;

        const evento = {
            tipo: tipo,
            minuto: parseInt(minuto),
            equipo_id: parseInt(equipoId),
            equipo_nombre: equipo,
            jugador_id: parseInt(jugadorId),
            jugador_nombre: jugador.nombre + ' ' + jugador.apellido1 + ' ' + jugador.apellido2
        };

        eventos.push(evento);
        guardarEventosAjax();
    }

    function actualizarListaEventos() {
        const lista = document.getElementById('listaEventos');
        if(!lista) return;
        lista.innerHTML = '';

        eventos.forEach((evento, index) => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between p-3 bg-zinc-50 border border-zinc-200 rounded-xl text-sm';
            li.innerHTML = `<span><strong>[${evento.minuto}']</strong> ${evento.tipo} &mdash; ${evento.jugador_nombre} (${evento.equipo_nombre})</span>`;

            const btnEliminar = document.createElement('button');
            btnEliminar.type = 'button';
            btnEliminar.className = 'bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors cursor-pointer';
            btnEliminar.innerHTML = 'Eliminar';
            btnEliminar.onclick = function() {
                eventos.splice(index, 1);
                guardarEventosAjax();
            };

            li.appendChild(btnEliminar);
            lista.appendChild(li);
        });

        const eventosJsonInput = document.getElementById('eventos_json');
        if(eventosJsonInput) {
            eventosJsonInput.value = JSON.stringify(eventos);
        }
    }

    function guardarEventosAjax() {
        const partidoId = document.getElementById('eventosPartidoId').value || '{{ $partido->id }}';

        fetch(`/admin/partidos/${partidoId}/eventos/agregar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    eventos: eventos
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    actualizarListaEventos();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron guardar los eventos.',
                        background: '#ffffff',
                        color: '#18181b',
                        confirmButtonColor: '#a3e635'
                    });
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión.',
                    background: '#ffffff',
                    color: '#18181b',
                    confirmButtonColor: '#a3e635'
                });
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        eventos = <?= json_encode($eventosArray) ?> || [];
        actualizarListaEventos();

        const createAndSubmitDeleteForm = (url) => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        };

        const botonesEliminarPartido = document.querySelectorAll('.btn-eliminar-partido');
        botonesEliminarPartido.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este partido?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    background: '#ffffff',
                    color: '#18181b',
                    showCancelButton: true,
                    confirmButtonColor: '#a3e635',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        createAndSubmitDeleteForm(url);
                    }
                });
            });
        });
    });
</script>
@endpush