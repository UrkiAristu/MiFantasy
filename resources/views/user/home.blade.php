@extends('user.layouts.app')

@section('title', 'Dashboard - MiFantasy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

    <!-- Hero Banner Bento -->
    <div class="relative overflow-hidden rounded-3xl bg-zinc-900/70 border border-zinc-800/80 backdrop-blur-xl p-6 sm:p-10 shadow-2xl shadow-zinc-950/60">
        <!-- Glow ambiental decorativo -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-lime-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-3 text-center md:text-left max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-xs font-semibold">
                    <i class="bi bi-stars"></i>
                    <span>Panel de Competición</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-zinc-100">
                    ¡Bienvenido, <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-400 to-emerald-400">{{ Auth::user()->name ?? 'Jugador' }}</span>!
                </h1>
                <p class="text-sm sm:text-base text-zinc-400 leading-relaxed">
                    Gestiona tus liguillas, ficha a los mejores jugadores y lidera las tablas de clasificación de tus torneos favoritos.
                </p>
            </div>

            <div class="shrink-0 flex items-center justify-center">
                <div class="relative group">
                    <div class="absolute inset-0 bg-lime-400/20 rounded-2xl blur-xl group-hover:bg-lime-400/30 transition-all"></div>
                    <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy" class="relative w-28 h-28 sm:w-36 sm:h-36 rounded-2xl object-contain ring-1 ring-zinc-800 bg-zinc-950/60 p-3 shadow-xl">
                </div>
            </div>
        </div>
    </div>

    <!-- Bento Grid Acciones Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Card 1: Mis Liguillas -->
        <div class="group relative rounded-2xl bg-zinc-900/50 border border-zinc-800/80 hover:border-zinc-700/80 p-6 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-zinc-950/50">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Mis Liguillas
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        Consulta el estado de las liguillas en las que estás compitiendo, clasificaciones y alineaciones activas.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/user/liguillas') }}" class="w-full py-2.5 px-4 bg-zinc-800/60 hover:bg-zinc-800 text-zinc-200 hover:text-zinc-100 font-semibold text-xs rounded-xl border border-zinc-700/60 hover:border-zinc-600 transition-all flex items-center justify-center gap-2">
                    <span>Ver mis liguillas</span>
                    <i class="bi bi-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        <!-- Card 2: Crear Liguilla -->
        <div class="group relative rounded-2xl bg-zinc-900/50 border border-zinc-800/80 hover:border-lime-500/30 p-6 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-zinc-950/50">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Crear Liguilla
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        Selecciona un torneo oficial activo, define las normas de fichajes y crea una nueva liga privada con tus amigos.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/user/torneos') }}" class="w-full py-2.5 px-4 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-semibold text-xs rounded-xl shadow-lg shadow-lime-400/10 hover:shadow-lime-400/20 transition-all flex items-center justify-center gap-2">
                    <span>Explorar torneos y crear</span>
                    <i class="bi bi-plus-lg text-sm font-bold"></i>
                </a>
            </div>
        </div>

        <!-- Card 3: Unirme a Liguilla -->
        <div class="group relative rounded-2xl bg-zinc-900/50 border border-zinc-800/80 hover:border-zinc-700/80 p-6 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-zinc-950/50">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-bookmark-plus"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Unirme a Liguilla
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        ¿Tienes un código o nombre de liga? Únete a una liguilla existente y empieza a competir de inmediato.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/user/unirseLiguilla') }}" class="w-full py-2.5 px-4 bg-zinc-800/60 hover:bg-zinc-800 text-zinc-200 hover:text-zinc-100 font-semibold text-xs rounded-xl border border-zinc-700/60 hover:border-zinc-600 transition-all flex items-center justify-center gap-2">
                    <span>Unirse con código</span>
                    <i class="bi bi-door-open text-sm"></i>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
