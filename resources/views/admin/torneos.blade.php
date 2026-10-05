@extends('admin.layouts.app')

@section('title', 'Torneos')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-zinc-200">
    <!-- Mensajes -->
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
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-100">Torneos</h1>
        <button type="button" onclick="openModal('crearTorneoModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Crear Torneo
        </button>
    </div>

    <!-- Tabla Container -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm p-6">
        <div class="overflow-x-auto">
            <table id="tablaTorneos" class="w-full text-left text-sm text-zinc-300">
                <thead class="bg-zinc-950/80 text-zinc-400 uppercase text-xs tracking-wider border-b border-zinc-800">
                    <tr>
                        <th class="py-3 px-4 text-center">ID</th>
                        <th class="py-3 px-4 text-center">Logo</th>
                        <th class="py-3 px-4">Nombre</th>
                        <th class="py-3 px-4">Descripción</th>
                        <th class="py-3 px-4 text-center">Inicio</th>
                        <th class="py-3 px-4 text-center">Fin</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4 text-center">Jugadores</th>
                        <th class="py-3 px-4 text-center">Posiciones</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($torneos as $torneo)
                    <tr class="hover:bg-zinc-800/30 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-400">{{ $torneo->id }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($torneo->logo)
                            <img src="{{ asset($torneo->logo) }}" alt="Logo" class="w-10 h-10 object-contain mx-auto rounded-lg bg-zinc-950 p-1 border border-zinc-800">
                            @else
                            <span class="text-zinc-500 text-xs">Sin logo</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-zinc-100">{{ $torneo->nombre }}</td>
                        <td class="py-3 px-4 text-zinc-400 max-w-xs truncate">{{ $torneo->descripcion }}</td>
                        <td class="py-3 px-4 text-center text-zinc-300 text-xs">{{ $torneo->fecha_inicio }}</td>
                        <td class="py-3 px-4 text-center text-zinc-300 text-xs">{{ $torneo->fecha_fin }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($torneo->estado)
                            <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">Activo</span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold">Inactivo</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-semibold text-zinc-200">{{ $torneo->jugadores_por_equipo }}</td>
                        <td class="py-3 px-4 text-center">
                            @if ($torneo->usa_posiciones)
                            <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">Sí</span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-400 text-xs font-semibold">No</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <a href="{{ url('/admin/torneos/'.$torneo->id) }}" class="inline-flex items-center gap-1 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver torneo">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ url('/admin/torneos/'.$torneo->id.'/jornadas') }}" class="inline-flex items-center gap-1 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver jornadas">
                                <i class="bi bi-calendar-event"></i> Jornadas
                            </a>
                            <button type="button"
                                class="inline-flex items-center gap-1 bg-red-500/10 hover:bg-red-500/20 text-red-400 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-eliminar-torneo cursor-pointer"
                                title="Eliminar torneo"
                                data-url="{{ url('/admin/torneos/'.$torneo->id.'/eliminar') }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-8 text-center text-zinc-500">No hay torneos creados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear torneo (Tailwind) -->
    <div id="crearTorneoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl relative">
            <form method="POST" action="{{ url('/admin/torneos/crear') }}" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between p-6 border-b border-zinc-800">
                    <h5 class="text-lg font-bold text-zinc-100" id="crearTorneoModalLabel">Crear Torneo</h5>
                    <button type="button" onclick="closeModal('crearTorneoModal')" class="text-zinc-400 hover:text-zinc-100 p-1.5 rounded-lg hover:bg-zinc-800">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Nombre del Torneo</label>
                            <input type="text" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" id="nombre" name="nombre" required>
                        </div>
                        <div>
                            <label for="logo" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Logo</label>
                            <input type="file" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-400 text-xs focus:outline-none focus:border-lime-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-200 hover:file:bg-zinc-700" id="logo" name="logo" accept="image/*">
                        </div>
                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Descripción</label>
                            <textarea class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" id="descripcion" name="descripcion" rows="2"></textarea>
                        </div>
                        <div>
                            <label for="fecha_inicio" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha de Inicio</label>
                            <input type="date" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Fecha de Fin</label>
                            <input type="date" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <div>
                            <label for="estado" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Estado</label>
                            <select class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" id="estado" name="estado" required>
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div>
                            <label for="jugadores_por_equipo" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1">Jugadores por Alineación</label>
                            <input type="number" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-zinc-200 text-sm focus:outline-none focus:border-lime-400" id="jugadores_por_equipo" name="jugadores_por_equipo" min="1" value="5" required>
                        </div>
                        <div class="flex items-center space-x-3 pt-2">
                            <input class="w-4 h-4 rounded bg-zinc-950 border-zinc-800 text-lime-400 focus:ring-lime-400 focus:ring-offset-zinc-900" type="checkbox" id="usa_posiciones" name="usa_posiciones" value="1">
                            <label class="text-xs font-semibold uppercase tracking-wider text-zinc-300 select-none" for="usa_posiciones">Usar Posiciones</label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-800 bg-zinc-950/40">
                    <button type="button" onclick="closeModal('crearTorneoModal')" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Crear Torneo</button>
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
            closeModal('crearTorneoModal');
        }
    });

    $(document).ready(function() {
        $('#tablaTorneos').DataTable({
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
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-torneo');

        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');

                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este torneo?',
                    text: "Esta acción no se puede deshacer.",
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
