@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $jornada->nombre }}</h1>
            <p class="text-gray-500">Torneo: {{ $jornada->torneo->nombre }} | Registro de resultados</p>
        </div>
        <a href="{{ route('admin.local.jornadas.index') }}" class="text-gray-500 hover:text-white font-medium">Volver al calendario</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden">
        <form action="{{ route('admin.local.partidos.bulkUpdate') }}" method="POST">
            @csrf
            @method('PUT')
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Equipo Local</th>
                        <th class="py-4 px-6 text-center">Goles</th>
                        <th class="py-4 px-6 text-center">Goles</th>
                        <th class="py-4 px-6">Equipo Visitante</th>
                        <th class="py-4 px-6">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach($jornada->partidos as $index => $partido)
                        <tr class="hover:bg-gray-200/40">
                            <input type="hidden" name="partidos[{{ $index }}][id]" value="{{ $partido->id }}">
                            <td class="py-4 px-6 font-semibold text-gray-900">{{ $partido->equipoLocal->nombre }}</td>
                            <td class="py-4 px-6">
                                <input type="number" name="partidos[{{ $index }}][goles_local]" value="{{ $partido->goles_local }}" class="w-20 bg-white border border-gray-300 rounded-lg text-center p-2 text-gray-900 focus:ring-lime-400">
                            </td>
                            <td class="py-4 px-6">
                                <input type="number" name="partidos[{{ $index }}][goles_visitante]" value="{{ $partido->goles_visitante }}" class="w-20 bg-white border border-gray-300 rounded-lg text-center p-2 text-gray-900 focus:ring-lime-400">
                            </td>
                            <td class="py-4 px-6 font-semibold text-gray-900">{{ $partido->equipoVisitante->nombre }}</td>
                            <td class="py-4 px-6">
                                <select name="partidos[{{ $index }}][estado]" class="bg-white border border-gray-300 rounded-lg text-sm p-2 text-gray-900">
                                    <option value="programado" {{ $partido->estado == 'programado' ? 'selected' : '' }}>Programado</option>
                                    <option value="jugado" {{ $partido->estado == 'jugado' ? 'selected' : '' }}>Jugado</option>
                                    <option value="cancelado" {{ $partido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-6 border-t border-gray-200 bg-white/40 flex justify-end">
                <button type="submit" class="bg-lime-400 text-gray-900 font-bold px-8 py-3 rounded-xl hover:bg-lime-300 transition-colors shadow-lg shadow-lime-400/10">
                    Guardar Resultados
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


