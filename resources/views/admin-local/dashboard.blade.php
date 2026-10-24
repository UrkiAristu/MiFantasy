@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard Ejecutivo</h1>
            <p class="text-gray-500">Bienvenido de nuevo, {{ Auth::user()->name }}</p>
        </div>

        <form action="{{ route('admin.local.dashboard') }}" method="GET" class="flex items-center gap-2">
            <select name="torneo_id" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-900 rounded-xl text-sm px-3 py-2.5 focus:ring-lime-400 focus:border-lime-400 shadow-sm min-w-[240px]">
                <option value="">ðŸŒ Todos los Torneos</option>
                @foreach($torneos as $t)
                    <option value="{{ $t->id }}" {{ $torneoId == $t->id ? 'selected' : '' }}>ðŸ† {{ $t->nombre }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $kpis = [
                ['label' => 'Torneos Activos', 'value' => $torneosActivos, 'icon' => 'bi-trophy', 'color' => 'lime'],
                ['label' => 'Equipos', 'value' => $equiposInscritos, 'icon' => 'bi-shield', 'color' => 'blue'],
                ['label' => 'Jornadas Comp.', 'value' => $jornadasCompletadas, 'icon' => 'bi-calendar-check', 'color' => 'emerald'],
                ['label' => 'Partidos Pend.', 'value' => $partidosPendientes, 'icon' => 'bi-calendar-event', 'color' => 'amber'],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-5 hover:border-{{ $kpi['color'] }}-500 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="bg-{{ $kpi['color'] }}-500/10 text-{{ $kpi['color'] }}-400 p-3 rounded-xl">
                        <i class="bi {{ $kpi['icon'] }} text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ $kpi['label'] }}</p>
                        <h4 class="text-2xl font-bold text-gray-900">{{ $kpi['value'] }}</h4>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- PrÃ³xima Jornada Widget -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6">PrÃ³ximos Partidos</h3>
        @forelse($proximosPartidos as $partido)
            <div class="border-b border-gray-200 py-4 flex items-center justify-between group last:border-0">
                <div class="flex items-center gap-4 flex-1">
                    <div class="text-center w-24">
                        <p class="text-lg font-bold text-gray-900">{{ $partido->equipoLocal->nombre }}</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-xs font-bold text-gray-500 uppercase">VS</span>
                        <span class="text-xs text-gray-500">{{ $partido->fecha_partido->format('H:i') }}</span>
                    </div>
                    <div class="text-center w-24">
                        <p class="text-lg font-bold text-gray-900">{{ $partido->equipoVisitante->nombre }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-600">{{ $partido->jornada->nombre }}</p>
                    <p class="text-xs text-gray-500">{{ $partido->jornada->torneo->nombre }}</p>
                </div>
            </div>
        @empty
            <div class="border-dashed border-2 border-gray-200 rounded-2xl py-16 flex flex-col items-center justify-center text-center">
                <i class="bi bi-calendar-x text-5xl text-gray-700 mb-4"></i>
                <p class="text-gray-500 font-medium text-lg">No hay partidos programados</p>
                <a href="{{ route('admin.local.partidos.create') }}" class="bg-lime-400 text-gray-900 font-semibold px-6 py-3 rounded-xl mt-6 hover:bg-lime-300 transition-colors">
                    Registrar Partido
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection


