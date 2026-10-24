@extends('admin-local.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.local.equipos.index') }}" class="text-xs font-semibold text-gray-500 hover:text-lime-400 flex items-center gap-1.5 transition-colors mb-2">
                <i class="bi bi-arrow-left"></i> Volver a Equipos
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Inscribir Nuevo Equipo</h1>
            <p class="text-sm text-gray-500">Registra un nuevo equipo y asÃ­gnalo a tus torneos autorizados.</p>
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

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xl">
        <form action="{{ route('admin.local.equipos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Nombre del Equipo -->
            <div>
                <label for="nombre" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                    Nombre del Equipo <span class="text-lime-400">*</span>
                </label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required placeholder="Ej: Real Betis BalompiÃ©" class="bg-white border border-gray-300 text-gray-900 rounded-xl text-sm px-4 py-3 w-full focus:ring-lime-400 focus:border-lime-400 placeholder:text-gray-600 shadow-sm">
            </div>

            <!-- Torneos a Asociar -->
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                    Asignar a Torneo(s) <span class="text-lime-400">*</span>
                </label>
                <p class="text-xs text-gray-500 mb-3">Selecciona al menos uno de los torneos que gestionas.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-56 overflow-y-auto p-1">
                    @forelse($torneos as $t)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-gray-50 hover:border-gray-300 cursor-pointer transition-colors">
                            <input type="checkbox" name="torneos[]" value="{{ $t->id }}" {{ (is_array(old('torneos')) && in_array($t->id, old('torneos'))) ? 'checked' : '' }} class="rounded bg-gray-200 border-gray-300 text-lime-400 focus:ring-lime-400 focus:ring-offset-gray-900 h-4 w-4">
                            <span class="text-sm font-semibold text-gray-900">ðŸ† {{ $t->nombre }}</span>
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
                <label for="logo" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                    Escudo / Logo (Opcional)
                </label>
                <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-900 hover:file:bg-gray-700 file:transition-colors file:cursor-pointer">
                <p class="text-[11px] text-gray-500 mt-1.5">Formatos permitidos: JPG, PNG, GIF. MÃ¡ximo 2MB.</p>
            </div>

            <!-- Botones de AcciÃ³n -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.local.equipos.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-200 text-gray-600 hover:text-white hover:bg-gray-700 transition-colors text-sm font-semibold">
                    Cancelar
                </a>
                <button type="submit" class="bg-lime-400 text-gray-900 font-bold px-6 py-2.5 rounded-xl hover:bg-lime-300 transition-colors text-sm shadow-lg shadow-lime-400/10 flex items-center gap-2">
                    <i class="bi bi-check-lg"></i> Guardar Equipo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


