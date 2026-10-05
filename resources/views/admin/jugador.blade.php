@extends('admin.layouts.app')

@section('title', 'Detalle del Jugador')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-900">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Detalle del Jugador</h1>
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

    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6 mb-8">
        <h2 class="text-lg font-bold text-zinc-900 mb-6">Información del Jugador</h2>
        <form method="POST" action="{{ url('/admin/jugadores/'.$jugador->id.'/editar') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <div class="md:col-span-1 text-center">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">Foto Actual</label>
                    @if($jugador->foto)
                    <img src="{{ asset($jugador->foto) }}" alt="Foto del jugador" class="w-32 h-32 object-cover mx-auto rounded-xl bg-white p-2 border border-zinc-200 mb-3" onerror="this.outerHTML='<div class=\'w-32 h-32 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3\'>N/A</div>'">
                    <div class="flex items-center justify-center gap-2">
                        <input class="w-4 h-4 rounded bg-white border-zinc-300 text-red-600 focus:ring-red-500" type="checkbox" name="eliminar_foto" id="eliminar_foto" value="1">
                        <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="eliminar_foto">Eliminar foto</label>
                    </div>
                    @else
                    <div class="w-32 h-32 mx-auto rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 text-xs font-bold border border-zinc-200 mb-3">Sin foto</div>
                    @endif
                </div>

                <div class="md:col-span-3 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="foto" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Cambiar Foto</label>
                            <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs focus:outline-none focus:border-lime-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200" id="foto" name="foto" accept="image/*">
                        </div>
                        <div>
                            <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre</label>
                            <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="nombre" name="nombre" value="{{ old('nombre', $jugador->nombre) }}" required>
                        </div>
                        <div>
                            <label for="apellido1" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">1º Apellido</label>
                            <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="apellido1" name="apellido1" value="{{ old('apellido1', $jugador->apellido1) }}" required>
                        </div>
                        <div>
                            <label for="apellido2" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">2º Apellido</label>
                            <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="apellido2" name="apellido2" value="{{ old('apellido2', $jugador->apellido2) }}" required>
                        </div>
                        <div>
                            <label for="fecha_nacimiento" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha Nacimiento</label>
                            <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $jugador->fecha_nacimiento) }}" required>
                        </div>
                        <div>
                            <label for="posicion" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Posición</label>
                            <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="posicion" name="posicion" value="{{ old('posicion', $jugador->posicion) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3 border-t border-zinc-200 pt-4">
                <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Guardar Cambios</button>
                <a href="{{ url('/admin/jugadores/'.$jugador->id.'/eliminar') }}"
                    class="bg-red-50 hover:bg-red-100 text-red-700 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors btn-eliminar-jugador"
                    data-url="{{ url('/admin/jugadores/'.$jugador->id.'/eliminar') }}">
                    Eliminar
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-zinc-900">Equipos</h2>
                <button type="button" onclick="openModal('equipoModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Añadir Equipos
                </button>
            </div>
            @if($jugador->equipos->count())
            <table id="tablaEquiposJugador" class="w-full text-left text-sm text-zinc-700">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-center">#</th>
                        <th class="py-3 px-4 text-center">Logo</th>
                        <th class="py-3 px-4">Equipo</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach($jugador->equipos as $index => $equipo)
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
                            <a href="{{ url('/admin/equipos/'.$equipo->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <button type="button" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-dejar-equipo cursor-pointer" data-url="{{ url('/admin/jugadores/'.$jugador->id.'/equipos/'.$equipo->id.'/eliminar') }}">
                                <i class="bi bi-x-circle"></i> Quitar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-zinc-500 text-center py-4">No hay equipos todavía.</p>
            @endif
        </div>

        <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
            <h2 class="text-lg font-bold text-zinc-900 mb-6">Estadísticas</h2>
            @if($jugador->participaciones->count())
            <table id="tablaTorneosJugador" class="w-full text-left text-sm text-zinc-700">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-center">Torneo</th>
                        <th class="py-3 px-4 text-center">Pts</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach($jugador->participaciones as $torneo)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="py-3 px-4 text-center">{{ $torneo->nombre }}</td>
                        <td class="py-3 px-4 text-center font-bold text-zinc-900">{{ $torneo->pivot->puntos }}</td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                <i class="bi bi-trophy"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-zinc-500 text-center py-4">No ha participado en ningún torneo.</p>
            @endif
        </div>
    </div>
</div>

<div id="equipoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
    <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl relative">
        <div class="flex items-center justify-between p-6 border-b border-zinc-200">
            <h5 class="text-lg font-bold text-zinc-900">Gestionar Equipos</h5>
            <button type="button" onclick="closeModal('equipoModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="flex gap-2 border-b border-zinc-200 pb-4 mb-4" role="tablist">
                <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-lime-400 text-zinc-950 shadow-sm" id="tabSelBtn" onclick="switchTab('seleccionar')">Seleccionar</button>
                <button class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors bg-zinc-100 text-zinc-600 hover:text-zinc-900" id="tabCrearBtn" onclick="switchTab('crear')">Crear</button>
            </div>
            <div id="paneSeleccionar">
                <form method="POST" action="{{ url('/admin/jugadores/'.$jugador->id.'/equipos/agregar') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Equipo</label>
                        <select class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm" name="equipo_id" required>
                            <option value="">Selecciona</option>
                            @foreach($equiposDisponibles as $equipo)
                            <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors w-full">Añadir</button>
                </form>
            </div>
            <div id="paneCrear" class="hidden">
                <form method="POST" action="{{ url('/admin/jugadores/'.$jugador->id.'/equipos/crear') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre</label>
                        <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm" name="nombre" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Logo</label>
                        <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs" name="logo" accept="image/*">
                    </div>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors w-full">Crear y Añadir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    function switchTab(tab) {
        const sel = document.getElementById('paneSeleccionar');
        const cre = document.getElementById('paneCrear');
        if(tab === 'seleccionar') { sel.classList.remove('hidden'); cre.classList.add('hidden'); }
        else { cre.classList.remove('hidden'); sel.classList.remove('hidden'); }
    }

    $(document).ready(function() {
        $('#tablaEquiposJugador, #tablaTorneosJugador').DataTable({
            destroy: true, order: false, locale: "es", colReorder: true, dom: 'Bfrtip', stateSave: true,
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const createAndSubmitDeleteForm = (url) => {
            const form = document.createElement('form');
            form.method = 'POST'; form.action = url;
            const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.appendChild(csrf); form.appendChild(method); document.body.appendChild(form); form.submit();
        };
        document.querySelectorAll('.btn-dejar-equipo, .btn-eliminar-jugador').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#a3e635', cancelButtonColor: '#ef4444', confirmButtonText: 'Sí'
                }).then((res) => { if (res.isConfirmed) createAndSubmitDeleteForm(this.dataset.url); });
            });
        });
    });
</script>
@endpush
