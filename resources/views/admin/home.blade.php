@extends('admin.layouts.app')

@section('title', 'Inicio | Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header con Logo MiFantasy controlado -->
    <div class="text-center space-y-3">
        <div class="inline-block">
            <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy" class="w-12 h-12 max-w-[48px] max-h-[48px] object-contain">
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-xs font-semibold uppercase tracking-wider">
            <i class="bi bi-shield-lock"></i>
            <span>Zona de Administración</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-zinc-100 tracking-tight">
            Panel de Administración
        </h1>
        <p class="text-xs sm:text-sm text-zinc-400 max-w-md mx-auto">
            Gestiona usuarios, torneos, equipos, jugadores y liguillas de la plataforma.
        </p>
    </div>

    <!-- Grid Sólido de Tarjetas del Panel -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Usuarios -->
        <div class="group relative bg-zinc-900/60 hover:bg-zinc-900/90 border border-zinc-800 hover:border-lime-400/50 rounded-2xl p-6 transition-all duration-300 shadow-lg hover:shadow-lime-400/5 hover:-translate-y-1 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Usuarios
                    </h2>
                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                        Gestiona los usuarios registrados en el sistema, permisos y estado de cuentas.
                    </p>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/admin/usuarios') }}" class="w-full py-2.5 px-4 bg-zinc-800/60 hover:bg-lime-400 text-zinc-200 hover:text-zinc-950 font-semibold text-xs rounded-xl border border-zinc-700/60 hover:border-lime-400 transition-all flex items-center justify-center gap-2">
                    <span>Ver Usuarios</span>
                    <i class="bi bi-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        <!-- Liguillas -->
        <div class="group relative bg-zinc-900/60 hover:bg-zinc-900/90 border border-zinc-800 hover:border-lime-400/50 rounded-2xl p-6 transition-all duration-300 shadow-lg hover:shadow-lime-400/5 hover:-translate-y-1 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-award"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Liguillas
                    </h2>
                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                        Revisa y administra todas las liguillas creadas por los usuarios de la comunidad.
                    </p>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/admin/liguillas') }}" class="w-full py-2.5 px-4 bg-zinc-800/60 hover:bg-lime-400 text-zinc-200 hover:text-zinc-950 font-semibold text-xs rounded-xl border border-zinc-700/60 hover:border-lime-400 transition-all flex items-center justify-center gap-2">
                    <span>Ver Liguillas</span>
                    <i class="bi bi-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        <!-- Torneos -->
        <div class="group relative bg-zinc-900/60 hover:bg-zinc-900/90 border border-zinc-800 hover:border-lime-400/50 rounded-2xl p-6 transition-all duration-300 shadow-lg hover:shadow-lime-400/5 hover:-translate-y-1 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Torneos
                    </h2>
                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                        Administra competiciones oficiales, calendarios, jornadas y modalidades.
                    </p>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/admin/torneos') }}" class="w-full py-2.5 px-4 bg-zinc-800/60 hover:bg-lime-400 text-zinc-200 hover:text-zinc-950 font-semibold text-xs rounded-xl border border-zinc-700/60 hover:border-lime-400 transition-all flex items-center justify-center gap-2">
                    <span>Ver Torneos</span>
                    <i class="bi bi-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        <!-- Equipos -->
        <div class="group relative bg-zinc-900/60 hover:bg-zinc-900/90 border border-zinc-800 hover:border-lime-400/50 rounded-2xl p-6 transition-all duration-300 shadow-lg hover:shadow-lime-400/5 hover:-translate-y-1 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-shield-fill"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Equipos
                    </h2>
                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                        Controla los clubes y equipos inscritos en cada competición y sus escudos.
                    </p>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/admin/equipos') }}" class="w-full py-2.5 px-4 bg-zinc-800/60 hover:bg-lime-400 text-zinc-200 hover:text-zinc-950 font-semibold text-xs rounded-xl border border-zinc-700/60 hover:border-lime-400 transition-all flex items-center justify-center gap-2">
                    <span>Ver Equipos</span>
                    <i class="bi bi-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        <!-- Jugadores -->
        <div class="group relative bg-zinc-900/60 hover:bg-zinc-900/90 border border-zinc-800 hover:border-lime-400/50 rounded-2xl p-6 transition-all duration-300 shadow-lg hover:shadow-lime-400/5 hover:-translate-y-1 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Jugadores
                    </h2>
                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                        Administra los jugadores, posiciones, valoraciones y estadísticas de cada plantilla.
                    </p>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/admin/jugadores') }}" class="w-full py-2.5 px-4 bg-zinc-800/60 hover:bg-lime-400 text-zinc-200 hover:text-zinc-950 font-semibold text-xs rounded-xl border border-zinc-700/60 hover:border-lime-400 transition-all flex items-center justify-center gap-2">
                    <span>Ver Jugadores</span>
                    <i class="bi bi-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection