@extends('admin.layouts.app')

@section('title', 'Detalle del Equipo')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-900">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Detalle del Equipo</h1>
        <a href="{{ url('/admin/equipos') }}" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2 rounded-xl text-sm transition-colors">Volver</a>
    </div>

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

    <!-- Card Información del Equipo -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6 mb-8">
        <h2 class="text-lg font-bold text-zinc-900 mb-6">Información del Equipo</h2>
        <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/editar') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <!-- Columna Logo Actual -->
                <div class="md:col-span-1 text-center">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">Logo Actual</label>
                    @if($equipo->logo)
                    <img src="{{ asset($equipo->logo) }}"
                        alt="Logo del equipo"
                        class="w-32 h-32 object-contain mx-auto rounded-xl bg-white p-2 border border-zinc-200 mb-3"
                        onerror="this.outerHTML='<div class=\'w-32 h-32 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3\'>N/A</div>'">
                    <div class="flex items-center justify-center gap-2">
                        <input class="w-4 h-4 rounded bg-white border-zinc-300 text-red-600 focus:ring-red-500" type="checkbox" name="eliminar_logo" id="eliminar_logo" value="1">
                        <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="eliminar_logo">Eliminar logo</label>
                    </div>
                    @else
                    <div class="w-32 h-32 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3">Sin logo</div>
                    @endif
                </div>

                <!-- Columna Formulario -->
                <div class="md:col-span-3 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre del Equipo</label>
                            <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="nombre" name="nombre" value="{{ old('nombre', $equipo->nombre) }}" required>
                        </div>
                        <div>
                            <label for="logo" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Cambiar Logo</label>
                            <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs focus:outline-none focus:border-lime-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200" id="logo" name="logo" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-zinc-200 pt-4">
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar Cambios</button>
                <a href="{{ url('/admin/equipos/'.$equipo->id.'/eliminar') }}"
                    class="bg-red-50 hover:bg-red-100 text-red-700 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors btn-eliminar-equipo"
                    title="Eliminar el equipo"
                    data-url="{{ url('/admin/equipos/'.$equipo->id.'/eliminar') }}">
                    Eliminar
                </a>
            </div>
        </form>
    </div>

    <!-- Grid Torneos y Jugadores -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Torneos Inscritos -->
        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-zinc-900">Torneos Inscritos</h2>
                <button type="button" onclick="openModal('torneoModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Añadir Torneo
                </button>
            </div>

            @if($equipo->torneos->count())
            <div class="overflow-x-auto">
                <table id="tablaTorneosEquipo" class="w-full text-left text-sm text-zinc-700">
                    <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-4 text-center">#</th>
                            <th class="py-3 px-4 text-center">Logo</th>
                            <th class="py-3 px-4">Torneo</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($equipo->torneos as $index => $torneo)
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($torneo->logo)
                                <img src="{{ asset($torneo->logo) }}" alt="Logo" class="w-10 h-10 object-contain mx-auto rounded-lg bg-white p-1 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-10 h-10 mx-auto rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200\'>N/A</div>'">
                                @else
                                <div class="w-10 h-10 mx-auto rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200">N/A</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-zinc-900">{{ $torneo->nombre }}</td>
                            <td class="py-3 px-4 text-center space-x-1">
                                <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver torneo">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a href="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores') }}" class="inline-flex items-center gap-1 bg-amber-50 hover:bg-amber-100 text-amber-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Jugadores">
                                    <i class="bi bi-people"></i> Jugadores
                                </a>
                                <button type="button"
                                    class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-desapuntar-equipo cursor-pointer"
                                    title="Quitar del torneo"
                                    data-url="{{ url('/admin/equipos/'.$equipo->id.'/torneos/'.$torneo->id.'/eliminar') }}">
                                    <i class="bi bi-x-circle"></i> Quitar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-zinc-500 text-center py-4">No hay torneos inscritos todavía.</p>
            @endif
        </div>

        <!-- Jugadores del Equipo -->
        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-zinc-900">Jugadores del Equipo</h2>
                <button type="button" onclick="openModal('jugadorModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Añadir Jugador
                </button>
            </div>

            @if($equipo->jugadores->count())
            <div class="overflow-x-auto">
                <table id="tablaJugadoresEquipo" class="w-full text-left text-sm text-zinc-700">
                    <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3 px-4 text-center">#</th>
                            <th class="py-3 px-4 text-center">Foto</th>
                            <th class="py-3 px-4">Jugador</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($equipo->jugadores as $index => $jugador)
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($jugador->foto)
                                <img src="{{ asset($jugador->foto) }}" alt="Foto" class="w-10 h-10 object-cover mx-auto rounded-full bg-white p-0.5 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-10 h-10 mx-auto rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200\'>N/A</div>'">
                                @else
                                <div class="w-10 h-10 mx-auto rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200">N/A</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-zinc-900">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</td>
                            <td class="py-3 px-4 text-center space-x-1">
                                <a href="{{ url('/admin/jugadores/'.$jugador->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <button type="button"
                                    class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-expulsar-jugador cursor-pointer"
                                    title="Quitar del equipo"
                                    data-url="{{ url('/admin/equipos/'.$equipo->id.'/jugadores/'.$jugador->id.'/eliminar') }}">
                                    <i class="bi bi-x-circle"></i> Quitar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-zinc-500 text-center py-4">No hay jugadores en este equipo.</p>
            @endif
        </div>
    </div>

    <!-- Modal Gestionar Torneos -->
    <div id="torneoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl relative">
            <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                <h5 class="text-lg font-bold text-zinc-900">Gestionar Torneos</h5>
                <button type="button" onclick="closeModal('torneoModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="flex gap-2 border-b border-zinc-200 pb-4 mb-4" role="tablist">
                    <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm" id="tabSelTorneoBtn" onclick="switchTorneoTab('seleccionar')">Seleccionar Torneo</button>
                    <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900" id="tabCrearTorneoBtn" onclick="switchTorneoTab('crear')">Crear Nuevo Torneo</button>
                </div>

                <div id="paneSeleccionarTorneo">
                    <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/torneos/agregar') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="torneo_existente" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Torneo Existente</label>
                            <select class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="torneo_existente" name="torneo_id" required>
                                <option value="">Selecciona un torneo</option>
                                @foreach($torneosDisponibles as $torneo)
                                <option value="{{ $torneo->id }}">{{ $torneo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Añadir al Torneo</button>
                        </div>
                    </form>
                </div>

                <div id="paneCrearTorneo" class="hidden">
                    <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/torneos/crear') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre del Torneo</label>
                                    <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="nombre" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Logo</label>
                                    <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs focus:outline-none focus:border-lime-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200" name="logo" accept="image/*">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Descripción</label>
                                <textarea class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="descripcion" rows="2"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha de Inicio</label>
                                    <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="fecha_inicio" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha de Fin</label>
                                    <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="fecha_fin" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Estado</label>
                                    <select class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="estado" required>
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Jugadores / Eq.</label>
                                    <input type="number" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="jugadores_por_equipo" min="1" value="5" required>
                                </div>
                                <div class="flex items-center space-x-2 pt-5">
                                    <input class="w-4 h-4 rounded bg-white border-zinc-300 text-lime-600 focus:ring-lime-500" type="checkbox" name="usa_posiciones" value="1" id="usa_posiciones_torneo">
                                    <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="usa_posiciones_torneo">Usar Posiciones</label>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4 pt-3 border-t border-zinc-200">
                            <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Crear y Añadir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Gestionar Jugadores -->
    <div id="jugadorModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl relative">
            <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                <h5 class="text-lg font-bold text-zinc-900">Gestionar Jugadores</h5>
                <button type="button" onclick="closeModal('jugadorModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="flex gap-2 border-b border-zinc-200 pb-4 mb-4" role="tablist">
                    <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm" id="tabSelJugadorBtn" onclick="switchJugadorTab('seleccionar')">Seleccionar Jugador</button>
                    <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900" id="tabCrearJugadorBtn" onclick="switchJugadorTab('crear')">Crear Nuevo Jugador</button>
                </div>

                <div id="paneSeleccionarJugador">
                    <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/jugadores/agregar') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="jugador_existente" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Jugador Existente</label>
                            <select class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="jugador_existente" name="jugador_id" required>
                                <option value="">Selecciona un jugador</option>
                                @foreach($jugadoresDisponibles as $jugador)
                                <option value="{{ $jugador->id }}">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Añadir Jugador</button>
                        </div>
                    </form>
                </div>

                <div id="paneCrearJugador" class="hidden">
                    <form method="POST" action="{{ url('/admin/equipos/'.$equipo->id.'/jugadores/crear') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre</label>
                                    <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="nombre" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">1º Apellido</label>
                                    <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="apellido1" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">2º Apellido</label>
                                    <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="apellido2" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha Nac.</label>
                                    <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="fecha_nacimiento" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Posición</label>
                                    <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" name="posicion">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Foto</label>
                                    <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs focus:outline-none focus:border-lime-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200" name="foto" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4 pt-3 border-t border-zinc-200">
                            <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Crear y Añadir</button>
                        </div>
                    </form>
                </div>
            </div>
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

    function switchTorneoTab(tab) {
        const paneSel = document.getElementById('paneSeleccionarTorneo');
        const paneCrear = document.getElementById('paneCrearTorneo');
        const btnSel = document.getElementById('tabSelTorneoBtn');
        const btnCrear = document.getElementById('tabCrearTorneoBtn');

        if(tab === 'seleccionar') {
            paneSel.classList.remove('hidden');
            paneCrear.classList.add('hidden');
            btnSel.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm";
            btnCrear.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900";
        } else {
            paneCrear.classList.remove('hidden');
            paneSel.classList.add('hidden');
            btnCrear.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm";
            btnSel.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900";
        }
    }

    function switchJugadorTab(tab) {
        const paneSel = document.getElementById('paneSeleccionarJugador');
        const paneCrear = document.getElementById('paneCrearJugador');
        const btnSel = document.getElementById('tabSelJugadorBtn');
        const btnCrear = document.getElementById('tabCrearJugadorBtn');

        if(tab === 'seleccionar') {
            paneSel.classList.remove('hidden');
            paneCrear.classList.add('hidden');
            btnSel.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm";
            btnCrear.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900";
        } else {
            paneCrear.classList.remove('hidden');
            paneSel.classList.add('hidden');
            btnCrear.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm";
            btnSel.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900";
        }
    }

    $(document).ready(function() {
        $('#tablaTorneosEquipo').DataTable({
            destroy: true,
            order: false,
            locale: "es",
            colReorder: true,
            dom: 'Bfrtip',
            stateSave: true,
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
        $('#tablaJugadoresEquipo').DataTable({
            destroy: true,
            order: false,
            locale: "es",
            colReorder: true,
            dom: 'Bfrtip',
            stateSave: true,
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
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

        const botonesDesapuntar = document.querySelectorAll('.btn-desapuntar-equipo');
        botonesDesapuntar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas quitar este equipo del torneo?',
                    text: "El equipo ya no participará en este torneo.",
                    icon: 'warning',
                    background: '#ffffff',
                    color: '#18181b',
                    showCancelButton: true,
                    confirmButtonColor: '#a3e635',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Sí, quitar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        createAndSubmitDeleteForm(url);
                    }
                });
            });
        });

        const botonesExpulsar = document.querySelectorAll('.btn-expulsar-jugador');
        botonesExpulsar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas quitar este jugador del equipo?',
                    text: "El jugador dejará de pertenecer al equipo.",
                    icon: 'warning',
                    background: '#ffffff',
                    color: '#18181b',
                    showCancelButton: true,
                    confirmButtonColor: '#a3e635',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Sí, quitar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        createAndSubmitDeleteForm(url);
                    }
                });
            });
        });

        const botonesEliminarEquipo = document.querySelectorAll('.btn-eliminar-equipo');
        botonesEliminarEquipo.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este equipo?',
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