@extends('admin-local.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.local.equipos.index') }}" class="text-xs font-semibold text-zinc-400 hover:text-lime-400 flex items-center gap-1.5 transition-colors mb-2">
                <i class="bi bi-arrow-left"></i> Volver a Equipos
            </a>
            <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Inscribir Nuevo Equipo</h1>
            <p class="text-sm text-zinc-400">Registra un nuevo equipo y asígnalo a tus torneos autorizados.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-2xl p-4 text-sm">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle"></i> Revisa los siguientes errores:
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-xl">
        <form action="{{ route('admin.local.equipos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Nombre del Equipo -->
            <div>
                <label for="nombre" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                    Nombre del Equipo <span class="text-lime-400">*</span>
                </label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required placeholder="Ej: Real Betis Balompié" class="bg-zinc-950 border border-zinc-700 text-zinc-100 rounded-xl text-sm px-4 py-3 w-full focus:ring-lime-400 focus:border-lime-400 placeholder:text-zinc-600 shadow-sm">
            </div>

            <!-- Torneos a Asociar -->
            <div>
                <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                    Asignar a Torneo(s) <span class="text-lime-400">*</span>
                </label>
                <p class="text-xs text-zinc-500 mb-3">Selecciona al menos uno de los torneos que gestionas.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-56 overflow-y-auto p-1">
                    @forelse($torneos as $t)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-zinc-800 bg-zinc-950/60 hover:border-zinc-700 cursor-pointer transition-colors">
                            <input type="checkbox" name="torneos[]" value="{{ $t->id }}" {{ (is_array(old('torneos')) && in_array($t->id, old('torneos'))) ? 'checked' : '' }} class="rounded bg-zinc-800 border-zinc-700 text-lime-400 focus:ring-lime-400 focus:ring-offset-zinc-900 h-4 w-4">
                            <span class="text-sm font-semibold text-zinc-200">🏆 {{ $t->nombre }}</span>
                        </label>
                    @empty
                        <div class="col-span-2 text-xs text-amber-400 bg-amber-400/10 border border-amber-400/20 p-3 rounded-xl">
                            No tienes torneos asignados actualmente. Contacta al administrador para vincularte a un torneo.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Escudo / Logo -->
            <div>
                <label for="logo" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                    Escudo / Logo (Opcional)
                </label>
                <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-xs text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-200 hover:file:bg-zinc-700 file:transition-colors file:cursor-pointer">
                <p class="text-[11px] text-zinc-500 mt-1.5">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</p>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800">
                <a href="{{ route('admin.local.equipos.index') }}" class="px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 hover:text-white hover:bg-zinc-700 transition-colors text-sm font-semibold">
                    Cancelar
                </a>
                <button type="submit" class="bg-lime-400 text-zinc-950 font-bold px-6 py-2.5 rounded-xl hover:bg-lime-300 transition-colors text-sm shadow-lg shadow-lime-400/10 flex items-center gap-2">
                    <i class="bi bi-check-lg"></i> Guardar Equipo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
