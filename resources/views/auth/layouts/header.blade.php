<!-- Header Autenticación -->
<header class="sticky top-0 z-50 w-full border-b border-zinc-800/80 bg-zinc-950/80 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-3 group transition-transform hover:scale-[1.02]">
            <img src="{{ asset('assets/media/logos/logo-fantasy.svg') }}" alt="MiFantasy Logo" class="h-9 w-auto">
            <span class="font-bold text-lg tracking-tight text-zinc-100 group-hover:text-lime-400 transition-colors">
                MiFantasy
            </span>
        </a>

        <div class="flex items-center gap-4">
            @auth
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-zinc-400">
                    {{ Auth::user()->name ?? Auth::user()->nombreUsuario }}
                </span>
                <form action="{{ url('/logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 border border-rose-500/20 rounded-lg transition-colors">
                        Cerrar sesión
                    </button>
                </form>
            </div>
            @else
            <a href="/" class="text-xs font-semibold text-zinc-400 hover:text-zinc-200 transition-colors flex items-center gap-1.5">
                <i class="bi bi-arrow-left"></i> Volver al inicio
            </a>
            @endauth
        </div>
    </div>
</header>