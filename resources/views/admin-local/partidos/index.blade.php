@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Partidos</h1>
            <p class="text-gray-500">Administra los partidos de tus torneos.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.local.partidos.create') }}" class="bg-lime-500 text-gray-900 font-semibold px-4 py-2 rounded-lg hover:bg-lime-400 transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i> Registrar Partido
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.local.partidos.bulkUpdate') }}" method="POST" x-data="{ saving: false }" @submit="saving = true" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @csrf
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Fecha</th>
                    <th class="px-6 py-4">Local</th>
                    <th class="px-6 py-4">Goles</th>
                    <th class="px-6 py-4">Visitante</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($partidos as $index => $partido)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-600">
                            <input type="hidden" name="partidos[{{ $index }}][id]" value="{{ $partido->id }}">
                            {{ $partido->fecha_partido->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $partido->equipoLocal->nombre }}</td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            <input type="number" name="partidos[{{ $index }}][goles_local]" value="{{ $partido->goles_local }}" class="w-16 p-1 border border-gray-300 rounded text-center">
                            <span>-</span>
                            <input type="number" name="partidos[{{ $index }}][goles_visitante]" value="{{ $partido->goles_visitante }}" class="w-16 p-1 border border-gray-300 rounded text-center">
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $partido->equipoVisitante->nombre }}</td>
                        <td class="px-6 py-4">
                            <select name="partidos[{{ $index }}][estado]" class="p-1 border border-gray-300 rounded text-sm">
                                <option value="programado" {{ $partido->estado === 'programado' ? 'selected' : '' }}>Programado</option>
                                <option value="jugado" {{ $partido->estado === 'jugado' ? 'selected' : '' }}>Jugado</option>
                                <option value="cancelado" {{ $partido->estado === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.local.partidos.edit', $partido) }}" class="text-blue-600 hover:text-blue-900 font-medium">Detallado</a>
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
        @if($partidos->isNotEmpty())
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end">
            <button type="submit" :disabled="saving" class="bg-lime-500 text-gray-900 font-semibold px-4 py-2 rounded-lg hover:bg-lime-400 transition shadow-sm disabled:opacity-50"><span x-text="saving ? 'Guardando...' : 'Guardar Jornada'"></span></button>
        </div>
        @endif
    </form>
    <div class="p-4">
        {{ $partidos->links() }}
    </div>
</div>
@endsection


