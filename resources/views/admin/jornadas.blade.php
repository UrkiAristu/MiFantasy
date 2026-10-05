@extends('admin.layouts.app')

@section('title', 'Jornadas del Torneo')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-900">
    <div class="text-center mb-6">
        <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="inline-block group">
            @if($torneo->logo)
            <img src="{{ asset($torneo->logo) }}" alt="Logo Torneo" class="w-20 h-20 object-contain mx-auto mb-3 rounded-xl bg-white p-2 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-20 h-20 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3\'>N/A</div>'">
            @endif
            <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900 group-hover:text-lime-600 transition-colors">{{ $torneo->nombre }}</h1>
        </a>
    </div>

    <!-- Errores -->
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

    <div class="flex items-center justify-between mb-6">
        <!-- Selector de vista (toggle) a la izquierda -->
        <div class="flex items-center gap-2">
            <button id="btnVistaTabs" type="button" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-900 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium" title="Vista pestañas">
                <i class="bi bi-card-list"><span>Pestañas</span></i>
            </button>
            <button id="btnVistaCards" type="button" class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-600 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium" title="Vista cards">
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
        <div class="flex flex-wrap gap-2 border-b border-zinc-200 pb-4 mb-6" role="tablist">
            @foreach($torneo->jornadas as $index => $jornada)
            <button
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors jornada-tab-btn {{ $index == 0 ? 'bg-lime-400 text-zinc-950 shadow-md' : 'bg-white border border-zinc-200 text-zinc-600 hover:text-zinc-900' }}"
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
                class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden jornada-content-pane {{ $index == 0 ? '' : 'hidden' }}"
                id="jornada-{{ $jornada->id }}"
                role="tabpanel">

                <div class="flex flex-col md:flex-row md:items-center justify-between p-6 border-b border-zinc-200 gap-4 bg-zinc-50">
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 mb-1">{{ $jornada->nombre }}</h3>
                        <p class="text-xs text-zinc-600">
                            {{ $jornada->fecha_inicio ? \Carbon\Carbon::parse($jornada->fecha_inicio)->format('d/m/Y') : '-' }} -
                            {{ $jornada->fecha_fin ? \Carbon\Carbon::parse($jornada->fecha_fin)->format('d/m/Y') : '-' }}
                            · Cierre alineaciones:
                            @if($jornada->fecha_cierre_alineaciones)
                            <strong class="text-zinc-900">{{ $jornada->fecha_cierre_alineaciones->format('d/m/Y H:i') }}</strong>
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
                            class="bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors border border-amber-200">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    @if ($jornada->partidos->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-700">
                            <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
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
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($jornada->partidos as $indexP => $partido)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $indexP + 1 }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-zinc-900">{{ $partido->equipoLocal->nombre }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-zinc-900">{{ $partido->equipoVisitante->nombre }}</td>
                                    <td class="py-3 px-4 text-center text-zinc-600 text-xs">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-center text-zinc-600 text-xs">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('H:i') }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-zinc-900">
                                        {{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-1">
                                        <a href="{{ url('/admin/partidos/'.$partido->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <button type="button" class="inline-flex items-center gap-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors"
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
        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between p-6 border-b border-zinc-200 gap-4 bg-zinc-50">
                <div>
                    <h3 class="text-lg font-bold text-zinc-900 mb-1">{{ $jornada->nombre }}</h3>
                    <p class="text-xs text-zinc-600">
                        {{ $jornada->fecha_inicio ? \Carbon\Carbon::parse($jornada->fecha_inicio)->format('d/m/Y') : '-' }} -
                        {{ $jornada->fecha_fin ? \Carbon\Carbon::parse($jornada->fecha_fin)->format('d/m/Y') : '-' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="abrirModalCrearPartido('{{ $jornada->id }}', '{{ $jornada->nombre }}')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-3 py-1.5 rounded-xl text-xs transition-colors shadow-sm inline-flex items-center gap-1">
                        <i class="bi bi-plus-lg"></i> Añadir Partido
                    </button>
                    <button type="button" onclick="abrirModalEditarJornada('{{ $jornada->id }}', '{{ $jornada->nombre }}', '{{ $jornada->fecha_inicio }}', '{{ $jornada->fecha_fin }}', '{{ $jornada->fecha_cierre_alineaciones }}')" class="bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors border border-amber-200">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </div>
            </div>
            <div class="p-6">
                @if ($jornada->partidos->count())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($jornada->partidos as $partido)
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4 flex items-center justify-between">
                        <div class="text-left flex-1">
                            <span class="block font-bold text-zinc-900 text-sm">{{ $partido->equipoLocal->nombre }}</span>
                            <span class="block font-bold text-zinc-900 text-sm">{{ $partido->equipoVisitante->nombre }}</span>
                            <span class="block text-xs text-zinc-500 mt-1">{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="text-center px-4 font-mono font-bold text-zinc-900 text-lg">
                            {{ $partido->goles_local ?? '-' }} : {{ $partido->goles_visitante ?? '-' }}
                        </div>
                        <div class="flex flex-col gap-1">
                            <button type="button" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors" onclick="abrirModalResultado('{{ $partido->id }}', '{{ $partido->equipoLocal->nombre }}', '{{ $partido->equipoVisitante->nombre }}', '{{ $partido->goles_local }}', '{{ $partido->goles_visitante }}')">
                                Resultado
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-zinc-500 text-sm">No hay partidos en esta jornada.</div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white border border-zinc-200 rounded-2xl p-8 text-center text-zinc-500">No hay jornadas creadas.</div>
        @endforelse
    </div>

    <!-- MODAL CREAR JORNADA -->
    <div id="modalCrearJornada" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
            <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/jornadas/crear') }}">
                @csrf
                <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                    <h5 class="text-lg font-bold text-zinc-900">Crear Jornada</h5>
                    <button type="button" onclick="closeModal('modalCrearJornada')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="nombre_jornada" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre de la Jornada</label>
                        <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="nombre_jornada" name="nombre" placeholder="Ej. Jornada 1" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="fecha_inicio_jornada" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha Inicio</label>
                            <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="fecha_inicio_jornada" name="fecha_inicio">
                        </div>
                        <div>
                            <label for="fecha_fin_jornada" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha Fin</label>
                            <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="fecha_fin_jornada" name="fecha_fin">
                        </div>
                    </div>
                    <div>
                        <label for="fecha_cierre_alineaciones" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Cierre de Alineaciones</label>
                        <input type="datetime-local" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="fecha_cierre_alineaciones" name="fecha_cierre_alineaciones">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200 bg-zinc-50">
                    <button type="button" onclick="closeModal('modalCrearJornada')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Crear Jornada</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDITAR JORNADA -->
    <div id="modalEditarJornada" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
            <form id="formEditarJornada" method="POST">
                @csrf
                @method('PUT')
                <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                    <h5 class="text-lg font-bold text-zinc-900">Editar Jornada</h5>
                    <button type="button" onclick="closeModal('modalEditarJornada')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="edit_nombre_jornada" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre de la Jornada</label>
                        <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="edit_nombre_jornada" name="nombre" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_fecha_inicio_jornada" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha Inicio</label>
                            <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="edit_fecha_inicio_jornada" name="fecha_inicio">
                        </div>
                        <div>
                            <label for="edit_fecha_fin_jornada" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha Fin</label>
                            <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="edit_fecha_fin_jornada" name="fecha_fin">
                        </div>
                    </div>
                    <div>
                        <label for="edit_fecha_cierre_alineaciones" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Cierre de Alineaciones</label>
                        <input type="datetime-local" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="edit_fecha_cierre_alineaciones" name="fecha_cierre_alineaciones">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200 bg-zinc-50">
                    <button type="button" onclick="closeModal('modalEditarJornada')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CREAR PARTIDO -->
    <div id="modalCrearPartido" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
            <form id="formCrearPartido" method="POST">
                @csrf
                <input type="hidden" id="partido_jornada_id" name="jornada_id">
                <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                    <h5 class="text-lg font-bold text-zinc-900" id="tituloModalPartido">Añadir Partido</h5>
                    <button type="button" onclick="closeModal('modalCrearPartido')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="equipo_local_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Equipo Local</label>
                        <select class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="equipo_local_id" name="equipo_local_id" required>
                            <option value="">Selecciona equipo local</option>
                            @foreach($equiposDisponibles ?? [] as $eq)
                            <option value="{{ $eq->id }}">{{ $eq->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="equipo_visitante_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Equipo Visitante</label>
                        <select class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="equipo_visitante_id" name="equipo_visitante_id" required>
                            <option value="">Selecciona equipo visitante</option>
                            @foreach($equiposDisponibles ?? [] as $eq)
                            <option value="{{ $eq->id }}">{{ $eq->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="fecha_partido" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha y Hora del Partido</label>
                        <input type="datetime-local" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="fecha_partido" name="fecha_partido" required>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200 bg-zinc-50">
                    <button type="button" onclick="closeModal('modalCrearPartido')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar Partido</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ACTUALIZAR RESULTADO -->
    <div id="modalResultado" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative">
            <form id="formResultado" method="POST">
                @csrf
                @method('PUT')
                <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                    <h5 class="text-lg font-bold text-zinc-900" id="tituloModalResultado">Actualizar Resultado</h5>
                    <button type="button" onclick="closeModal('modalResultado')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="goles_local" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1" id="labelLocal">Local</label>
                            <input type="number" min="0" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm font-bold text-center focus:outline-none focus:border-lime-500" id="goles_local" name="goles_local" required>
                        </div>
                        <div>
                            <label for="goles_visitante" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1" id="labelVisitante">Visitante</label>
                            <input type="number" min="0" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm font-bold text-center focus:outline-none focus:border-lime-500" id="goles_visitante" name="goles_visitante" required>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200 bg-zinc-50">
                    <button type="button" onclick="closeModal('modalResultado')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnTabs = document.getElementById('btnVistaTabs');
        const btnCards = document.getElementById('btnVistaCards');
        const vistaTabs = document.getElementById('vistaTabs');
        const vistaCards = document.getElementById('vistaCards');

        if(btnTabs && btnCards) {
            btnTabs.addEventListener('click', function() {
                vistaTabs.classList.remove('hidden');
                vistaCards.classList.add('hidden');
                btnTabs.className = "bg-zinc-200 hover:bg-zinc-300 text-zinc-900 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium";
                btnCards.className = "bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-600 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium";
            });

            btnCards.addEventListener('click', function() {
                vistaCards.classList.remove('hidden');
                vistaTabs.classList.add('hidden');
                btnCards.className = "bg-zinc-200 hover:bg-zinc-300 text-zinc-900 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium";
                btnTabs.className = "bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-600 px-3 py-2 rounded-xl text-sm transition-colors flex items-center gap-1 font-medium";
            });
        }

        const tabBtns = document.querySelectorAll('.jornada-tab-btn');
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.dataset.target;
                document.querySelectorAll('.jornada-content-pane').forEach(pane => pane.classList.add('hidden'));
                document.getElementById(target).classList.remove('hidden');

                tabBtns.forEach(b => {
                    b.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-white border border-zinc-200 text-zinc-600 hover:text-zinc-900";
                });
                this.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-md";
            });
        });
    });

    function abrirModalEditarJornada(id, nombre, inicio, fin, cierre) {
        document.getElementById('edit_nombre_jornada').value = nombre;
        document.getElementById('edit_fecha_inicio_jornada').value = inicio;
        document.getElementById('edit_fecha_fin_jornada').value = fin;
        if(cierre && cierre !== '') {
            document.getElementById('edit_fecha_cierre_alineaciones').value = cierre.replace(' ', 'T').substring(0, 16);
        }
        document.getElementById('formEditarJornada').action = "{{ url('/admin/jornadas') }}/" + id;
        openModal('modalEditarJornada');
    }

    function abrirModalCrearPartido(jornadaId, jornadaNombre) {
        document.getElementById('partido_jornada_id').value = jornadaId;
        document.getElementById('tituloModalPartido').innerText = 'Añadir Partido - ' + jornadaNombre;
        document.getElementById('formCrearPartido').action = "{{ url('/admin/jornadas') }}/" + jornadaId + "/partidos/crear";
        openModal('modalCrearPartido');
    }

    function abrirModalResultado(partidoId, local, visitante, glocal, gvisitante) {
        document.getElementById('labelLocal').innerText = local;
        document.getElementById('labelVisitante').innerText = visitante;
        document.getElementById('goles_local').value = (glocal !== 'null' && glocal !== '') ? glocal : 0;
        document.getElementById('goles_visitante').value = (gvisitante !== 'null' && gvisitante !== '') ? gvisitante : 0;
        document.getElementById('formResultado').action = "{{ url('/admin/partidos') }}/" + partidoId + "/resultado";
        openModal('modalResultado');
    }
</script>
@endpush
