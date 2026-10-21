@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Partidos</h1>
            <p class="text-gray-500">Administra los partidos de tus torneos.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.local.partidos.create') }}" class="bg-emerald-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i> Registrar Partido
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
                    <th class="px-6 py-4">Fecha</th>
                    <th class="px-6 py-4">Local</th>
                    <th class="px-6 py-4">Visitante</th>
                    <th class="px-6 py-4">Resultado</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($partidos as $partido)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-600">{{ $partido->fecha_partido->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $partido->equipoLocal->nombre }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $partido->equipoVisitante->nombre }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">
                            {{ $partido->estado === 'jugado' ? $partido->goles_local . ' - ' . $partido->goles_visitante : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full
                                {{ $partido->estado === 'jugado' ? 'bg-green-100 text-green-800' :
                                   ($partido->estado === 'programado' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($partido->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.local.partidos.edit', $partido) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                            <button type="button" onclick="eliminarPartido({{ $partido->id }})" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
                            <form id="delete-form-{{ $partido->id }}" action="{{ route('admin.local.partidos.destroy', $partido) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No hay partidos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">
            {{ $partidos->links() }}
        </div>
    </div>
</div>

<script>
    function eliminarPartido(id) {
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
