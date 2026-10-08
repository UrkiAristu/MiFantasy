@extends('tenant.layouts.app')

@section('title', 'Editar Jugador')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        <h1 class="text-xl font-bold text-white mb-6">Editar Jugador</h1>

        <form action="{{ route('tenant.jugadores.update', $jugador) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-zinc-400 mb-1">Nombre</label>
                <input type="text" name="nombre" value="{{ $jugador->nombre }}" class="w-full bg-zinc-900 border border-zinc-700 rounded-lg p-2.5 text-zinc-100 focus:border-lime-400 focus:ring-1 focus:ring-lime-400 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-400 mb-1">Posición</label>
                <input type="text" name="posicion" value="{{ $jugador->posicion }}" class="w-full bg-zinc-900 border border-zinc-700 rounded-lg p-2.5 text-zinc-100 focus:border-lime-400 focus:ring-1 focus:ring-lime-400 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-400 mb-1">Valor</label>
                <input type="number" name="valor" value="{{ $jugador->valor }}" class="w-full bg-zinc-900 border border-zinc-700 rounded-lg p-2.5 text-zinc-100 focus:border-lime-400 focus:ring-1 focus:ring-lime-400 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-400 mb-1">Equipo</label>
                <select name="equipo_id" class="w-full bg-zinc-900 border border-zinc-700 rounded-lg p-2.5 text-zinc-100 focus:border-lime-400 focus:ring-1 focus:ring-lime-400 outline-none" required>
                    <option value="">Selecciona un equipo</option>
                    @foreach($equipos as $equipo)
                        <option value="{{ $equipo->id }}" {{ $jugador->equipo->contains($equipo->id) ? 'selected' : '' }}>{{ $equipo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('tenant.jugadores.index') }}" class="px-4 py-2 rounded-lg bg-zinc-800 text-zinc-300 hover:text-white font-medium">Cancelar</a>
                <button type="submit" class="bg-lime-400 text-zinc-950 px-6 py-2 rounded-lg font-semibold hover:bg-lime-300 transition-colors">Actualizar Jugador</button>
            </div>
        </form>
    </div>
</div>
@endsection
