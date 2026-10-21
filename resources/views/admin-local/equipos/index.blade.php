@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Equipos en el Torneo</h1>
            <p class="text-sm text-zinc-400">Listado global de equipos que participan en tus torneos (creados por SuperAdmin).</p>
        </div>
    </div>

    <!-- Filtro de Torneo y Búsqueda -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 shadow-xl flex flex-col md:flex-row gap-4 items-center justify-between">
        <form action="{{ route('admin.local.equipos.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="torneo_id" onchange="this.form.submit()" class="bg-zinc-950 border border-zinc-700 text-zinc-100 rounded-xl text-sm px-3 py-2.5 focus:ring-lime-400 focus:border-lime-400 shadow-sm min-w-[220px]">
                <option value="">🌐 Todos mis Torneos</option>
                @foreach($torneos as $t)
                    <option value="{{ $t->id }}" {{ $torneoId == $t->id ? 'selected' : '' }}>🏆 {{ $t->nombre }}</option>
                @endforeach
            </select>

            <div class="relative flex-1 md:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar equipo..." class="bg-zinc-950 border border-zinc-700 text-zinc-100 rounded-xl text-sm pl-9 pr-4 py-2.5 w-full focus:ring-lime-400 focus:border-lime-400">
            </div>
            @if(request('search') || request('torneo_id'))
                <a href="{{ route('admin.local.equipos.index') }}" class="px-3 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 hover:text-white text-xs font-medium transition-colors">Limpiar</a>
            @endif
        </form>
        <span class="text-xs text-zinc-400 font-medium">Mostrando {{ $equipos->firstItem() ?? 0 }} - {{ $equipos->lastItem() ?? 0 }} de {{ $equipos->total() }} equipos</span>
    </div>

    <!-- Tabla de Equipos -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 bg-zinc-950/60 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Equipo</th>
                        <th class="py-3.5 px-6">Torneos Asignados</th>
                        <th class="py-3.5 px-6 text-right">Jugadores</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm">
                    @forelse($equipos as $equipo)
                    <tr class="hover:bg-zinc-800/40 transition-colors group">
                        <td class="py-4 px-6 font-semibold text-zinc-100 flex items-center gap-3">
                            @if($equipo->logo)
                                <img src="{{ asset('storage/' . $equipo->logo) }}" alt="{{ $equipo->nombre }}" class="w-9 h-9 rounded-xl object-cover border border-zinc-700 bg-zinc-800">
                            @else
                                <div class="w-9 h-9 rounded-xl bg-zinc-800 flex items-center justify-center text-lime-400 font-bold text-xs border border-zinc-700 shadow-inner">
                                    {{ strtoupper(substr($equipo->nombre, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <div class="font-bold text-zinc-100">{{ $equipo->nombre }}</div>
                                <div class="text-[11px] text-zinc-500 font-normal">ID: #{{ $equipo->id }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-1">
                                @forelse($equipo->torneos as $torneo)
                                    <span class="px-2 py-0.5 rounded-md bg-zinc-800 text-zinc-300 text-xs border border-zinc-700">🏆 {{ $torneo->nombre }}</span>
                                @empty
                                    <span class="text-xs text-zinc-500 italic">Sin torneo</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right text-zinc-300 font-medium">
                            <span class="px-2.5 py-1 rounded-lg bg-zinc-800 border border-zinc-700 text-xs">
                                <i class="bi bi-people text-lime-400 mr-1"></i> {{ $equipo->jugadores->count() }} jugadores
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-16 text-center text-zinc-500">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-800 flex items-center justify-center text-zinc-400 text-xl border border-zinc-700">
                                    <i class="bi bi-shield-slash"></i>
                                </div>
                                <p class="text-sm font-medium text-zinc-400">No se encontraron equipos registrados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($equipos->hasPages())
            <div class="p-4 border-t border-zinc-800 bg-zinc-950/40">
                {{ $equipos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
