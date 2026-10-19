@extends('admin-local.layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Panel de Competición Local</h1>
        <p class="text-gray-500">Bienvenido, {{ Auth::user()->name }}</p>
    </div>

    <!-- Grid de 4 KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <!-- Torneos Activos -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
            <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg text-xl flex items-center justify-center">
                <i class="bi bi-trophy"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Torneos Activos</p>
                <h4 class="text-2xl font-bold text-gray-900">1</h4>
            </div>
        </div>

        <!-- Equipos Inscritos -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
            <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg text-xl flex items-center justify-center">
                <i class="bi bi-shield"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Equipos Inscritos</p>
                <h4 class="text-2xl font-bold text-gray-900">20</h4>
            </div>
        </div>

        <!-- Jugadores en Base -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
            <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg text-xl flex items-center justify-center">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Jugadores en Base</p>
                <h4 class="text-2xl font-bold text-gray-900">140</h4>
            </div>
        </div>

        <!-- Partidos Pendientes -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5 flex items-center gap-4">
            <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg text-xl flex items-center justify-center">
                <i class="bi bi-calendar"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Partidos Pendientes</p>
                <h4 class="text-2xl font-bold text-gray-900">10</h4>
            </div>
        </div>
    </div>

    <!-- Grid de Contenido (2/3 izquierda, 1/3 derecha) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Izquierda (Colspan 2 - Últimos Partidos) -->
        <div class="lg:col-span-2 bg-white border border-gray-200 shadow-sm rounded-xl p-6 flex flex-col justify-between">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Últimos Partidos</h3>
            <div class="border-dashed border-2 border-gray-300 bg-gray-50 rounded-xl py-16 flex flex-col items-center justify-center text-center">
                <i class="bi bi-inbox text-4xl text-gray-400 mb-2"></i>
                <p class="text-gray-500 font-medium">No hay partidos recientes en tu torneo</p>
                <button type="button" class="bg-emerald-600 text-white font-semibold px-5 py-2.5 rounded-lg mt-4 hover:bg-emerald-700 transition">
                    Registrar Partido
                </button>
            </div>
        </div>

        <!-- Derecha (Acciones Rápidas) -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Gestión Rápida</h3>
            <div class="space-y-2">
                <button type="button" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-emerald-700 rounded-lg transition-colors font-medium">
                    Ver Equipos
                </button>
                <button type="button" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-emerald-700 rounded-lg transition-colors font-medium">
                    Crear Jornada
                </button>
                <button type="button" class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-emerald-700 rounded-lg transition-colors font-medium">
                    Cerrar Clasificación
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
