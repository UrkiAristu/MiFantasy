@extends('admin.layouts.app')

@section('title', 'Jornadas del Torneo')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-200">
    <div class="text-center mb-6">
        <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="inline-block group">
            @if($torneo->logo)
            <img src="{{ asset($torneo->logo) }}" alt="Logo Torneo" class="w-20 h-20 object-contain mx-auto mb-3 rounded-xl bg-zinc-950 p-2 border border-zinc-800">
            @endif
            <h1 class="text-2xl font-extrabold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">{{ $torneo->nombre }}</h1>
        </a>
    </div>

    <!-- Errores -->
    @if ($errors->any())
    <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <!-- Selector de vista (toggle) a la izquierda -->
        <div class="flex items-center gap-2">
            <button id="btnVistaTabs" type="button" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium" title="Vista pestañas">
                <i class="bi bi-card-list"><span>Pestañas</span></i>
            </button>
            <button id="btnVistaCards" type="button" class="bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium" title="Vista cards">
                <i class="bi bi-grid-3x3-gap"><span>Cuadrícula</span></i>
            </button>
        </div>

        <!-- Botón Crear Jornada a la derecha -->
        <button type="button" onclick="openModal('modalCrearJornada')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Crear Jornada
        </button>
    </div>

    <!-- VISTA PESTAÑAS -->
    <div id="vistaTabs">
        <div class="flex flex-wrap gap-2 border-b border-zinc-800 pb-4 mb-6" role="tablist">
            @foreach($torneo->jornadas as $index => $jornada)
            <button
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors jornada-tab-btn {{ $index == 0 ? 'bg-lime-400 text-zinc-950 shadow-md' : 'bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-zinc-200' }}"
                data-target="jornada-{{ $jornada->id }}"
                type="button"
                role="tab">
                {{ $jornada->nombre }}
            </button>
            @endforeach
        </div>

        <div class="space-y-6">
            @foreach($torneo->jornadas as $index => $jornada)
            <div
                class="bg-zinc-900/80 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm jornada-content-pane {{ $index == 0 ? '' : 'hidden' }}"
                id="jornada-{{ $jornada->id }}"
                role="tabpanel">

                <div class="flex flex-col md:flex-row md:items-center justify-between p-6 border-b border-zinc-800 gap-4 bg-zinc-950/40">
                    <div>
                        <h3 class="text-lg font-bold text-zinc-100 mb-1">{{ $jornada->nombre }}</h3>
                        <p class="text-xs text-zinc-400">
                            {{ $jornada->fecha_inicio ? \Carbon\Carbon::parse($jornada->fecha_inicio)->format('d/m/Y') : '-' }} -
                            {{ $jornada->fecha_fin ? \Carbon\Carbon::parse($jornada->fecha_fin)->format('d/m/Y') : '-' }}
                            · Cierre alineaciones:
                            @if($jornada->fecha_cierre_alineaciones)
                            <strong class="text-zinc-200">{{ $jornada->fecha_cierre_alineaciones->format('d/m/Y H:i') }}</strong>
                            @else
                            -
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="abrirModalCrearPartido('{{ $jornada->id }}', '{{ $jornada->nombre }}')"
                            class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-3 py-1.5 rounded-xl text-xs transition-colors shadow-sm inline-flex items-center gap-1">
                            <i class="bi bi-plus-lg"></i> Añadir Partido
                        </button>
                        <button type="button"
                            onclick="abrirModalEditarJornada('{{ $jornada->id }}', '{{ $jornada->nombre }}', '{{ $jornada->fecha_inicio }}', '{{ $jornada->fecha_fin }}', '{{ $jornada->fecha_cierre_alineaciones }}')"
                            class="bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    @if ($jornada->partidos->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-300">
                            <thead class="bg-zinc-950/80 text-zinc-400 uppercase text-xs tracking-wider border-b border-zinc-800">
                                <tr>
                                    <th class="py-3 px-4 text-center">#</th>
                                    <th class="py-3 px-4 text-center">Local</th>
                                    <th class="py-3 px-4 text-center">Visitante</th>
                                    <th class="py-3 px-4 text-center">Fecha</th>
                                    <th class="py-3 px-4 text-center">Hora</th>
                                    <th class="py-3 px-4 text-center">Marcador</th>
                                    <th class="py-3 px-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800/60">
                                @foreach($jornada->partidos as $indexP => $partido)
                                <tr class="hover:bg-zinc-800/30 transition-colors">
                                    <td class="py-3 px-4 text-center font-mono text-zinc-400">{{ $indexP + 1 }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-zinc-100">{{ $partido->equipoLocal->nombre }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-zinc-100">{{ $partido->equipoVisitante->nombre }}</td>
                                    <td class="py-3 px-4 text-center text-zinc-300 text-xs">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-center text-zinc-300 text-xs">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-lime-400">
                                        {{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-1">
                                        <a href="{{ url('/admin/partidos/'.$partido->id) }}" class="inline-flex items-center gap-1 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <button type="button" class="inline-flex items-center gap-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                            onclick="abrirModalResultado('{{ $partido->id }}', '{{ $partido->equipoLocal->nombre }}', '{{ $partido->equipoVisitante->nombre }}', '{{ $partido->goles_local }}', '{{ $partido->goles_visitante }}')">
                                            <i class="bi bi-pencil-square"></i> Resultado
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-6 text-zinc-500 text-sm">No hay partidos programados en esta jornada.</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- VISTA CARDS -->
    <div id="vistaCards" class="hidden space-y-6">
        @forelse ($torneo->jornadas as $jornada)
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between p-6 border-b border-zinc-800 gap-4 bg-zinc-950/40">
                <div>
                    <h3 class="text-lg font-bold text-zinc-100 mb-1">{{ $jornada->nombre }}</h3>
                    <p class="text-xs text-zinc-400">
                        {{ $jornada->fecha_inicio ? \Carbon\Carbon::parse($jornada->fecha_inicio)->format('d/m/Y') : '-' }} -
                        {{ $jornada->fecha_fin ? \Carbon\Carbon::parse($jornada->fecha_fin)->format('d/m/Y') : '-' }}
                        · Cierre alineaciones:
                        @if($jornada->fecha_cierre_alineaciones)
                        <strong class="text-zinc-200">{{ $jornada->fecha_cierre_alineaciones->format('d/m/Y H:i') }}</strong>
                        @else
                        -
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button"
                        onclick="abrirModalCrearPartido('{{ $jornada->id }}', '{{ $jornada->nombre }}')"
                        class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-3 py-1.5 rounded-xl text-xs transition-colors shadow-sm inline-flex items-center gap-1">
                        <i class="bi bi-plus-lg"></i> Añadir Partido
                    </button>
                    <button type="button"
                        onclick="abrirModalEditarJornada('{{ $jornada->id }}', '{{ $jornada->nombre }}', '{{ $jornada->fecha_inicio }}', '{{ $jornada->fecha_fin }}', '{{ $jornada->fecha_cierre_alineaciones }}')"
                        class="bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
            </div>
            <div class="p-6">
                @if ($jornada->partidos->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-300">
                        <thead class="bg-zinc-950/80 text-zinc-400 uppercase text-xs tracking-wider border-b border-zinc-800">
                            <tr>
                                <th class="py-3 px-4 text-center">#</th>
                                <th class="py-3 px-4 text-center">Local</th>
                                <th class="py-3 px-4 text-center">Visitante</th>
                                <th class="py-3 px-4 text-center">Fecha</th>
                                <th class="py-3 px-4 text-center">Hora</th>
                                <th class="py-3 px-4 text-center">Marcador</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/60">
                            @foreach($jornada->partidos as $index => $partido)
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3 px-4 text-center font-mono text-zinc-400">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 text-center font-bold text-zinc-100">{{ $partido->equipoLocal->nombre }}</td>
                                <td class="py-3 px-4 text-center font-bold text-zinc-100">{{ $partido->equipoVisitante->nombre }}</td>
                                <td class="py-3 px-4 text-center text-zinc-300 text-xs">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 text-center text-zinc-300 text-xs">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</td>
                                <td class="py-3 px-4 text-center font-mono font-bold text-lime-400">
                                    {{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center space-x-1">
                                    <a href="{{ url('/admin/partidos/'.$partido->id) }}" class="inline-flex items-center gap-1 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <button type="button" class="inline-flex items-center gap-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                        onclick="abrirModalResultado('{{ $partido->id }}', '{{ $partido->equipoLocal->nombre }}', '{{ $partido->equipoVisitante->nombre }}', '{{ $partido->goles_local }}', '{{ $partido->goles_visitante }}')">
                                        <i class="bi bi-pencil-square"></i> Resultado
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-6 text-zinc-500 text-sm">No hay partidos programados en esta jornada.</div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-8 text-center text-zinc-400">Este torneo aún no tiene jornadas creadas.</div>
        @endforelse
    </div>

    <!-- Orden de jornadas -->
    @if ($torneo->jornadas->count())
    <div class="mt-10 bg-zinc-900/80 border border-zinc-800 rounded-2xl shadow-xl p-6 backdrop-blur-sm">
        <h3 class="text-lg font-bold text-zinc-100 mb-4">Orden de jornadas en {{ $torneo->nombre }}</h3>

        <form id="formOrdenJornadas" method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/jornadas/guardarOrdenJornadas') }}" class="space-y-4">
            @csrf
            <ul id="lista-jornadas" class="space-y-2">
                @foreach ($torneo->jornadas as $jornada)
                <li class="bg-zinc-950/80 border border-zinc-800 rounded-xl p-4 flex items-center justify-between cursor-grab active:cursor-grabbing" data-id="{{ $jornada->id }}">
                    <div>
                        <strong class="text-zinc-100">{{ $jornada->nombre }}</strong>
                        <p class="text-xs text-zinc-400 mt-0.5">
                            {{ $jornada->fecha_inicio ? \Carbon\Carbon::parse($jornada->fecha_inicio)->format('d/m/Y') : '-' }}
                            –
                            {{ $jornada->fecha_fin ? \Carbon\Carbon::parse($jornada->fecha_fin)->format('d/m/Y') : '-' }}
                            · Cierre alineaciones:
                            @if($jornada->fecha_cierre_alineaciones)
                            {{ $jornada->fecha_cierre_alineaciones->format('d/m/Y H:i') }}
                            @else
                            -
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <i class="bi bi-list text-zinc-500 text-xl"></i>
                        <button type="button" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-eliminar-jornada" title="Eliminar jornada"
                            data-url="{{ url('/admin/jornadas/'.$jornada->id.'/eliminar') }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </li>
                @endforeach
            </ul>
            <input type="hidden" name="orden" id="ordenInput">
            <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar orden</button>
        </form>
    </div>
    @endif
</div>

<!-- Modal único para crear partido -->
<div id="modalCrearPartido" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
        <form method="POST" id="formCrearPartido">
            @csrf
            <div class="flex items-center justify-between p-6 border-b border-zinc-800">
                <h5 class="text-lg font-bold text-zinc-100" id="modalCrearPartidoTitle">Nuevo Partido</h5>
                <button type="button" onclick="closeModal('modalCrearPartido')" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Equipo Local</label>
                    <select class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" id="equipo_local_id" name="equipo_local_id" required>
                        <option value="">Selecciona un equipo local</option>
                        @foreach ($torneo->equipos as $equipo)
                        <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Equipo Visitante</label>
                    <select class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" id="equipo_visitante_id" name="equipo_visitante_id" required>
                        <option value="">Selecciona un equipo visitante</option>
                        @foreach ($torneo->equipos as $equipo)
                        <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha</label>
                    <input type="date" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="fecha_partido" required
                        min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                        max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Hora</label>
                    <input type="time" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="hora_partido">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-800 bg-zinc-950/40">
                <button type="button" onclick="closeModal('modalCrearPartido')" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal crear jornada -->
<div id="modalCrearJornada" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
        <form action="{{ url('/admin/torneos/'.$torneo->id.'/jornadas/crear') }}" method="POST">
            @csrf
            <input type="hidden" name="torneo_id" value="{{ $torneo->id }}">
            <div class="flex items-center justify-between p-6 border-b border-zinc-800">
                <h5 class="text-lg font-bold text-zinc-100">Crear Jornada</h5>
                <button type="button" onclick="closeModal('modalCrearJornada')" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Nombre de la Jornada</label>
                    <input type="text" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="nombre" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha de Inicio</label>
                        <input type="date" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="fecha_inicio"
                            min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha de Fin</label>
                        <input type="date" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="fecha_fin"
                            min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha y hora de cierre de alineaciones</label>
                    <input type="datetime-local" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="fecha_cierre_alineaciones"
                        min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->subDay()->format('Y-m-d\TH:i') }}"
                        max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->endOfDay()->format('Y-m-d\TH:i') }}">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-800 bg-zinc-950/40">
                <button type="button" onclick="closeModal('modalCrearJornada')" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Crear</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal editar jornada -->
<div id="modalEditarJornada" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
        <form action="" method="POST" id="formEditarJornada">
            @csrf
            <input type="hidden" name="jornada_id" id="editarJornadaId">
            <div class="flex items-center justify-between p-6 border-b border-zinc-800">
                <h5 class="text-lg font-bold text-zinc-100">Editar Jornada</h5>
                <button type="button" onclick="closeModal('modalEditarJornada')" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Nombre</label>
                    <input type="text" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="nombre" id="editarNombre" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha Inicio</label>
                        <input type="date" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="fecha_inicio" id="editarFechaInicio"
                            min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha Fin</label>
                        <input type="date" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="fecha_fin" id="editarFechaFin"
                            min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->format('Y-m-d') }}">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha y hora de cierre de alineaciones</label>
                    <input type="datetime-local" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="fecha_cierre_alineaciones" id="editarFechaCierre"
                        min="{{ \Carbon\Carbon::parse($torneo->fecha_inicio)->subDay()->format('Y-m-d\TH:i') }}"
                        max="{{ \Carbon\Carbon::parse($torneo->fecha_fin)->endOfDay()->format('Y-m-d\TH:i') }}">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-800 bg-zinc-950/40">
                <button type="button" onclick="closeModal('modalEditarJornada')" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal resultado -->
<div id="resultadoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative">
        <form method="POST" action="{{ url('/admin/partidos/actualizar-resultado') }}">
            @csrf
            <input type="hidden" name="partido_id" id="modalPartidoId">
            <div class="flex items-center justify-between p-6 border-b border-zinc-800">
                <h5 class="text-lg font-bold text-zinc-100">Editar Resultado</h5>
                <button type="button" onclick="closeModal('resultadoModal')" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <p id="modalEquipos" class="font-bold text-center text-zinc-200"></p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Goles Local</label>
                        <input type="number" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="goles_local" id="modalGolesLocal" min="0" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Goles Visitante</label>
                        <input type="number" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" name="goles_visitante" id="modalGolesVisitante" min="0" required>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-800 bg-zinc-950/40">
                <button type="button" onclick="closeModal('resultadoModal')" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('hidden');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    }

    function abrirModalCrearPartido(jornadaId, nombreJornada) {
        document.getElementById('modalCrearPartidoTitle').textContent = 'Nuevo Partido - ' + nombreJornada;
        document.getElementById('formCrearPartido').action = `/admin/jornadas/${jornadaId}/partidos/crear`;
        openModal('modalCrearPartido');
    }

    function abrirModalEditarJornada(id, nombre, fechaInicio, fechaFin, fechaCierre) {
        document.getElementById('editarJornadaId').value = id;
        document.getElementById('editarNombre').value = nombre;
        document.getElementById('editarFechaInicio').value = fechaInicio;
        document.getElementById('editarFechaFin').value = fechaFin;
        document.getElementById('editarFechaCierre').value = fechaCierre ? fechaCierre.replace(' ', 'T').slice(0, 16) : '';
        document.getElementById('formEditarJornada').action = `/admin/jornadas/${id}/editar`;
        openModal('modalEditarJornada');
    }

    function abrirModalResultado(partidoId, local, visitante, golesLocal, golesVisitante) {
        document.getElementById('modalPartidoId').value = partidoId;
        document.getElementById('modalEquipos').textContent = local + ' vs ' + visitante;
        document.getElementById('modalGolesLocal').value = golesLocal !== 'null' ? golesLocal : '';
        document.getElementById('modalGolesVisitante').value = golesVisitante !== 'null' ? golesVisitante : '';
        openModal('resultadoModal');
    }

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('modalCrearPartido');
            closeModal('modalCrearJornada');
            closeModal('modalEditarJornada');
            closeModal('resultadoModal');
        }
    });

    // Pestañas UI manual toggle
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.jornada-tab-btn');
        const panes = document.querySelectorAll('.jornada-content-pane');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                tabBtns.forEach(b => {
                    b.classList.remove('bg-lime-400', 'text-zinc-950', 'shadow-md');
                    b.classList.add('bg-zinc-900', 'border', 'border-zinc-800', 'text-zinc-400');
                });
                this.classList.remove('bg-zinc-900', 'border', 'border-zinc-800', 'text-zinc-400');
                this.classList.add('bg-lime-400', 'text-zinc-950', 'shadow-md');

                panes.forEach(pane => {
                    if (pane.id === targetId) {
                        pane.classList.remove('hidden');
                    } else {
                        pane.classList.add('hidden');
                    }
                });
            });
        });

        // Toggle vista
        const btnTabs = document.getElementById('btnVistaTabs');
        const btnCards = document.getElementById('btnVistaCards');
        const vistaTabs = document.getElementById('vistaTabs');
        const vistaCards = document.getElementById('vistaCards');

        btnTabs.addEventListener('click', () => {
            vistaTabs.classList.remove('hidden');
            vistaCards.classList.add('hidden');
            btnTabs.className = 'bg-zinc-800 hover:bg-zinc-700 text-zinc-200 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium';
            btnCards.className = 'bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium';
        });

        btnCards.addEventListener('click', () => {
            vistaCards.classList.remove('hidden');
            vistaTabs.classList.add('hidden');
            btnCards.className = 'bg-zinc-800 hover:bg-zinc-700 text-zinc-200 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium';
            btnTabs.className = 'bg-zinc-800/60 hover:bg-zinc-700 text-zinc-400 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium';
        });

        // Sortable
        const lista = document.getElementById('lista-jornadas');
        if (lista) {
            Sortable.create(lista, {
                animation: 150,
            });
        }

        const formOrden = document.getElementById('formOrdenJornadas');
        if (formOrden) {
            formOrden.addEventListener('submit', function() {
                const orden = [];
                lista.querySelectorAll('li').forEach((li, index) => {
                    orden.push({
                        id: li.dataset.id,
                        orden: index + 1
                    });
                });
                document.getElementById('ordenInput').value = JSON.stringify(orden);
            });
        }

        // Eliminar jornada
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-jornada');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar esta jornada?',
                    text: "Los partidos se eliminarán automáticamente.",
                    icon: 'warning',
                    background: '#18181b',
                    color: '#f4f4f5',
                    showCancelButton: true,
                    confirmButtonColor: '#a3e635',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                    }
                });
            });
        });
    });
</script>
@endpush
