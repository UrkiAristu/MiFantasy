@extends('admin.layouts.app')

@section('title', 'Inicio | Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header -->
    <div class="text-center space-y-3">
        <div class="inline-flex items-center gap-2 bg-lime-100 text-lime-800 border border-lime-300 px-3 py-1 rounded-full text-xs font-bold">
            <i class="bi bi-shield-lock"></i>
            <span>Zona de Administración</span>
        </div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Panel Premium</h1>
    </div>

    <!-- Bento Box Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">

        <!-- Usuarios: lg:col-span-3 -->
        <div class="lg:col-span-3 group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:-translate-y-1 hover:border-lime-500 hover:shadow-md transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl text-lime-600">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Usuarios</h2>
                    <p class="text-slate-500 text-sm mt-1">Gestión completa de usuarios registrados, permisos y estados.</p>
                </div>
            </div>
            <a href="{{ url('/admin/usuarios') }}" class="mt-6 block w-full text-center py-2 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold transition-colors">
                Gestionar Usuarios
            </a>
        </div>

        <!-- Liguillas: lg:col-span-3 -->
        <div class="lg:col-span-3 group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:-translate-y-1 hover:border-lime-500 hover:shadow-md transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl text-lime-600">
                    <i class="bi bi-award"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Liguillas</h2>
                    <p class="text-slate-500 text-sm mt-1">Administración de liguillas creadas por la comunidad.</p>
                </div>
            </div>
            <a href="{{ url('/admin/liguillas') }}" class="mt-6 block w-full text-center py-2 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold transition-colors">
                Gestionar Liguillas
            </a>
        </div>

        <!-- Torneos: lg:col-span-2 -->
        <div class="lg:col-span-2 group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:-translate-y-1 hover:border-lime-500 hover:shadow-md transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-xl text-lime-600">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Torneos</h2>
                    <p class="text-slate-500 text-sm mt-1">Competiciones y jornadas.</p>
                </div>
            </div>
            <a href="{{ url('/admin/torneos') }}" class="mt-4 block w-full text-center py-2 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Gestionar Torneos
            </a>
        </div>

        <!-- Equipos: lg:col-span-2 -->
        <div class="lg:col-span-2 group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:-translate-y-1 hover:border-lime-500 hover:shadow-md transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-xl text-lime-600">
                    <i class="bi bi-shield-fill"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Equipos</h2>
                    <p class="text-slate-500 text-sm mt-1">Clubes y escudos.</p>
                </div>
            </div>
            <a href="{{ url('/admin/equipos') }}" class="mt-4 block w-full text-center py-2 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Gestionar Equipos
            </a>
        </div>

        <!-- Jugadores: lg:col-span-2 -->
        <div class="lg:col-span-2 group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:-translate-y-1 hover:border-lime-500 hover:shadow-md transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-xl text-lime-600">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Jugadores</h2>
                    <p class="text-slate-500 text-sm mt-1">Plantillas y estadísticas.</p>
                </div>
            </div>
            <a href="{{ url('/admin/jugadores') }}" class="mt-4 block w-full text-center py-2 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Gestionar Jugadores
            </a>
        </div>

    </div>

</div>
@endsection
