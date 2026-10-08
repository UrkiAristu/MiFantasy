@extends('tenant.layouts.app')

@section('title', 'Añadir Equipo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        <h1 class="text-xl font-bold text-white mb-6">Añadir Nuevo Equipo</h1>

        <form action="{{ route('tenant.equipos.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-zinc-400 mb-1">Nombre del Equipo</label>
                <input type="text" name="nombre" class="w-full bg-zinc-950 border border-zinc-700 rounded-lg p-2.5 text-white focus:ring-2 focus:ring-lime-400 focus:border-transparent" required>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('tenant.equipos.index') }}" class="px-4 py-2 rounded-lg bg-zinc-800 text-zinc-300 hover:text-white font-medium">Cancelar</a>
                <button type="submit" class="bg-lime-400 text-zinc-950 px-6 py-2 rounded-lg font-semibold hover:bg-lime-300 transition-colors">Guardar Equipo</button>
            </div>
        </form>
    </div>
</div>
@endsection
