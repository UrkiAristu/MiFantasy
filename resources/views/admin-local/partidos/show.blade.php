@extends('admin-local.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 text-gray-900">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-gray-900">Detalle del Partido</h1>
            <a href="{{ route('admin.local.jornadas.index', ['torneo_id' => $partido->jornada->torneo_id]) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                {{ $partido->jornada->torneo->nombre ?? '' }} &bull; {{ $partido->jornada->nombre ?? '' }}
            </a>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.local.partidos.edit', $partido->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-lg text-sm transition-colors cursor-pointer">Editar Partido</a>
        </div>
    </div>

    <!-- Equipos y marcador -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden p-6 mb-8 grid grid-cols-1 md:grid-cols-3 gap-6 items-center text-center">
        <div class="flex flex-col items-center">
            <h3 class="text-lg font-bold text-gray-900">{{ $partido->equipoLocal?->nombre ?? 'Equipo Local' }}</h3>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="text-3xl font-extrabold tracking-tight text-gray-900 mb-1">{{ $partido->goles_local ?? '-' }} &mdash; {{ $partido->goles_visitante ?? '-' }}</div>
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ $partido->fecha_partido->format('d/m/Y H:i') }}
            </div>
        </div>
        <div class="flex flex-col items-center">
            <h3 class="text-lg font-bold text-gray-900">{{ $partido->equipoVisitante?->nombre ?? 'Equipo Visitante' }}</h3>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Estadísticas &mdash; {{ $partido->equipoLocal?->nombre ?? '' }}</h2>
            @if ($statsLocal->count())
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs">
                    <tr><th class="py-3 px-3">Jugador</th><th class="py-3 px-2 text-center">G</th><th class="py-3 px-2 text-center">A</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($statsLocal as $stat)
                    <tr><td class="py-3 px-3">{{ $stat->jugador?->nombre }}</td><td class="py-3 px-2 text-center">{{ $stat->goles }}</td><td class="py-3 px-2 text-center">{{ $stat->asistencias }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-gray-500 text-center py-4">No hay estadísticas.</p>
            @endif
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Estadísticas &mdash; {{ $partido->equipoVisitante?->nombre ?? '' }}</h2>
            @if ($statsVisitante->count())
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs">
                    <tr><th class="py-3 px-3">Jugador</th><th class="py-3 px-2 text-center">G</th><th class="py-3 px-2 text-center">A</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($statsVisitante as $stat)
                    <tr><td class="py-3 px-3">{{ $stat->jugador?->nombre }}</td><td class="py-3 px-2 text-center">{{ $stat->goles }}</td><td class="py-3 px-2 text-center">{{ $stat->asistencias }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-gray-500 text-center py-4">No hay estadísticas.</p>
            @endif
        </div>
    </div>
</div>
@endsection
