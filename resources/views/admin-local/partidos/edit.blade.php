@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Editar Partido</h1>
        <p class="text-gray-500">Actualiza los detalles o el resultado del partido.</p>
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
        <form action="{{ route('admin.local.partidos.update', $partido) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jornada</label>
                <select name="jornada_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Selecciona una jornada</option>
                    @foreach($jornadas as $jornada)
                        <option value="{{ $jornada->id }}" {{ (old('jornada_id', $partido->jornada_id) == $jornada->id) ? 'selected' : '' }}>
                            {{ $jornada->torneo->nombre ?? '' }} - {{ $jornada->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Equipo Local</label>
                    <select name="equipo_local_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Selecciona local</option>
                        @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}" {{ (old('equipo_local_id', $partido->equipo_local_id) == $equipo->id) ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Equipo Visitante</label>
                    <select name="equipo_visitante_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Selecciona visitante</option>
                        @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}" {{ (old('equipo_visitante_id', $partido->equipo_visitante_id) == $equipo->id) ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha y Hora del Partido</label>
                <input type="datetime-local" name="fecha_partido" value="{{ old('fecha_partido', $partido->fecha_partido?->format('Y-m-d\TH:i')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Goles Local</label>
                    <input type="number" name="goles_local" value="{{ old('goles_local', $partido->goles_local) }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Goles Visitante</label>
                    <input type="number" name="goles_visitante" value="{{ old('goles_visitante', $partido->goles_visitante) }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                <select name="estado" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="programado" {{ (old('estado', $partido->estado) == 'programado') ? 'selected' : '' }}>Programado</option>
                    <option value="jugado" {{ (old('estado', $partido->estado) == 'jugado') ? 'selected' : '' }}>Jugado</option>
                    <option value="cancelado" {{ (old('estado', $partido->estado) == 'cancelado') ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="bg-emerald-600 text-white font-semibold px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition shadow-sm">
                    Actualizar Partido
                </button>
                <a href="{{ route('admin.local.partidos.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection


