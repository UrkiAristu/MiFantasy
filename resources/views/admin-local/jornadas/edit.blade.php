@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Editar Jornada</h1>
        <p class="text-gray-500">Actualiza los datos de la jornada.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-6">
        <form action="{{ route('admin.local.jornadas.update', $jornada) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Torneo</label>
                <select name="torneo_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Selecciona un torneo</option>
                    @foreach($torneos as $torneo)
                        <option value="{{ $torneo->id }}" {{ (old('torneo_id', $jornada->torneo_id) == $torneo->id) ? 'selected' : '' }}>{{ $torneo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre de la Jornada</label>
                <input type="text" name="nombre" value="{{ old('nombre', $jornada->nombre) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Orden / Número</label>
                <input type="number" name="orden" value="{{ old('orden', $jornada->orden) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $jornada->fecha_inicio?->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Fin</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $jornada->fecha_fin?->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cierre de Alineaciones</label>
                <input type="datetime-local" name="fecha_cierre_alineaciones" value="{{ old('fecha_cierre_alineaciones', $jornada->fecha_cierre_alineaciones?->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="bg-emerald-600 text-white font-semibold px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition shadow-sm">
                    Actualizar Jornada
                </button>
                <a href="{{ route('admin.local.jornadas.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
