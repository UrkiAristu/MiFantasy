@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Dashboard Ejecutivo</h1>
            <p class="text-zinc-400">Bienvenido de nuevo, {{ Auth::user()->name }}</p>
        </div>

        <form action="{{ route('admin.local.dashboard') }}" method="GET" class="flex items-center gap-2">
            <select name="torneo_id" onchange="this.form.submit()" class="bg-zinc-950 border border-zinc-700 text-zinc-100 rounded-xl text-sm px-3 py-2.5 focus:ring-lime-400 focus:border-lime-400 shadow-sm min-w-[240px]">
                <option value="">🌐 Todos los Torneos</option>
                @foreach($torneos as $t)
                    <option value="{{ $t->id }}" {{ $torneoId == $t->id ? 'selected' : '' }}>🏆 {{ $t->nombre }}</option>
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
            <div class="bg-zinc-900 border border-zinc-800 shadow-sm rounded-2xl p-5 hover:border-{{ $kpi['color'] }}-500 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="bg-{{ $kpi['color'] }}-500/10 text-{{ $kpi['color'] }}-400 p-3 rounded-xl">
                        <i class="bi {{ $kpi['icon'] }} text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-400">{{ $kpi['label'] }}</p>
                        <h4 class="text-2xl font-bold text-zinc-100">{{ $kpi['value'] }}</h4>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Próxima Jornada Widget -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        <h3 class="text-xl font-bold text-zinc-100 mb-6">Próximos Partidos</h3>
        @forelse($proximosPartidos as $partido)
            <div class="border-b border-zinc-800 py-4 flex items-center justify-between group last:border-0">
                <div class="flex items-center gap-4 flex-1">
                    <div class="text-center w-24">
                        <p class="text-lg font-bold text-zinc-100">{{ $partido->equipoLocal->nombre }}</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-xs font-bold text-zinc-500 uppercase">VS</span>
                        <span class="text-xs text-zinc-400">{{ $partido->fecha_partido->format('H:i') }}</span>
                    </div>
                    <div class="text-center w-24">
                        <p class="text-lg font-bold text-zinc-100">{{ $partido->equipoVisitante->nombre }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-zinc-300">{{ $partido->jornada->nombre }}</p>
                    <p class="text-xs text-zinc-500">{{ $partido->jornada->torneo->nombre }}</p>
                </div>
            </div>
        @empty
            <div class="border-dashed border-2 border-zinc-800 rounded-2xl py-16 flex flex-col items-center justify-center text-center">
                <i class="bi bi-calendar-x text-5xl text-zinc-700 mb-4"></i>
                <p class="text-zinc-400 font-medium text-lg">No hay partidos programados</p>
                <a href="{{ route('admin.local.partidos.create') }}" class="bg-lime-400 text-zinc-950 font-semibold px-6 py-3 rounded-xl mt-6 hover:bg-lime-300 transition-colors">
                    Registrar Partido
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
