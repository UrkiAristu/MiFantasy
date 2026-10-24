@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Equipos en el Torneo</h1>
            <p class="text-sm text-gray-500">Listado global de equipos que participan en tus torneos (creados por SuperAdmin).</p>
        </div>
    </div>

    <!-- Filtro de Torneo y BÃºsqueda -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xl flex flex-col md:flex-row gap-4 items-center justify-between">
        <form action="{{ route('admin.local.equipos.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="torneo_id" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-900 rounded-xl text-sm px-3 py-2.5 focus:ring-lime-400 focus:border-lime-400 shadow-sm min-w-[220px]">
                <option value="">ðŸŒ Todos mis Torneos</option>
                @foreach($torneos as $t)
                    <option value="{{ $t->id }}" {{ $torneoId == $t->id ? 'selected' : '' }}>ðŸ† {{ $t->nombre }}</option>
                @endforeach
            </select>

            <div class="relative flex-1 md:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar equipo..." class="bg-white border border-gray-300 text-gray-900 rounded-xl text-sm pl-9 pr-4 py-2.5 w-full focus:ring-lime-400 focus:border-lime-400">
            </div>
            @if(request('search') || request('torneo_id'))
                <a href="{{ route('admin.local.equipos.index') }}" class="px-3 py-2.5 rounded-xl bg-gray-200 text-gray-600 hover:text-white text-xs font-medium transition-colors">Limpiar</a>
            @endif
        </form>
        <span class="text-xs text-gray-500 font-medium">Mostrando {{ $equipos->firstItem() ?? 0 }} - {{ $equipos->lastItem() ?? 0 }} de {{ $equipos->total() }} equipos</span>
    </div>

    <!-- Tabla de Equipos -->
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Equipo</th>
                        <th class="py-3.5 px-6">Torneos Asignados</th>
                        <th class="py-3.5 px-6 text-right">Jugadores</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse($equipos as $equipo)
                    <tr class="hover:bg-gray-200/40 transition-colors group">
                        <td class="py-4 px-6 font-semibold text-gray-900 flex items-center gap-3">
                            @if($equipo->logo)
                                <img src="{{ asset('storage/' . $equipo->logo) }}" alt="{{ $equipo->nombre }}" class="w-9 h-9 rounded-xl object-cover border border-gray-300 bg-gray-200">
                            @else
                                <div class="w-9 h-9 rounded-xl bg-gray-200 flex items-center justify-center text-lime-400 font-bold text-xs border border-gray-300 shadow-inner">
                                    {{ strtoupper(substr($equipo->nombre, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <div class="font-bold text-gray-900">{{ $equipo->nombre }}</div>
                                <div class="text-[11px] text-gray-500 font-normal">ID: #{{ $equipo->id }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-1">
                                @forelse($equipo->torneos as $torneo)
                                    <span class="px-2 py-0.5 rounded-md bg-gray-200 text-gray-600 text-xs border border-gray-300">ðŸ† {{ $torneo->nombre }}</span>
                                @empty
                                    <span class="text-xs text-gray-500 italic">Sin torneo</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right text-gray-600 font-medium">
                            <span class="px-2.5 py-1 rounded-lg bg-gray-200 border border-gray-300 text-xs">
                                <i class="bi bi-people text-lime-400 mr-1"></i> {{ $equipo->jugadores->count() }} jugadores
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-200 flex items-center justify-center text-gray-500 text-xl border border-gray-300">
                                    <i class="bi bi-shield-slash"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-500">No se encontraron equipos registrados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($equipos->hasPages())
            <div class="p-4 border-t border-gray-200 bg-white/40">
                {{ $equipos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


