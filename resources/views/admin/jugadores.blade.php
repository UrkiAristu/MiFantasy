@extends('admin.layouts.app')

@section('title', 'Jugadores')

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

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Jugadores</h1>
        <button type="button" onclick="openModal('crearJugadorModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Crear Jugador
        </button>
    </div>

    <!-- Tabla Container -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tablaJugadores" class="w-full text-left text-sm text-zinc-700">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-center">ID</th>
                        <th class="py-3 px-4 text-center">Foto</th>
                        <th class="py-3 px-4">Nombre</th>
                        <th class="py-3 px-4">Apellidos</th>
                        <th class="py-3 px-4 text-center">Fecha de Nacimiento</th>
                        <th class="py-3 px-4 text-center">Posición</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($jugadores as $jugador)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $jugador->id }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($jugador->foto)
                            <img src="{{ asset($jugador->foto) }}" alt="Foto" class="w-10 h-10 object-cover mx-auto rounded-full bg-white p-0.5 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-10 h-10 mx-auto rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200\'>N/A</div>'">
                            @else
                            <div class="w-10 h-10 mx-auto rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200">N/A</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-zinc-900">{{ $jugador->nombre }}</td>
                        <td class="py-3 px-4 text-zinc-600">{{ $jugador->apellido1 }} {{ $jugador->apellido2 }}</td>
                        <td class="py-3 px-4 text-center text-zinc-600 text-xs">{{ $jugador->fecha_nacimiento }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-md bg-zinc-100 border border-zinc-200 text-zinc-700 text-xs font-semibold">
                                {{ $jugador->posicion ?? 'Sin posición' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <a href="{{ url('/admin/jugadores/'.$jugador->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver jugador">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <button type="button"
                                class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-eliminar-jugador cursor-pointer"
                                title="Eliminar jugador"
                                data-url="{{ url('/admin/jugadores/'.$jugador->id.'/eliminar') }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-zinc-500">No hay jugadores creados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear jugador -->
    <div id="crearJugadorModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
            <form method="POST" action="{{ url('/admin/jugadores/crear') }}" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                    <h5 class="text-lg font-bold text-zinc-900" id="crearJugadorModalLabel">Crear Jugador</h5>
                    <button type="button" onclick="closeModal('crearJugadorModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    <div>
                        <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre del Jugador</label>
                        <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="nombre" name="nombre" required>
                    </div>
                    <div>
                        <label for="apellido1" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Primer Apellido</label>
                        <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="apellido1" name="apellido1" required>
                    </div>
                    <div>
                        <label for="apellido2" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Segundo Apellido</label>
                        <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="apellido2" name="apellido2" required>
                    </div>
                    <div>
                        <label for="fecha_nacimiento" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Fecha de Nacimiento</label>
                        <input type="date" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>
                    <div>
                        <label for="posicion" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Posición</label>
                        <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="posicion" name="posicion">
                    </div>
                    <div>
                        <label for="foto" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Foto</label>
                        <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs focus:outline-none focus:border-lime-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200" id="foto" name="foto" accept="image/*">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200 bg-zinc-50">
                    <button type="button" onclick="closeModal('crearJugadorModal')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Crear Jugador</button>
                </div>
            </form>
        </div>
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

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('crearJugadorModal');
        }
    });

    $(document).ready(function() {
        $('#tablaJugadores').DataTable({
            destroy: true,
            order: false,
            locale: "es",
            colReorder: true,
            dom: 'Bfrtip',
            stateSave: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print',
            ]
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-jugador');

        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url;

                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este jugador?',
                    text: "¡Esta acción no se puede deshacer!",
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
