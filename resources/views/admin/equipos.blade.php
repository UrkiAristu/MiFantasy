@extends('admin.layouts.app')

@section('title', 'Equipos')

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
        <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Equipos</h1>
        <button type="button" onclick="openModal('crearEquipoModal')" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-md flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Crear Equipo
        </button>
    </div>

    <!-- Tabla Container -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tablaEquipos" class="w-full text-left text-sm text-zinc-700">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-4 text-center">ID</th>
                        <th class="py-3 px-4 text-center">Logo</th>
                        <th class="py-3 px-4">Nombre</th>
                        <th class="py-3 px-4 text-center">Fecha de Creación</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($equipos as $equipo)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="py-3 px-4 text-center font-mono text-zinc-500">{{ $equipo->id }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($equipo->logo)
                            <img src="{{ asset($equipo->logo) }}" alt="Logo" class="w-10 h-10 object-contain mx-auto rounded-lg bg-white p-1 border border-zinc-200" onerror="this.outerHTML='<div class=\'w-10 h-10 mx-auto rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200\'>N/A</div>'">
                            @else
                            <div class="w-10 h-10 mx-auto rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-400 text-[10px] font-bold border border-zinc-200">N/A</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-zinc-900">{{ $equipo->nombre }}</td>
                        <td class="py-3 px-4 text-center text-zinc-600 text-xs">{{ $equipo->created_at }}</td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <a href="{{ url('/admin/equipos/'.$equipo->id) }}" class="inline-flex items-center gap-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors" title="Ver equipo">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <button type="button"
                                class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors btn-eliminar-equipo cursor-pointer"
                                title="Eliminar equipo"
                                data-url="{{ url('/admin/equipos/'.$equipo->id.'/eliminar') }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-zinc-500">No hay equipos creados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear equipo -->
    <div id="crearEquipoModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-sm">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative">
            <form method="POST" action="{{ url('/admin/equipos/crear') }}" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                    <h5 class="text-lg font-bold text-zinc-900" id="crearEquipoModalLabel">Crear Equipo</h5>
                    <button type="button" onclick="closeModal('crearEquipoModal')" class="text-zinc-500 hover:text-zinc-900 p-1.5 rounded-lg hover:bg-zinc-100">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Nombre del Equipo</label>
                        <input type="text" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-900 text-sm focus:outline-none focus:border-lime-500" id="nombre" name="nombre" required>
                    </div>
                    <div>
                        <label for="logo" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-1">Logo</label>
                        <input type="file" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2 text-zinc-600 text-xs focus:outline-none focus:border-lime-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200" id="logo" name="logo" accept="image/*">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200 bg-zinc-50">
                    <button type="button" onclick="closeModal('crearEquipoModal')" class="bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-medium px-4 py-2.5 rounded-xl text-sm transition-colors">Cancelar</button>
                    <button type="submit" class="bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-colors shadow-md">Crear Equipo</button>
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
            closeModal('crearEquipoModal');
        }
    });

    $(document).ready(function() {
        $('#tablaEquipos').DataTable({
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
        const botonesEliminar = document.querySelectorAll('.btn-eliminar-equipo');

        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url;

                Swal.fire({
                    title: '¿Estás seguro de que deseas eliminar este equipo?',
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
