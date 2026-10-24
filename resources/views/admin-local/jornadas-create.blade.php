@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Crear Nueva Jornada</h1>
        <p class="text-gray-500">Define los parÃ¡metros de la nueva jornada para este torneo.</p>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-6">
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre de la Jornada</label>
                <input type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej: Jornada 4">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Inicio</label>
                    <input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Fin</label>
                    <input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cierre de Alineaciones</label>
                <input type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="bg-emerald-600 text-white font-semibold px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition">
                    Crear Jornada
                </button>
                <a href="{{ route('admin.local.dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection


