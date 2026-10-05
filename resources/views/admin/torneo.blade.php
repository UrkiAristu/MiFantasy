@extends('admin.layouts.app')

@section('title', 'Detalle del Torneo')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-900">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Detalle del Torneo</h1>
        <a href="{{ url('/admin/torneos') }}" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2 rounded-xl text-sm transition-colors">Volver</a>
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

    <!-- Card Información del Torneo -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6 mb-8">
        <h2 class="text-lg font-bold text-zinc-900 mb-6">Información del Torneo</h2>
        <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/editar') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <!-- Columna Logo Actual -->
                <div class="md:col-span-1 text-center">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">Logo Actual</label>
                    @if($torneo->logo)
                    <img src="{{ asset($torneo->logo) }}"
                        alt="Logo del torneo"
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
                            <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre del Torneo</label>
                            <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="nombre" name="nombre" value="{{ old('nombre', $torneo->nombre) }}" required>
                        </div>
                        <div>
                            <label for="logo" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Cambiar Logo</label>
                            <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs focus:outline-none focus:border-lime-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200" id="logo" name="logo" accept="image/*">
                        </div>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Descripción</label>
                        <textarea class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $torneo->descripcion) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="fecha_inicio" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha de Inicio</label>
                            <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $torneo->fecha_inicio) }}" required>
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha de Fin</label>
                            <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', $torneo->fecha_fin) }}" required>
                        </div>
                        <div>
                            <label for="estado" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Estado</label>
                            <select class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="estado" name="estado" required>
                                <option value="1" {{ old('estado', $torneo->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('estado', $torneo->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center pt-2">
                        <div>
                            <label for="jugadores_por_equipo" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Jugadores por Equipo</label>
                            <input type="number" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="jugadores_por_equipo" name="jugadores_por_equipo" min="1" value="{{ old('jugadores_por_equipo', $torneo->jugadores_por_equipo) }}" required>
                        </div>
                        <div class="flex items-center space-x-3 pt-6">
                            <input class="w-4 h-4 rounded bg-white border-zinc-300 text-lime-600 focus:ring-lime-500" type="checkbox" id="usa_posiciones" name="usa_posiciones" value="1" {{ old('usa_posiciones', $torneo->usa_posiciones ?? false) ? 'checked' : '' }}>
                            <label class="text-xs font-semibold uppercase tracking-wider text-zinc-700 select-none" for="usa_posiciones">Usar Posiciones</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-zinc-200 pt-4">
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar Cambios</button>
                <a href="{{ url('/admin/torneos/'.$torneo->id.'/eliminar') }}"
                    class="bg-red-50 hover:bg-red-100 text-red-700 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors btn-eliminar-torneo"
                    title="Eliminar el torneo"
                    data-url="{{ url('/admin/torneos/'.$torneo->id.'/eliminar') }}">
                    Eliminar
                </a>
            </div>
        </form>
    </div>

    <!-- Enlace a Jornadas -->
    <div class="mb-8">
        <a href="{{ url('/admin/torneos/'.$torneo->id.'/jornadas') }}" class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-3 px-6 rounded-2xl shadow-md transition-colors flex items-center justify-center gap-2 text-base">
            <i class="bi bi-calendar-event text-lg"></i> Ver Jornadas del Torneo
        </a>
    </div>

    <!-- Equipos Inscritos -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-zinc-900">Equipos Inscritos</h2>
            <button type="button" onclick="openModal('equipoModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Añadir Equipo
            </button>
        </div>

        @if($torneo->equipos->count())
        <div class="overflow-x-auto">
            <table id="tablaEquiposTorneo" class="w-full text-left text-sm text-zinc-700">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-center">#</th>
                        <th class="py-3 px-4 text-center">Logo</th>
                        <th class="py-3 px-4">Nombre del Equipo</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach($torneo->equipos as $index => $equipo)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($equipo->logo)
                            <img src="{{ asset($equipo->logo) }}" alt="Logo" class="w-10 h-10 object-contain mx-auto rounded-lg bg-white p-1 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-10 h-10 mx-auto rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200\'>N/A</div>'">
                            @else
                            <div class="w-10 h-10 mx-auto rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200">N/A</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-zinc-900">{{ $equipo->nombre }}</td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <a href="{{ url('/admin/equipos/'.$equipo->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver equipo">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/jugadores') }}" class="inline-flex items-center gap-1 bg-amber-50 hover:bg-amber-100 text-amber-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver jugadores">
                                <i class="bi bi-people"></i> Jugadores
                            </a>
                            <button type="button"
                                class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-desapuntar-equipo cursor-pointer"
                                title="Desapuntar del torneo"
                                data-url="{{ url('/admin/torneos/'.$torneo->id.'/equipos/'.$equipo->id.'/eliminar') }}">
                                <i class="bi bi-x-circle"></i> Quitar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-zinc-500 text-center py-4">No hay equipos inscritos todavía.</p>
        @endif
    </div>

    <!-- Jugadores Inscritos -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6 mb-8">
        <h2 class="text-lg font-bold text-zinc-900 mb-6">Jugadores inscritos en el torneo</h2>

        @if($torneo->jugadores->count())
        <div class="overflow-x-auto">
            <table id="tablaJugadoresTorneo" class="w-full text-left text-sm text-zinc-700">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-center">#</th>
                        <th class="py-3 px-4">Jugador</th>
                        <th class="py-3 px-4">Equipo</th>
                        <th class="py-3 px-4 text-center">Goles</th>
                        <th class="py-3 px-4 text-center">Asistencias</th>
                        <th class="py-3 px-4 text-center">Puntos</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach($torneo->jugadores as $index => $jugador)
                    @php
                    $equipo = $torneo->equipos->firstWhere('id', $jugador->pivot->equipo_id);
                    @endphp
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 font-bold text-zinc-900">{{ $jugador->nombre }} {{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</td>
                        <td class="py-3 px-4 text-zinc-700">{{ $equipo ? $equipo->nombre : 'Sin equipo' }}</td>
                        <td class="py-3 px-4 text-center font-semibold">{{ $jugador->pivot->goles }}</td>
                        <td class="py-3 px-4 text-center font-semibold">{{ $jugador->pivot->asistencias }}</td>
                        <td class="py-3 px-4 text-center font-bold text-emerald-600">{{ $jugador->pivot->puntos }}</td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ url('/admin/jugadores/'.$jugador->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-zinc-500 text-center py-4">No hay jugadores inscritos en este torneo todavía.</p>
        @endif
    </div>

    <!-- Modal Gestionar Equipos -->
    <div id="equipoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl relative">
            <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                <h5 class="text-lg font-bold text-zinc-900" id="equipoModalLabel">Gestionar Equipos</h5>
                <button type="button" onclick="closeModal('equipoModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="flex gap-2 border-b border-zinc-200 pb-4 mb-4" role="tablist">
                    <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm" id="tabSeleccionarBtn" onclick="switchTab('seleccionar')">Seleccionar Equipo</button>
                    <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900" id="tabCrearBtn" onclick="switchTab('crear')">Crear Nuevo Equipo</button>
                </div>

                <div id="tabSeleccionarPane">
                    <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/equipos/agregar') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="equipo_existente" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Equipo Existente</label>
                            <select class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="equipo_existente" name="equipo_id" required>
                                <option value="">Selecciona un equipo</option>
                                @foreach($equiposDisponibles as $equipo)
                                <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Añadir al Torneo</button>
                        </div>
                    </form>
                </div>

                <div id="tabCrearPane" class="hidden">
                    <form method="POST" action="{{ url('/admin/torneos/'.$torneo->id.'/equipos/crear') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="nuevo_nombre" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre del Equipo</label>
                            <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="nuevo_nombre" name="nombre" required>
                        </div>
                        <div class="mb-4">
                            <label for="logo_nuevo" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Logo</label>
                            <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs focus:outline-none focus:border-lime-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200" id="logo_nuevo" name="logo" accept="image/*">
                        </div>
                        <div class="flex justify-end">
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

    function switchTab(tab) {
        const selPane = document.getElementById('tabSeleccionarPane');
        const crearPane = document.getElementById('tabCrearPane');
        const selBtn = document.getElementById('tabSeleccionarBtn');
        const crearBtn = document.getElementById('tabCrearBtn');

        if(tab === 'seleccionar') {
            selPane.classList.remove('hidden');
            crearPane.classList.add('hidden');
            selBtn.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm";
            crearBtn.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900";
        } else {
            crearPane.classList.remove('hidden');
            selPane.classList.add('hidden');
            crearBtn.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm";
            selBtn.className = "px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900";
        }
    }

    $(document).ready(function() {
        $('#tablaEquiposTorneo').DataTable({
            destroy: true,
            order: false,
            locale: "es",
            colReorder: true,
            dom: 'Bfrtip',
            stateSave: true,
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
        $('#tablaJugadoresTorneo').DataTable({
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
        const botonesEliminar = document.querySelectorAll('.btn-desapuntar-equipo');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas expulsar este equipo del torneo?',
                    text: "El equipo no participará más en el torneo.",
                    icon: 'warning',
                    background: '#ffffff',
                    color: '#18181b',
                    showCancelButton: true,
                    confirmButtonColor: '#a3e635',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Sí, expulsar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitDeleteForm(url);
                    }
                });
            });
        });

        const botonEliminarTorneo = document.querySelector('.btn-eliminar-torneo');
        if(botonEliminarTorneo) {
            botonEliminarTorneo.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este torneo?',
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
                        submitDeleteForm(url);
                    }
                });
            });
        }
    });

    function submitDeleteForm(url) {
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
</script>
@endpush
