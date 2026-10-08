@extends('tenant.layouts.app')

@section('title', 'Panel de Competición')

@section('content')
<div class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Torneos Activos --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 flex items-center justify-between">
            <div>
                <p class="text-zinc-400 text-[11px] font-bold uppercase tracking-wider">Torneos Activos</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $data['torneos'] ?? 0 }}</p>
            </div>
            <div class="p-3 rounded-xl bg-lime-400/10 text-lime-400">
                <i class="bi bi-trophy text-xl"></i>
            </div>
        </div>
        {{-- Equipos Inscritos --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 flex items-center justify-between">
            <div>
                <p class="text-zinc-400 text-[11px] font-bold uppercase tracking-wider">Equipos Inscritos</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $data['equipos'] ?? 0 }}</p>
            </div>
            <div class="p-3 rounded-xl bg-lime-400/10 text-lime-400">
                <i class="bi bi-shield text-xl"></i>
            </div>
        </div>
        {{-- Jugadores en Base --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 flex items-center justify-between">
            <div>
                <p class="text-zinc-400 text-[11px] font-bold uppercase tracking-wider">Jugadores en Base</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $data['jugadores'] ?? 0 }}</p>
            </div>
            <div class="p-3 rounded-xl bg-lime-400/10 text-lime-400">
                <i class="bi bi-people text-xl"></i>
            </div>
        </div>
        {{-- Partidos Pendientes --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 flex items-center justify-between">
            <div>
                <p class="text-zinc-400 text-[11px] font-bold uppercase tracking-wider">Partidos Registrados</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $data['partidos'] ?? 0 }}</p>
            </div>
            <div class="p-3 rounded-xl bg-lime-400/10 text-lime-400">
                <i class="bi bi-calendar-check text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Panel de Acciones Rápidas y Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Izquierda (Últimos Movimientos) -->
        <div class="lg:col-span-2 bg-zinc-900/50 border-dashed border-2 border-zinc-800 rounded-2xl p-10 flex flex-col items-center justify-center text-center">
             <i class="bi bi-calendar-x text-5xl text-zinc-800 mb-4"></i>
             <h3 class="text-white font-semibold text-lg">No hay partidos recientes</h3>
             <p class="text-zinc-400 mb-6 max-w-sm">Empieza gestionando tus jornadas para ver actividad aquí.</p>
             <button class="bg-lime-400 text-zinc-950 px-6 py-2.5 rounded-lg font-semibold hover:bg-lime-300 transition-colors">Añadir Jornada</button>
        </div>

        <!-- Columna Derecha (Acciones Rápidas) -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-zinc-100 font-semibold mb-5 flex items-center gap-2">
                <i class="bi bi-lightning-charge text-lime-400"></i> Acciones Rápidas
            </h3>
            <div class="space-y-2">
                <button class="w-full text-left p-3 rounded-xl bg-zinc-800/50 text-zinc-300 hover:text-white hover:bg-zinc-800 transition-all font-medium">Añadir Equipo</button>
                <button class="w-full text-left p-3 rounded-xl bg-zinc-800/50 text-zinc-300 hover:text-white hover:bg-zinc-800 transition-all font-medium">Crear Jornada</button>
                <button class="w-full text-left p-3 rounded-xl bg-lime-400/10 text-lime-400 border border-lime-400/20 hover:bg-lime-400 hover:text-zinc-950 font-semibold transition-all">Cerrar Clasificación</button>
            </div>
        </div>
    </div>
</div>
@endsection
