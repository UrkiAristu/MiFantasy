@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Jornadas</h1>
            <p class="text-gray-500">Administra las fechas y el calendario de tus torneos.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.local.jornadas.index') }}" method="GET" class="flex items-center gap-2">
                <select name="torneo_id" onchange="this.form.submit()" class="bg-white border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    <option value="">🌐 Todos los Torneos</option>
                    @foreach($torneos as $t)
                        <option value="{{ $t->id }}" {{ $torneoId == $t->id ? 'selected' : '' }}>🏆 {{ $t->nombre }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.local.jornadas.create') }}" class="bg-emerald-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i> Nueva Jornada
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Orden</th>
                    <th class="px-6 py-4">Nombre</th>
                    <th class="px-6 py-4">Torneo</th>
                    <th class="px-6 py-4">Fechas</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($jornadas as $jornada)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $jornada->orden }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $jornada->nombre }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $jornada->torneo->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            {{ $jornada->fecha_inicio?->format('d/m/Y') }} - {{ $jornada->fecha_fin?->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.local.jornadas.edit', $jornada) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                            <button type="button" onclick="eliminarJornada({{ $jornada->id }})" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
                            <form id="delete-form-{{ $jornada->id }}" action="{{ route('admin.local.jornadas.destroy', $jornada) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No hay jornadas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">
            {{ $jornadas->links() }}
        </div>
    </div>
</div>

<script>
    function eliminarJornada(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection
