@extends('tenant.layouts.app')

@section('title', 'Editar Equipo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        <h1 class="text-xl font-bold text-white mb-6">Editar Equipo</h1>

        <form action="{{ route('tenant.equipos.update', $equipo) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-zinc-400 mb-1">Nombre del Equipo</label>
                <input type="text" name="nombre" value="{{ $equipo->nombre }}" class="w-full bg-zinc-900 border border-zinc-700 rounded-lg p-2.5 text-zinc-100 focus:border-lime-400 focus:ring-1 focus:ring-lime-400 outline-none" required>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('tenant.equipos.index') }}" class="px-4 py-2 rounded-lg bg-zinc-800 text-zinc-300 hover:text-white font-medium">Cancelar</a>
                <button type="submit" class="bg-lime-400 text-zinc-950 px-6 py-2 rounded-lg font-semibold hover:bg-lime-300 transition-colors">Actualizar Equipo</button>
            </div>
        </form>
    </div>
</div>
@endsection
