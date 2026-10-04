@extends('user.layouts.app')

@section('title', 'Dashboard - MiFantasy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

    <!-- Hero Banner Bento Premium -->
    <div class="relative overflow-hidden rounded-3xl bg-zinc-900/60 border border-zinc-800/80 backdrop-blur-2xl p-6 sm:p-10 shadow-2xl shadow-zinc-950/80">
        <!-- Glow ambiental decorativo -->
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-lime-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-4 text-center md:text-left max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-400 text-xs font-semibold shadow-sm">
                    <i class="bi bi-stars"></i>
                    <span>Panel de Competición Élite</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-zinc-100">
                    ¡Bienvenido, <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-400 via-emerald-400 to-teal-400">{{ Auth::user()->name ?? 'Jugador' }}</span>!
                </h1>
                <p class="text-sm sm:text-base text-zinc-400 leading-relaxed font-normal">
                    Gestiona tus liguillas, arma tu once ideal y domina la clasificación general con análisis en tiempo real.
                </p>
            </div>

            <div class="shrink-0 flex items-center justify-center">
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-tr from-lime-400/20 to-emerald-400/20 rounded-3xl blur-2xl group-hover:scale-110 transition-transform"></div>
                    <img src="{{ asset('assets/media/logos/logo-fantasy.png') }}" alt="MiFantasy" class="relative w-28 h-28 sm:w-36 sm:h-36 rounded-3xl object-contain ring-1 ring-zinc-800/80 bg-zinc-950/80 p-3 shadow-2xl shadow-zinc-950">
                </div>
            </div>
        </div>
    </div>

    <!-- Bento Grid Acciones Rápidas / Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Card 1: Mis Liguillas -->
        <div class="group relative rounded-3xl bg-zinc-950/40 border border-zinc-800/80 hover:border-lime-400/40 p-6 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-lime-400/5 backdrop-blur-xl">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-lime-400/20 transition-all shadow-inner">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Mis Liguillas
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        Accede al listado completo de ligas donde compites, revisa tu puntuación y jornada actual.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/user/liguillas') }}" class="w-full py-3 px-4 bg-zinc-900/80 hover:bg-zinc-800 text-zinc-200 hover:text-lime-400 font-semibold text-xs rounded-2xl border border-zinc-800 hover:border-lime-400/30 transition-all flex items-center justify-center gap-2 group/btn">
                    <span>Ver mis liguillas</span>
                    <i class="bi bi-arrow-right text-sm group-hover/btn:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <!-- Card 2: Crear Liguilla -->
        <div class="group relative rounded-3xl bg-zinc-950/40 border border-zinc-800/80 hover:border-lime-400/40 p-6 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-lime-400/10 backdrop-blur-xl">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-lime-400/20 transition-all shadow-inner">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Crear Liguilla
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        Crea una competición privada, elige el torneo oficial y reta a tus amigos con tus normas.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/user/torneos') }}" class="w-full py-3 px-4 bg-lime-400 hover:bg-lime-300 text-zinc-950 font-bold text-xs rounded-2xl shadow-xl shadow-lime-400/20 hover:shadow-lime-400/30 transition-all flex items-center justify-center gap-2">
                    <span>Explorar torneos y crear</span>
                    <i class="bi bi-plus-lg text-sm"></i>
                </a>
            </div>
        </div>

        <!-- Card 3: Unirme a Liguilla -->
        <div class="group relative rounded-3xl bg-zinc-950/40 border border-zinc-800/80 hover:border-lime-400/40 p-6 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-lime-400/5 backdrop-blur-xl">
            <div class="space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-lime-400/10 border border-lime-400/20 text-lime-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-lime-400/20 transition-all shadow-inner">
                    <i class="bi bi-bookmark-plus"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">
                        Unirme a Liguilla
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-400 leading-relaxed">
                        Introduce tu código de invitación o nombre de liga para unirte de inmediato a la competición.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-zinc-800/80">
                <a href="{{ url('/user/unirseLiguilla') }}" class="w-full py-3 px-4 bg-zinc-900/80 hover:bg-zinc-800 text-zinc-200 hover:text-lime-400 font-semibold text-xs rounded-2xl border border-zinc-800 hover:border-lime-400/30 transition-all flex items-center justify-center gap-2 group/btn">
                    <span>Unirse con código</span>
                    <i class="bi bi-door-open text-sm group-hover/btn:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection