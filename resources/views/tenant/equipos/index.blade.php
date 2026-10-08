@extends('tenant.layouts.app')

@section('title', 'Gestión de Equipos')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Equipos</h1>
            <p class="text-sm text-zinc-400">Gestiona los equipos inscritos en tu competición.</p>
        </div>
        <a href="{{ route('tenant.equipos.create') }}" class="bg-lime-400 text-zinc-950 px-4 py-2 rounded-xl font-semibold text-sm hover:bg-lime-300 transition-colors flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Añadir Equipo
        </a>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 bg-zinc-950/50 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Nombre del Equipo</th>
                        <th class="py-3.5 px-6">Estado</th>
                        <th class="py-3.5 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800 text-sm">
                    @forelse($equipos as $equipo)
                    <tr class="hover:bg-zinc-800/40 transition-colors group">
                        <td class="py-4 px-6 font-semibold text-zinc-100 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-lime-400 font-bold text-xs border border-zinc-700">
                                {{ strtoupper(substr($equipo->nombre, 0, 2)) }}
                            </div>
                            {{ $equipo->nombre }}
                        </td>
                        <td class="py-4 px-6 text-zinc-400 text-xs">
                            <span class="px-2.5 py-1 rounded-md bg-lime-400/10 text-lime-400 border border-lime-400/20 font-semibold">Inscrito</span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            <a href="{{ route('tenant.equipos.edit', $equipo) }}" class="px-3 py-1.5 rounded-lg bg-zinc-800 text-zinc-300 hover:text-white hover:bg-zinc-700 transition-colors text-xs font-medium">Editar</a>
                            <form action="{{ route('tenant.equipos.destroy', $equipo) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este equipo?')" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors text-xs font-medium">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-12 text-center text-zinc-500">
                            No hay equipos registrados todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
